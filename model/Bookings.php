<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../classes/Date.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/History.php"));
require_once(REL(__FILE__, "../model/Collections.php"));
require_once(REL(__FILE__, "../model/Calendars.php"));
require_once(REL(__FILE__, "../model/MemberAccounts.php"));
require_once(REL(__FILE__, "../model/MediaTypes.php"));
require_once(REL(__FILE__, "../model/Members.php"));

/**
 * this class provides an interface to the booking table and related actions
 * @author Micah Stetson
 **/

class Bookings extends CoreTable {
	public function __construct() {
		parent::__construct();
		$this->setName('booking');
		$this->setFields(array(
			'bookingid'=>'number',
			'bibid'=>'number',
			'book_dt'=>'string',
			'due_dt'=>'string',
			'out_histid'=>'number',
			'out_dt'=>'string',
			'ret_histid'=>'number',
			'ret_dt'=>'string',
		));
		$this->setKey('bookingid');
        $this->setReq(array(
            'mbrids', 'bibid', 'book_dt', 'due_dt',
        ));
		$this->setSequenceField('bookingid');
		$this->setForeignKey('bibid', 'biblio', 'bibid');
		$this->setForeignKey('out_histid', 'biblio_status_hist', 'histid');
		$this->setForeignKey('ret_histid', 'biblio_status_hist', 'histid');
		$this->setIter('BookingsIter');
	}
	function getByHistid($histid) {
		$sql = $this->mkSQL("select * from booking "
			. "where out_histid=%N or ret_histid=%N ",
			$histid, $histid);
		return $this->select01($sql);
	}

	function getDaysLate($booking) {
		list($now, $err) = Date::read_e('now');
		if($err) {
			Fatal::internalError(T("Unexpected date error: ").$err->toStr());
		}
		return round(Date::daysLater($now, $booking['due_dt']));
	}

	/**
	 * This function is intended for displaying the number of copies
	 * booked for different days in a calendar form.  It returns an array
	 * of dates with the corresponding numbers of copies needed to fulfill
	 * the bookings on those dates.
	 */
	function getCalendarTotals($since, $before, $calendar=OBIB_MASTER_CALENDAR, $mbrid=NULL, $bibid=NULL) {
		$cal = new Calendars;
		$cal->extend($calendar, $since, $before);

		$sql = 'create temporary table bk1 type=heap '
			. 'select bk.bookingid, '
			. 'ifnull(bk.out_dt, bk.book_dt) as outd, '
			. 'if(bk.out_dt is null, bk.due_dt, '
			. 'ifnull(bk.ret_dt, greatest(bk.due_dt, now()))) as retd '
			. 'from booking bk ';
		if ($mbrid !== NULL) {
			$sql .= ', booking_member bkm ';
		}
		$sql .= $this->mkSQL('where ifnull(bk.out_dt, bk.book_dt) <= %Q '
			. 'and ifnull(bk.ret_dt, greatest(bk.due_dt, sysdate())) >= %Q ',
			$before, $since);
		if ($bibid !== NULL) {
			$sql .= $this->mkSQL('and bk.bibid=%N ', $bibid);
		}
		if ($mbrid !== NULL) {
			$sql .= $this->mkSQL('and bk.bookingid=bkm.bookingid '
				. 'and bkm.mbrid=%N ', $mbrid);
		}
		$this->act($sql);
		# MySQL doesn't support self joins on temp tables
		$this->act('create temporary table bk2 type=heap select * from bk1');
		$sql = 'create temporary table overlaps type=heap '
					 . 'select c.date, c.open, bk1.bookingid, '
					 . 'count(distinct bk2.bookingid) as noverlaps '
					 . 'from calendar c '
					 . 'left join bk1 on bk1.outd < c.date + interval 1 day '
					 . 'and bk1.retd >= c.date + interval 1 day '
					 . 'left join bk2 on bk2.outd < c.date + interval 1 day '
					 . 'and bk2.retd >= c.date + interval 1 day '
					 . 'and bk1.outd < bk2.retd '
					 . 'and bk2.outd < bk1.retd ';
		$sql .= $this->mkSQL('where c.calendar=%N '
			. 'and c.date >= %Q and c.date <= %Q ',
			$calendar, $since, $before);
		$sql .= 'group by c.date, bk1.bookingid ';
		$this->act($sql);
		return $this->select('select date, open, max(noverlaps) as ncopies '
			. 'from overlaps '
			. 'group by date order by date ');
	}
	protected function validate_el($new, $insert) {
        if ($insert) {
			$old = array();
		} else {
			$old = $this->getOne($new['bookingid']);
		}
		$booking = array_merge($old, $new);

		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);

