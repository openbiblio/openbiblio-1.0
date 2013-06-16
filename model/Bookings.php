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

class Bookings extends CoreTable {
	function Bookings() {
		$this->CoreTable();
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
		$this->setSequenceField('bookingid');
		$this->setForeignKey('bibid', 'biblio', 'bibid');
		$this->setForeignKey('out_histid', 'biblio_status_hist', 'histid');
		$this->setForeignKey('ret_histid', 'biblio_status_hist', 'histid');
		$this->setIter('BookingsIter');
	}
	function getByHistid($histid) {
		$sql = $this->db->mkSQL("select * from booking "
			. "where out_histid=%N or ret_histid=%N ",
			$histid, $histid);
		return $this->db->select01($sql);
	}

	function getDaysLate($booking) {
		list($now, $err) = Date::read_e('now');
		if($err) {
			Fatal::internalError(T("Unexpected date error: ").$err->toStr());
		}
		return round(Date::daysLater($now, $booking['due_dt']));
	}

	/* This function is intended for displaying the number of copies
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
		$sql .= $this->db->mkSQL('where ifnull(bk.out_dt, bk.book_dt) <= %Q '
			. 'and ifnull(bk.ret_dt, greatest(bk.due_dt, sysdate())) >= %Q ',
			$before, $since);
		if ($bibid !== NULL) {
			$sql .= $this->db->mkSQL('and bk.bibid=%N ', $bibid);
		}
		if ($mbrid !== NULL) {
			$sql .= $this->db->mkSQL('and bk.bookingid=bkm.bookingid '
				. 'and bkm.mbrid=%N ', $mbrid);
		}
		$this->db->act($sql);
		# MySQL doesn't support self joins on temp tables
		$this->db->act('create temporary table bk2 type=heap select * from bk1');
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
		$sql .= $this->db->mkSQL('where c.calendar=%N '
			. 'and c.date >= %Q and c.date <= %Q ',
			$calendar, $since, $before);
		$sql .= 'group by c.date, bk1.bookingid ';
		$this->db->act($sql);
		return $this->db->select('select date, open, max(noverlaps) as ncopies '
			. 'from overlaps '
			. 'group by date order by date ');
	}
	function validate_el($new, $insert) {
		$modelBookingsNotEnoughCopiesText = T("modelBookingsNotEnoughCopies");
		
		if ($insert) {
			$old = array();
		} else {
			$old = $this->getOne($new['bookingid']);
		}
		$booking = array_merge($old, $new);

		$errors = array();
		foreach (array('mbrids', 'bibid', 'book_dt', 'due_dt') as $req) {
			if (!isset($booking[$req])
					or isset($booking[$req]) and $booking[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}

		# Check that mbrids exist
		if (isset($new['mbrids'])) {
			foreach ($new['mbrids'] as $mbrid) {
				$sql = $this->db->mkSQL('select mbrid from member '
					. 'where mbrid=%N', $mbrid);
				if (!$this->db->select01($sql)) {
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
			$sql = $this->db->mkSQL('select count(*) as copies '
				. 'from biblio_copy where bibid=%N',
				$booking['bibid']);
			$row = $this->db->select1($sql);
			$ncopies = $row['copies'];

			# Check that copies exist
			if ($ncopies == 0) {
				$errors[] = new Error($modelBookingsNotEnoughCopiesText);
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
			$sql .= $this->db->mkSQL('where b1.bibid=%N '
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
				$sql .= $this->db->mkSQL('and b1.bookingid != %N and b2.bookingid != %N ',
				$booking['bookingid'], $booking['bookingid']);
			}
			$sql .= $this->db->mkSQL('group by b1.bookingid '
				. 'having ncopies >= %N ', $ncopies);
			$rows = $this->db->select($sql);
			if ($rows->count() != 0) {
				$errors[] = new Error($modelBookingsNotEnoughCopiesText);
				return $errors;
			}
		}

		# FIXME - check that fulfilling this booking will not cause members
		# to exceed their checkout limits. (is done in verifyCheckout_e() - LJ)
		# FIXME - check that the item's collection's default days due back
		# field is nonzero, otherwise no checkouts are allowed. (is done in verifyCheckout_e() - LJ)

		if (isset($new['mbrids']) and !empty($booking['mbrids'])) {
			$sql = $this->db->mkSQL('select c.date, c.open '
				. 'from calendar c, member m, site s '
				. 'where c.calendar=s.calendar '
				. 'and s.siteid=m.siteid '
				. 'and c.open=\'No\' '
				. 'and c.date=%Q and m.mbrid in ',
				$booking['book_dt']);
			$mbrids = array();
			foreach ($booking['mbrids'] as $m) {
				$mbrids[] = $this->db->mkSQL('%N', $m);
			}
			$sql .= '('.implode(",", $mbrids).') ';
			$rows = $this->db->select($sql);
			if ($rows->count() != 0) {
				$errors[] = new IgnorableError(T("modelBookingsClosedOnBookDate"));
			}
			$sql = $this->db->mkSQL('select c.date, c.open '
				. 'from calendar c, member m, site s '
				. 'where c.calendar=s.calendar '
				. 'and s.siteid=m.siteid '
				. 'and c.open=\'No\' '
				. 'and c.date=%Q and m.mbrid in ',
				$booking['due_dt']);
			$sql .= '('.implode(",", $mbrids).') ';
			$rows = $this->db->select($sql);
			if ($rows->count() != 0) {
				$errors[] = new IgnorableError(T("modelBookingsClosedOnDueDate"));
			}
		}

		return $errors;
	}
	function _putMbrids($bookingid, $mbrids) {
		$sql = $this->db->mkSQL('DELETE FROM booking_member '
			. 'WHERE bookingid=%N ', $bookingid);
		$this->db->act($sql);
		foreach ($mbrids as $mbrid) {
			$sql = $this->db->mkSQL('INSERT INTO booking_member '
				. 'SET bookingid=%N, mbrid=%N',
				$bookingid, $mbrid);
			$this->db->act($sql);
		}
	}
	function insert_el($rec, $confirmed=false) {
		$this->db->lock();
		list($id, $errs) = parent::insert_el($rec, $confirmed);
		if ($errs) {
			$this->db->unlock();
			return array(NULL, $errs);
		}
		$this->_putMbrids($id, $rec['mbrids']);
		$this->db->unlock();
		return array($id, NULL);
	}
	function update_el($rec, $confirmed=false) {
		$this->db->lock();
		$errs = parent::update_el($rec, $confirmed);
		if ($errs) {
			$this->db->unlock();
			return $errs;
		}
		if (isset($rec['mbrids'])) {
			$this->_putMbrids($rec['bookingid'], $rec['mbrids']);
		}
		$this->db->unlock();
		return NULL;
	}
	function deleteOne($bookingid) {
		$this->db->lock();
		# Older MySQL doesn't support DELETE using multiple tables.
		$sql = 'select s.histid, s.bibid, s.copyid, c.histid as curr_histid '
			. 'from booking b, biblio_status_hist s, biblio_copy c '
			. 'where (s.histid=b.out_histid or s.histid=b.ret_histid) '
			. 'and c.copyid=s.copyid '
			. $this->db->mkSQL(' and b.bookingid=%N ', $bookingid);
		$rows = $this->db->select($sql);
		if ($rows->count() != 0) {
			$ids = array();
			while ($r = $rows->next()) {
				if ($r['histid'] == $r['curr_histid']) {
					$history = new History;
					$history->insert(array(
						'bibid'=>$r['bibid'],
						'copyid'=>$r['copyid'],
						'status_cd'=>OBIB_STATUS_IN,
					));
				}
				$ids[] = $this->db->mkSQL('%N', $r['histid']);
			}
			$sql = 'delete from biblio_status_hist where histid in ( '
						 . implode(',', $ids).') ';
			$this->db->act($sql);
		}
		$sql = $this->db->mkSQL('DELETE FROM booking_member '
			. 'WHERE bookingid=%N ', $bookingid);
		$this->db->act($sql);
		parent::deleteOne($bookingid);
		$this->db->unlock();
	}
	function deleteMatches($fields) {
		$this->db->lock();
		$rows = $this->getMatches($fields);
		while ($r = $rows->next()) {
			$this->deleteOne($r['bookingid']);
		}
		$this->db->unlock();
	}
	/* Takes a bookingid, a copy barcode, and optionally a list of
	 * copyids already set to be checked out in this transaction,
	 * and verifies that the checkout wouldn't break the rules.
	 * Returns array($bibid, $copyid, $error)
	 * $bibid and $copyid are used to actually make the checkout.
	 * If the checkout should not be made, $error will contain an
	 * Error object indicating the reason.
	 */
	function verifyCheckout_e($bookingid, $barcode, $out_copyids=array()) {
		$this->db->lock();
		do {
			if (!$barcode) {
				$err = new Error(T("No barcode set."));
				break;
			}
			$copies = new Copies;
			$copy = $copies->getByBarcode($barcode);
			if (!$copy) {
				$err = new Error(T("No copy with barcode %barcode%", array('barcode'=>$barcode)));
				break;
			}
			$booking = $this->getOne($bookingid);
			if ($copy['bibid'] != $booking['bibid']) {
				$err = new Error(T("modelBookingsBarcodeNoMatch"));
				break;
			}
			if ($booking['out_histid']) {
				$err = new Error(T("modelBookingsAlreadyCheckedOut"));
				break;
			}
			$history = new History;
			$status = $history->getOne($copy['histid']);
			if ($status['status_cd'] == OBIB_STATUS_OUT) {
				$err = new Error(T("modelBookingsCopyUnavailable", array('barcode'=>$barcode)));
				break;
			}
			if (in_array($copy['copyid'], $out_copyids)) {
				$err = new Error(T("modelBookingsSetForOtherBooking", array('barcode'=>$barcode)));
				break;
			}
			if (Settings::get('block_checkouts_when_fines_due')) {
				$all_fined = true;
				$acct = new MemberAccounts;
				foreach ($booking['mbrids'] as $mbrid) {
					if ($acct->getBalance($mbrid) == 0) {
						$all_fined = false;
						break;
					}
				}
				if ($all_fined) {
					$err = new Error(T("modelBookingsPayFinesFirst"));
					break;
				}
			}
			# Check if collection allows checkout - added here as it seem the right place - LJ
			$collections = new Collections;
			$coll = $collections->getByBibid($copy['bibid']);
			if ($coll['days_due_back'] <= 0) {
				$err = new Error(T("modelBookingsNotAvailable"));
				break;
			}
			# Check wherether the user can take out more books (not sure if I understand this) - LJ
			$MediaTypes = new MediaTypes();
			$material = $MediaTypes->getByBibid($copy['bibid']);
			$copies = new Copies;
			$acct = new MemberAccounts;
			$members = new Members();
			$checkouts = 0;
			foreach ($booking['mbrids'] as $mbrid) {
				$checkouts .= $copies->getMemberCheckouts($mbrid)->count();
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
		$this->db->unlock();
		if ($err) {
			return array(NULL, NULL, $err);
		} else {
			return array($copy['bibid'], $copy['copyid'], NULL);
		}
	}

	function checkout_e($bookingid, $barcode) {
		$this->db->lock();
		list($bibid, $copyid, $error) = $this->verifyCheckout_e($bookingid, $barcode);
		if ($error) {
			$this->db->unlock();
			return $error;
		}
		$history = new History;
		$history->insert(array(
			'bibid'=>$bibid,
			'copyid'=>$copyid,
			'status_cd'=>OBIB_STATUS_OUT,
			'bookingid'=>$bookingid,
		));
		$this->db->unlock();
		return NULL;
	}

	function checkoutBatch_el($checkouts) {
		$this->db->lock();
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
			$this->db->unlock();
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
		$this->db->unlock();
		return array();
	}

	function quickCheckout_e($barcode, $calCd=1, $mbrids) {
		$this->db->lock();
		$copies = new Copies;
		$copy = $copies->getByBarcode($barcode);
		if (!$copy) {
			$this->db->unlock();
			//return new Error(T("No copy with barcode %barcode%", array('barcode'=>$barcode)));
			//return new Error($errMsg);
			return T("No copy with barcode %barcode%", array('barcode'=>$barcode));
		}
		$history = new History;
		$status = $history->getOne($copy['histid']);
		if($status['status_cd'] == OBIB_STATUS_NOT_ON_LOAN){
			return new Error(T("modelBookingsNotOnLoan", array("barcode"=>$barcode)));
		}
		if ($status['status_cd'] == OBIB_STATUS_ON_HOLD) {
			include_once(REL(__FILE__, "../model/Holds.php"));
			$holds = new Holds;
			if ($hold = $holds->getFirstHold($copy['copyid'])) {
				if (!in_array($hold['mbrid'], $mbrids)) {
					$this->db->unlock();
					return new Error(T("modelBookingsHeldForOtherMember", array("barcode"=>$barcode)));
				} else {
					$holds->deleteOne($hold['holdid']);
				}
			}
		}

		$collections = new Collections;
		$coll = $collections->getByBibid($copy['bibid']);
		$daysDueBack = $coll['days_due_back'];
		if ($daysDueBack <= 0) {
			$this->db->unlock();
			return new Error(T("modelBookingsNotAvailable", array("barcode"=>$barcode)));
		}
		list($book_dt, $err) = Date::read_e('now');
		if ($err) {
			Fatal::internalError(T("Unexpected date error: ").$err->toStr());
		}

		## assure due date is a 'library open' day
		$cal = new Calendars;
		$due_dt = Date::addDays($book_dt, $daysDueBack);
		$row = $cal->isOpen($calCd, $due_dt);
		$status = $row['open'];
  	if ($status == 'No') {
			do {
				$due_dt = Date::addDays($due_dt, 1); // advance to next day
				$row = $cal->isOpen($calCd, $due_dt);
				$status = $row['open'];
			} while ($status == 'No');
		}

		$booking = new Bookings($copy['bibid'], $book_dt, $due_dt, $mbrids);
		list($bookingid, $err) = $this->insert_el(array("book_dt" => $book_dt, "bibid" => $copy['bibid'], "bidid2" => $bidid, "due_dt" => $due_dt, "mbrids" => $mbrids));
		if ($err) {
			$this->db->unlock();
			return $err;
		}
		$err = $this->checkout_e($bookingid, $barcode);
		if ($err) {
			$this->deleteOne($bookingid);
			$this->db->unlock();
			return $err;
		}
		$this->db->unlock();
		return NULL;
	}
	function removeMember($bookingid, $mbrid) {
		$this->db->lock();
		$b = $this->getOne($bookingid);
		$idx = array_search($mbrid, $b['mbrids']);
		if ($idx === false) {
			$this->db->unlock();
			return;
		}
		unset($b['mbrids'][$idx]);
		if (empty($b['mbrids'])) {
			$this->deleteOne($bookingid);
		} else {
			$this->update($b, true);
		}
		$this->db->unlock();
	}
}

class BookingsIter extends Iter {
	function BookingsIter($rows) {
		$this->rows = $rows;
		$this->db = new Query;
	}
	function next() {
		$row = $this->rows->next();
		if (!$row)
			return NULL;
		$sql = $this->db->mkSQL('SELECT * FROM booking_member '
			. 'WHERE bookingid=%N ', $row['bookingid']);
		$rs = $this->db->select($sql);
		$row['mbrids'] = array();
		while ($r = $rs->next())
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
