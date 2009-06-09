<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Query.php"));

class Integrity {
	function Integrity() {
		$this->db = new Query;
		$this->checks = array(
			array(
				'error' => T("%count% unattached MARC fields"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_field left join biblio '
					. 'on biblio_field.bibid=biblio.bibid '
					. 'where biblio.bibid is null ',
				'fixSql' => 'delete from biblio_field '
					. 'using biblio_field left join biblio '
					. 'on biblio.bibid=biblio_field.bibid '
					. 'where biblio.bibid is null ',
			),
			array(
				'error' => T("%count% unattached MARC subfields"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_subfield left join biblio_field '
					. 'on biblio_subfield.fieldid=biblio_field.fieldid '
					. 'where biblio_field.fieldid is null ',
				'fixSql' => 'delete from biblio_subfield '
					. 'using biblio_subfield left join biblio_field '
					. 'on biblio_subfield.fieldid=biblio_field.fieldid '
					. 'where biblio_field.fieldid is null ',
			),
			array(
				'error' => T("%count% unattached images"),
				'countSql' => 'select count(*) as count '
					. 'from images left join biblio '
					. 'on biblio.bibid=images.bibid '
					. 'where biblio.bibid is null ',
				'fixSql' => 'delete from images '
					. 'using images left join biblio '
					. 'on biblio.bibid=images.bibid '
					. 'where biblio.bibid is null ',
			),
			array(
				'error' => T("%count% unattached copies"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_copy left join biblio '
					. 'on biblio.bibid=biblio_copy.bibid '
					. 'where biblio.bibid is null ',
				'fixSql' => 'delete from biblio_copy '
					. 'using biblio_copy left join biblio '
					. 'on biblio.bibid=biblio_copy.bibid '
					. 'where biblio.bibid is null ',
			),
			array(
				'error' => T("%count% copies with broken status references"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_copy left join biblio_status_hist '
					. 'on biblio_status_hist.histid=biblio_copy.histid '
					. 'where biblio_status_hist.histid is null ',
				// FIXME - Check in copies
			),
			array(
				'error' => T("%count% unattached copy status history records"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join biblio_copy '
					. 'on biblio_copy.copyid=biblio_status_hist.copyid '
					. 'where biblio_copy.copyid is null ',
				'fixSql' => 'delete from biblio_status_hist '
					. 'using biblio_status_hist left join biblio_copy '
					. 'on biblio_copy.copyid=biblio_status_hist.copyid '
					. 'where biblio_copy.copyid is null ',
			),
			array(
				'error' => T('IntegrityQueryInvalidStatusCodes'),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join biblio_status_dm '
					. 'on biblio_status_dm.code=biblio_status_hist.status_cd '
					. 'where biblio_status_dm.code is null ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T('IntegrityQueryBrokenBibidRef'),
				'countSql' => 'select count(*) as count '
					. 'from booking left join biblio '
					. 'on biblio.bibid=booking.bibid '
					. 'where biblio.bibid is null ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T('IntegrityQueryBrokenOutRef'),
				'countSql' => 'select count(*) as count '
					. 'from booking left join biblio_status_hist '
					. 'on biblio_status_hist.histid=booking.out_histid '
					. 'where booking.out_histid is not null '
					. 'and biblio_status_hist.histid is null ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T('IntegrityQueryBrokenReturnRef'),
				'countSql' => 'select count(*) as count '
					. 'from booking left join biblio_status_hist '
					. 'on biblio_status_hist.histid=booking.ret_histid '
					. 'where booking.ret_histid is not null '
					. 'and biblio_status_hist.histid is null ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T('IntegrityQueryNoAssBooking'),
				'countSql' => 'select count(*) as count '
					. 'from booking_member left join booking '
					. 'on booking.bookingid=booking_member.bookingid '
					. 'where booking.bookingid is null ',
				'fixSql' => 'delete from booking_member '
					. 'using booking_member left join booking '
					. 'on booking.bookingid=booking_member.bookingid '
					. 'where booking.bookingid is null ',
			),
			array(
				'error' => T('IntegrityQueryNoAssMember'),
				'countSql' => 'select count(*) as count '
					. 'from booking_member left join member '
					. 'on member.mbrid=booking_member.mbrid '
					. 'where member.mbrid is null ',
				'fixSql' => 'delete from booking_member '
					. 'using booking_member left join member '
					. 'on member.mbrid=booking_member.mbrid '
					. 'where member.mbrid is null ',
			),
			array(
				'error' => T("%count% members without sites"),
				'countSql' => 'select count(*) as count '
					. 'from member left join site '
					. 'on site.siteid=member.siteid '
					. 'where site.siteid is null ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T('IntegrityQueryUnattachedAccTrans'),
				'countSql' => 'select count(*) as count '
					. 'from member_account left join member '
					. 'on member.mbrid=member_account.mbrid '
					. 'where member.mbrid is null ',
				'fixSql' => 'delete from member_account '
					. 'using member_account left join member '
					. 'on member.mbrid=member_account.mbrid '
					. 'where member.mbrid is null ',
			),
			array(
				'error' => T('IntegrityQueryChangedCopyStatus'),
				'countSql' => 'select count(*) as count '
					. 'from booking b, biblio_status_hist h, biblio_copy c '
					. 'where b.out_histid is not null and b.ret_histid is null '
					. 'and h.histid=b.out_histid and c.copyid=h.copyid '
					. 'and c.histid != b.out_histid ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T('IntegrityQueryOutRecNoBooking'),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join booking '
					. 'on booking.out_histid=biblio_status_hist.histid '
					. 'where biblio_status_hist.status_cd=\'out\' '
					. 'and booking.bookingid is null ',
				// NO AUTOMATIC FIX
			),
			array(
				'error' => T("%count% double check outs"),
				'countFn' => 'countDoubleCheckouts',
				// NO AUTOMATIC FIX
			),
		);
	}
	function check_el($fix=false) {
		$errors = array();
		foreach ($this->checks as $chk) {
			assert('isset($chk["error"])');
			if (isset($chk['countSql'])) {
				$row = $this->db->select1($chk['countSql']);
				$count = $row["count"];
			} elseif (isset($chk['countFn'])) {
				$fn = $chk['countFn'];
				assert('method_exists($this, $fn)');
				$count = $this->$fn();
			} else {
				assert('NULL');
			}
			if ($count) {
				$msg = $this->_loc->getText($chk["error"], array('count'=>$count));
				if ($fix) {
					if (isset($chk['fixSql'])) {
						$this->db->act($chk['fixSql']);
						$msg .= ' <b>'.T("FIXED").'</b> ';
					} elseif (isset($chk['fixFn'])) {
						$fn = $chk['fixFn'];
						assert('method_exists($this, $fn)');
						$error = $this->$fn();
						if ($error) {
							$msg .= ' <b>'.T("CAN'T FIX:").'</b> '.$error->toStr();
						} else {
							$msg .= ' <b>'.T("FIXED").'</b> ';
						}
					} else {
						$msg .= ' <b>'.T("CANNOT BE FIXED AUTOMATICALLY").'</b>';
					}
				}
				$errors[] = new Error($msg);
			}
		}
		return $errors;
	}
	/* Count and fix functions below */
	function countDoubleCheckouts() {
		$sql = 'select histid, copyid, status_cd from biblio_status_hist order by histid ';
		$status = array();
		$errors = 0;
		$r = $this->db->select($sql);
		while ($row = $r->next()) {
			if ($row['status_cd'] == 'out' and isset($status[$row['copyid']])) {
				if ($status[$row['copyid']]['status_cd'] == 'out') {
					$errors++;
				}
			}
			$status[$row['copyid']] = $row;
		}
		return $errors;
	}
}