		# Check that mbrids exist
		if (isset($new['mbrids'])) {
			foreach ($new['mbrids'] as $mbrid) {
				$sql = $this->mkSQL('select mbrid from member '
					. 'where mbrid=%N', $mbrid);
				if (!$this->select01($sql)) {
					$errors[] = new Error(T("modelBookingsMemberNoExist"));
				}
			}
		}

		if (isset($new['book_dt']) or isset($new['due_dt']) or isset($new['bibid'])) {
			# Check that due date is after out date
			if ($booking['due_dt'] <= $booking['book_dt']) {
				$errors[] = new Error(T("modelBookingsDueNotEarlier"));
			}
			if (empty($old) && $booking['book_dt'] < date('Y-m-d')) {
				$errors[] = new IgnorableFieldError('date', T("Date is in the past"));
			}

			# Get total number of copies
			$sql = $this->mkSQL('select count(*) as copies '
				. 'from biblio_copy where bibid=%N',
				$booking['bibid']);
			$row = $this->select1($sql);
			$ncopies = $row['copies'];

			# Check that copies exist
			if ($ncopies == 0) {
				$errors[] = new Error(T("modelBookingsNotEnoughCopies"));
			}

			if ($errors) {
				return $errors;
			}

			# Check that at least one copy is available
			$sql = 'select b1.bookingid, count(*) ncopies '
						 . 'from booking b1, booking b2 ';
			# Using to_days() ensures that a booking made for 2005-05-05
			# won't overlap with one that was returned 2005-05-05 12:35:42.
			# Can't use date() as that requires MySQL 4.1.1.
			$sql .= $this->mkSQL('where b1.bibid=%N '
				. 'and b2.bibid=b1.bibid '
				. 'and to_days(ifnull(b1.out_dt, b1.book_dt)) < to_days(%Q) '
				. 'and to_days(ifnull(b2.out_dt, b2.book_dt)) < to_days(%Q) '
				. 'and to_days(ifnull(b1.ret_dt, greatest(b1.due_dt, sysdate()))) '
				. ' > to_days(%Q) '
				. 'and to_days(ifnull(b2.ret_dt, greatest(b2.due_dt, sysdate()))) '
				. ' > to_days(%Q) '
				. 'and to_days(ifnull(b1.ret_dt, greatest(b1.due_dt, sysdate()))) '
				. ' > to_days(ifnull(b2.out_dt, b2.book_dt)) '
				. 'and to_days(ifnull(b1.out_dt, b1.book_dt)) '
				. ' < to_days(ifnull(b2.ret_dt, greatest(b2.due_dt, sysdate()))) ',
				$booking['bibid'], $booking['due_dt'], $booking['due_dt'],
				$booking['book_dt'], $booking['book_dt']);
			if (isset($booking['bookingid']) and $booking['bookingid'] !== NULL) {
				$sql .= $this->mkSQL('and b1.bookingid != %N and b2.bookingid != %N ',
				$booking['bookingid'], $booking['bookingid']);
			}
			$sql .= $this->mkSQL('group by b1.bookingid '
				. 'having ncopies >= %N ', $ncopies);
			$rows = $this->select($sql);
			if ($rows->num_rows != 0) {
				$errors[] = new Error(T("modelBookingsNotEnoughCopies"));
				return $errors;
			}
		}

		# FIXME - check that fulfilling this booking will not cause members
		# to exceed their checkout limits. (is done in verifyCheckout_e() - LJ)
		# FIXME - check that the item's collection's default days due back
		# field is nonzero, otherwise no checkouts are allowed. (is done in verifyCheckout_e() - LJ)

		if (isset($new['mbrids']) and !empty($booking['mbrids'])) {
			## determine if library is open today ##
			$sql = $this->mkSQL('select c.date, c.open '
				. 'from calendar c, member m, site s '
				. 'where c.calendar=s.calendar '
				. 'and s.siteid=m.siteid '
				. 'and c.open=\'No\' '
				. 'and c.date=%Q and m.mbrid in ',
				$booking['book_dt']);
			$mbrids = array();
			foreach ($booking['mbrids'] as $m) {
				$mbrids[] = $this->mkSQL('%N', $m);
			}
			$sql .= '('.implode(",", $mbrids).') ';
			$rows = $this->select($sql);
			if ($rows->num_rows != 0) {
				//$errors[] = new IgnorableError(T("modelBookingsClosedOnBookDate").": ".$booking['book_dt']);
				die (T("modelBookingsClosedToday"));
			}

			## determine if library is open on due date ##
			$sql = $this->mkSQL('select c.date, c.open '
				. 'from calendar c, member m, site s '
				. 'where c.calendar=s.calendar '
				. 'and s.siteid=m.siteid '
				. 'and c.open=\'No\' '
				. 'and c.date=%Q and m.mbrid in ',
				$booking['due_dt']);
			$sql .= '('.implode(",", $mbrids).') ';
			$rows = $this->select($sql);
			if ($rows->num_rows != 0) {
				$errors[] = new IgnorableError(T("modelBookingsClosedOnDueDate").": ".$booking['due_dt']);
			}
		}

		return $errors;
	}
	function _getMbrids($bookingid) {
		$sql = $this->mkSQL('SELECT * FROM booking_member '
			. 'WHERE bookingid=%N ', $bookingid);
		$rs = $this->select($sql);
		$mbrids = array();
		while ($r = $rs->fetch_assoc())
			$mbrids[] = $r['mbrid'];
		if (!in_array($_REQUEST['mbrid'],$mbrids)) $mbrids[] = $_REQUEST['mbrid'];
		return $mbrids;
	}
	function _putMbrids($bookingid, $mbrids) {
		$sql = $this->mkSQL('DELETE FROM booking_member '
			. 'WHERE bookingid=%N ', $bookingid);
		$this->act($sql);
		foreach ($mbrids as $mbrid) {
			$sql = $this->mkSQL('INSERT INTO booking_member '
				. 'SET bookingid=%N, mbrid=%N',
				$bookingid, $mbrid);
			$this->act($sql);
		}
	}

	function insert_el($rec, $confirmed=false) {
		$this->lock();
//echo"booking inset_el, rec====>";print_r($rec);echo"\n";
		list($id, $errs) = parent::insert_el($rec, $confirmed);
		if ($errs) {
			$this->unlock();
			return array(NULL, $errs);
		}
		$this->_putMbrids($id, $rec['mbrids']);
		$this->unlock();
		return array($id, NULL);
	}

	function update_el($rec, $confirmed=false) {
		$this->lock();
		$errs = parent::update_el($rec, $confirmed);
		if ($errs) {
			$this->unlock();
			return $errs;
		}
		if (isset($rec['mbrids'])) {
			$this->_putMbrids($rec['bookingid'], $rec['mbrids']);
		}
		$this->unlock();
		return NULL;
	}
	function deleteOne() {
		$bookingid = func_get_arg(0);
		$this->lock();
		# Older MySQL doesn't support DELETE using multiple tables.
		$sql = 'select s.histid, s.bibid, s.copyid, c.histid as curr_histid '
			. 'from booking b, biblio_status_hist s, biblio_copy c '
			. 'where (s.histid=b.out_histid or s.histid=b.ret_histid) '
			. 'and c.copyid=s.copyid '
			. $this->mkSQL(' and b.bookingid=%N ', $bookingid);
		$rows = $this->select($sql);
		if ($rows->num_rows != 0) {
			$ids = array();
			while ($r = $rows->fetch_assoc()) {
				if ($r['histid'] == $r['curr_histid']) {
					$history = new History;
					$history->insert(array(
						'bibid'=>$r['bibid'],
						'copyid'=>$r['copyid'],
						'status_cd'=>OBIB_STATUS_IN,
					));
				}
				$ids[] = $this->mkSQL('%N', $r['histid']);
			}
			$sql = 'delete from biblio_status_hist where histid in ( '
						 . implode(',', $ids).') ';
			$this->act($sql);
		}
		$sql = $this->mkSQL('DELETE FROM booking_member '
			. 'WHERE bookingid=%N ', $bookingid);
		$this->act($sql);
		parent::deleteOne($bookingid);
		$this->unlock();
	}
	function deleteMatches($fields) {
		$this->lock();
		$rows = $this->getMatches($fields);
		while ($r = $rows->fetch_assoc()) {
			$this->deleteOne($r['bookingid']);
		}
		$this->unlock();
	}
	/* Takes a bookingid, a copy barcode, and optionally a list of
	 * copyids already set to be checked out in this transaction,
	 * and verifies that the checkout wouldn't break the rules.
	 * Returns array($bibid, $copyid, $error)
	 * $bibid and $copyid are used to actually make the checkout.
	 * If the checkout should not be made, $error will contain an
	 * Error object indicating the reason.
	 */

	// LJ: IS THIS STILL USED??? NOT SURE!

	function verifyCheckout_e($bookingid, $barcode, $out_copyids=array()) {
		$this->lock();
		do {
			if (!$barcode) {
				$err = new Error(T("No barcode set."));
				break;
			}
			$copies = new Copies;
			$copy = $copies->getByBarcode($barcode);
			if (!$copy) {
				$err = new Error(T("No copy with barcode").' '.$barcode);
				break;
			}

			$booking = $this->getOne($bookingid);
			if ($copy['bibid'] != $booking['bibid']) {
				$err = new Error(T("modelBookingsBarcodeNoMatch"));
				break;
			}
			if (!empty($booking['out_histid'])) {
				$err = new Error(T("modelBookingsAlreadyCheckedOut"));
				break;
			}
			$booking['mbrids'] = $this->_getMbrids($booking['bibid']);

//			$history = new History;
//			$status = $history->getOne($copy['histid']);
//			$status = $history->maybeGetOne($this->histid);
//			if ($status['status_cd'] == OBIB_STATUS_OUT) {
			if ($this->statusCd == OBIB_STATUS_OUT) {
				$err = new Error(T("modelBookingsCopyUnavailable", array('barcode'=>$barcode)));
				break;
			}
			if (in_array($this->copyid, $out_copyids)) {
				$err = new Error(T("modelBookingsSetForOtherBooking", array('barcode'=>$barcode)));
				break;
			}

			if (Settings::get('block_checkouts_when_fines_due')) {
				$all_fined = true;
				$acct = new MemberAccounts;
				foreach ($booking['mbrids'] as $mbrid) {
					$balance = $acct->getBalance($mbrid);
					if ($balance <= 0) {
						$all_fined = false;
						break;
					}
				}
				if ($all_fined) {
					$err = new Error((T("modelBookingsPayFinesFirst")." $balance"));
					break;
				}
			}

			# Check if collection allows checkout - added here as it seem the right place - LJ
			$collections = new Collections;
			$coll = $collections->getByBibid($this->bibid);
			if ($coll['days_due_back'] <= 0) {
				$err = new Error(T("modelBookingsNotAvailable"));
				break;
			}

			# Check wherether the user can take out more books (not sure if I understand this) - LJ
			$MediaTypes = new MediaTypes();
			$material = $MediaTypes->getByBibid($this->bibid);
			$copies = new Copies;
			$acct = new MemberAccounts;
			$members = new Members();
			$checkouts = 0;
			foreach ($booking['mbrids'] as $mbrid) {
				$checkouts .= $copies->getMemberCheckouts($mbrid)->nmbr_rows;
				# Also get if an adult (1) or juvenile (2). Not sure why there can be more members, and assume it will only be one, 
				# but will take adult as overruling if both are given - LJ
				$mbr = $members->maybeGetOne($mbrid);
				if($memberType != 1) $memberType = $mbr['classification'];			
			}
			if($memberType = 1){
				$limit = $material['adult_checkout_limit'];
			} else {
				$limit = $material['juvenile_checkout_limit'];
			}
			if($limit <= $checkouts){
				$err = new Error(T("Member has exceeded %number% items", array('number'=>$limit)));
				break;
			}
		} while (0);
		$this->unlock();
		if ($err) {
			return array(NULL, NULL, $err);
		} else {
			return array($this->bibid, $this->copyid, NULL);
		}
	}

	function checkout_e($bookingid, $barcode) {
		$this->lock();
		list($bibid, $copyid, $error) = $this->verifyCheckout_e($bookingid, $barcode);
		if ($error) {
			$this->unlock();
			return $error;
		}
		$history = new History;
		$history->insert(array(
			'histid'=>$this->histid,
			'bibid'=>$this->bibid,
			'copyid'=>$this->copyid,
			'status_cd'=>OBIB_STATUS_OUT,
		));
		$this->unlock();
		return NULL;
	}

	function checkoutBatch_el($checkouts) {
		$this->lock();
		$bibids = array();
		$copyids = array();
		$errors = array();
		foreach ($checkouts as $bookingid => $barcode) {
			list($bibid, $copyid, $error)
				= $this->verifyCheckout_e($bookingid, $barcode, $copyids);
			if ($error) {
				$errors[] = new FieldError($bookingid, $error->toStr());
			} else {
				$bibids[$bookingid] = $bibid;
				$copyids[$bookingid] = $copyid;
			}
		}
		if (!empty($errors)) {
			$this->unlock();
			return $errors;
		}
		$history = new History;
		foreach ($checkouts as $bookingid => $barcode) {
			$history->insert(array(
				'bibid'=>$bibids[$bookingid],
				'copyid'=>$copyids[$bookingid],
				'status_cd'=>OBIB_STATUS_OUT,
				'bookingid'=>$bookingid,
			));
		}
		$this->unlock();
		return array();
	}

	function quickCheckout_e($barcode, $calCd=1, $mbrids) {
 		$this->lock();
		$copies = new Copies;
		$copy = $copies->getByBarcode($barcode);
		if (!$copy) {
			$this->unlock();
			return T("No copy with barcode")." ".$barcode;
		}
		$bibid = $copy["bibid"];
		$copyid = $copy["copyid"];
		$histid = $copy["histid"];

		$history = new History;
		$status = $history->maybeGetOne($histid);
		if ($status == NULL) {
			## no entry found, need an initial entry for reports
			$status['status_cd'] = OBIB_DEFAULT_STATUS;
			$rslt = $history->insert(array(
				'bibid'=>$bibid,
				'copyid'=>$copyid,
				'status_cd'=>$status['status_cd'],
			));
        } else if($status['status_cd'] == OBIB_STATUS_OUT){
            return new Error(T("modelBookingsAlreadyCheckedOut"));
		} else if($status['status_cd'] == OBIB_STATUS_NOT_ON_LOAN){
			return new Error(T("modelBookingsNotOnLoan", array("barcode"=>$barcode)));
		} else if ($status['status_cd'] == OBIB_STATUS_ON_HOLD) {
			include_once(REL(__FILE__, "../model/Holds.php"));
			$holds = new Holds;
			if ($hold = $holds->getFirstHold($copyid)) {
				if (!in_array($hold['mbrid'], $mbrids)) {
					$this->unlock();
					return new Error(T("modelBookingsHeldForOtherMember", array("barcode"=>$barcode)));
				} else {
					$holds->deleteOne($hold['holdid']);
				}
			}
        } else {
            if ($status['status_cd'] != OBIB_STATUS_IN && $status['status_cd'] != OBIB_STATUS_SHELVING_CART) {
                // LJ: The above seemed incomplete (and the check functions are not called anymore, e.g. verifyCheckout_e). Somebody please verify
                return new Error(T("modelBookingsNotAvailable", array("barcode"=>$barcode)));
            }
        }

		$collections = new Collections;
		$coll = $collections->getByBibid($bibid);
		$loanDuration = $coll['days_due_back'];
		if ($loanDuration <= 0) {
			$this->unlock();
			return new Error(T("modelBookingsNotAvailable", array("barcode"=>$barcode)));
		}

		## get current date
		list($today, $err) = Date::read_e('now');
		if ($err) {
			Fatal::internalError(T("Unexpected date error: ").$err->toStr());
		}
		$this->outDate = $today;

		## assure potential due date is a 'library open' day
		$due_dt = Date::addDays($this->outDate, $loanDuration);
		$cal = new Calendars;
		$isOpen = $cal->isOpen($calCd, $due_dt);
  		if (!$isOpen) {
			do {
				$due_dt = Date::addDays($due_dt, 1); // advance to next day
				$isOpen = $cal->isOpen($calCd, $due_dt);
			} while (!$isOpen);
		}
//    	$this->due_dt = $due_dt;

		## all OK, begin DB update for checkout
//echo "mbrids===>";print_r($mbrids);echo"<br />\n";
		list($this->bookingid, $err) = $this->insert_el(
					array("book_dt" =>$today,
								"bibid" =>$bibid,
								"out_dt" =>$this->outDate,
								"out_histid" =>$histid,
								"due_dt" =>$due_dt,
								"mbrids" =>$mbrids,
		));
		if ($err) {
			$this->unlock();
			return $err;
		}
		$this->statusCd = OBIB_STATUS_OUT;
		list($this->histid, $err) = $history->insert_el(array(
			'bibid'=>$bibid,
			'copyid'=>$copyid,
			'status_cd'=>$this->statusCd,
			'bookingid'=>$this->bookingid,
		));
		if ($err) {
			$this->unlock();
            //echo "Error: " . $err . "/n";
			return $err;
		}

		$copies = new Copies;
		$copies->update(array(
			'copyid'=>$copyid,
			'histid'=>$this->histid,
		));

		$this->update(array(
			"bookingid"=>$this->bookingid,
			"out_histid" =>$this->histid,
			"out_dt" =>$this->outDate,
      		"mbrids" =>$mbrids,
		));

	//	$err = $this->checkout_e($bookingid, $barcode);
	//	if ($err) {
	//		$this->deleteOne($bookingid);
	//		$this->unlock();
	//		return $err;
	//	}

		$this->unlock();
		return NULL;
	}
	function removeMember($bookingid, $mbrid) {
		$this->lock();
		$b = $this->getOne($bookingid);
		$idx = array_search($mbrid, $b['mbrids']);
		if ($idx === false) {
			$this->unlock();
			return;
		}
		unset($b['mbrids'][$idx]);
		if (empty($b['mbrids'])) {
			$this->deleteOne($bookingid);
		} else {
			$this->update($b, true);
		}
		$this->unlock();
	}
}

class BookingsIter extends Iter {
	public function __construct($rows) {
		parent::__construct();
		$this->rows = $rows;
		$this->db = new Queryi;
	}
	function next() {
		$row = $this->rows->fetch_assoc();
		if (!$row)
			return NULL;
		$sql = $this->mkSQL('SELECT * FROM booking_member '
			. 'WHERE bookingid=%N ', $row['bookingid']);
		$rs = $this->select($sql);
		$row['mbrids'] = array();
		while ($r = $rs->fetch_assoc())
			$row['mbrids'][] = $r['mbrid'];
		return $row;
	}
	function skip() {
		$this->rows->skip();
	}
	function count() {
		return $this->rows->count();
	}
}
