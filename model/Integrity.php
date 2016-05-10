<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Queryi.php"));

class Integrity extends Queryi{
	private $checks= array();
	public function __construct() {
		parent::__construct();
			$this->checks[] = array(
				//'error' => T("%count% unattached MARC fields"),
				'error' => T("unattached MARC fields"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_field left join biblio '
					. 'on biblio_field.bibid=biblio.bibid '
					. 'where biblio.bibid is null ',
				'fixSql' => 'delete from biblio_field '
					. 'using biblio_field left join biblio '
					. 'on biblio.bibid=biblio_field.bibid '
					. 'where biblio.bibid is null ',
			);
			$this->checks[] = array(
				//'error' => T("%count% unattached MARC subfields"),
				'error' => T("unattached MARC subfields"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_subfield left join biblio_field '
					. 'on biblio_subfield.fieldid=biblio_field.fieldid '
					. 'where biblio_field.fieldid is null ',
				'fixSql' => 'delete from biblio_subfield '
					. 'using biblio_subfield left join biblio_field '
					. 'on biblio_subfield.fieldid=biblio_field.fieldid '
					. 'where biblio_field.fieldid is null ',
			);
			$this->checks[] = array(
				//'error' => T("%count% unattached images"),
				'error' => T("unattached images"),
				'countSql' => 'select count(*) as count '
					. 'from images left join biblio '
					. 'on biblio.bibid=images.bibid '
					. 'where biblio.bibid is null ',
				'fixSql' => 'delete from images '
					. 'using images left join biblio '
					. 'on biblio.bibid=images.bibid '
					. 'where biblio.bibid is null ',
			);
			$this->checks[] = array(
				//'error' => T("%count% unattached copies"),
				'error' => T("unattached copies"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_copy left join biblio '
					. 'on biblio.bibid=biblio_copy.bibid '
					. 'where biblio.bibid is null ',
				'fixSql' => 'delete from biblio_copy '
					. 'using biblio_copy left join biblio '
					. 'on biblio.bibid=biblio_copy.bibid '
					. 'where biblio.bibid is null ',
			);
			$this->checks[] = array(
				//'error' => T("%count% copies with broken status references"),
				'error' => T("copies with broken status references"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_copy left join biblio_status_hist '
					. 'on biblio_status_hist.histid=biblio_copy.histid '
					. 'where biblio_status_hist.histid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => "insert into biblio_status_hist "
					. "(bibid, copyid, status_cd) "
					. "select c.bibid, c.copyid, 'in' from biblio_copy c "
					. "where c.copyid not in (select h.copyid from biblio_status_hist h)",
				*/
			);
			
/*			
			array(
				// Added as there was a bug in the code, and not sure how long
				// it has been there so DB might be corrupt.  The problem is a
				// duplicate 245$a where there should only be one in biblio_field
				// for a bibid. Fix to be designed if needed.
				//'error' => T("%count% items with multiple 245 fields"),
				'error' => T("items with multiple 245 fields"),
				'countSql' => 'SELECT COUNT(*) AS count '
					. 'FROM (SELECT f.bibid, COUNT(f.fieldid)'
					. 'FROM biblio_field f, biblio_subfield s '
					. 'WHERE s.subfield_cd=\'a\' AND f.tag=\'245\' '
					. 'AND f.bibid=s.bibid AND f.fieldid=s.fieldid '
					. 'GROUP BY f.bibid '
					. 'HAVING COUNT(f.fieldid) > 1) AS t',
				'listSql' => 'SELECT bibid, COUNT(*) AS count '
					. 'FROM (SELECT f.bibid, COUNT(f.fieldid)'
					. 'FROM biblio_field f, biblio_subfield s '
					. 'WHERE s.subfield_cd=\'a\' AND f.tag=\'245\' '
					. 'AND f.bibid=s.bibid AND f.fieldid=s.fieldid '
					. 'GROUP BY f.bibid '
					. 'HAVING COUNT(f.fieldid) > 1) AS t',
				// NO AUTOMATIC FIX
			),			
*/
			$this->checks[] = array(
				//'error' => T("%count% items with multiple un-repeatable fields"),
				'error' => T("items with multiple un-repeatable fields"),
				'countSql' => 'SELECT COUNT(DISTINCT t.bibid)AS count FROM ('
					. 'SELECT f.bibid, concat( f.tag, s.subfield_cd ) AS marc, COUNT( f.fieldid ) AS count '
					. 'FROM biblio_field f, biblio_subfield s, material_fields m, biblio b '
					. 'WHERE f.bibid=b.bibid AND s.fieldid=f.fieldid '
					.	'AND m.material_cd=b.material_cd AND m.repeatable<2 '
					.	'AND m.tag=f.tag AND m.subfield_cd=s.subfield_cd '
					. 'GROUP BY f.bibid, marc '
					. 'HAVING count > 1 '
					.	') AS t',
/*
				'listSql' => 'SELECT DISTINCT t.bibid FROM ('
					. 'SELECT f.bibid, concat( f.tag, s.subfield_cd ) AS marc, COUNT( f.fieldid ) AS count '
					. 'FROM biblio_field f, biblio_subfield s, material_fields m, biblio b '
					. 'WHERE f.bibid=b.bibid AND s.fieldid=f.fieldid '
					.	'AND m.material_cd=b.material_cd AND m.repeatable<2 '
					.	'AND m.tag=f.tag AND m.subfield_cd=s.subfield_cd '
					. 'GROUP BY f.bibid, marc '
					. 'HAVING count > 1 '
					.	') AS t',
*/
				'fixFn' => 'removeRepeaters',
				// NO AUTOMATIC FIX
			);
			$this->checks[] = array(
				//'error' => T("%count% items with empty collections"),
				'error' => T("items with empty collections"),
				'countSql' => 'SELECT COUNT(*) AS count '
					. 'FROM biblio '
					. 'WHERE collection_cd = 0 ',
				'fixSql' => 'update biblio set collection_cd = '
					. '(SELECT code FROM collection_dm '
					. ' WHERE default_flg = \'Y\' )'
					.	'WHERE collection_cd = 0 ',
			);
			$this->checks[] = array(
				//'error' => T("%count% items with empty media-type"),
				'error' => T("items with empty media-type"),
				'countSql' => 'SELECT COUNT(*) AS count '
					. 'FROM biblio '
					. 'WHERE material_cd = 0 ',
				'fixSql' => 'update biblio set material_cd = '
					. '(SELECT code FROM material_type_dm '
					. ' WHERE default_flg = \'Y\' )'
					.	'WHERE material_cd = 0 ',
			);
			$this->checks[] = array(
				//'error' => T("%count% unattached copy status history records"),
				'error' => T("unattached copy status history records"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join biblio_copy '
					. 'on biblio_copy.copyid=biblio_status_hist.copyid '
					. 'where biblio_copy.copyid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete from biblio_status_hist '
					. 'using biblio_status_hist left join biblio_copy '
					. 'on biblio_copy.copyid=biblio_status_hist.copyid '
					. 'where biblio_copy.copyid is null ',
				*/
			);
			$this->checks[] = array(
				//'error' => T("%count% invalid biblio in copy status history records"),
				'error' => T("invalid biblio in copy status history records"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join biblio '
					. 'on biblio.bibid=biblio_status_hist.bibid '
					. 'where biblio.bibid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete from biblio_status_hist '
					. 'using biblio_status_hist left join biblio '
					. 'on biblio.bibid=biblio_status_hist.bibid '
					. 'where biblio.bibid is null ',
				*/
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryInvalidStatusCodes"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join biblio_status_dm '
					. 'on biblio_status_dm.code=biblio_status_hist.status_cd '
					. 'where biblio_status_dm.code is null ',
				'fixSql' => 'update biblio_status_hist set status_cd = \'lst\''
					. 'using biblio_status_hist left join biblio_status_dm '
					. 'on biblio_status_dm.code=biblio_status_hist.status_cd '
					. 'where biblio_status_dm.code is null ',
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryBrokenBibidRef"),
				'countSql' => 'select count(*) as count '
					. 'from booking left join biblio '
					. 'on biblio.bibid=booking.bibid '
					. 'where biblio.bibid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete from booking '
					. 'using booking left join biblio '
					. 'on booking.bibid=biblio.bibid '
					. 'where biblio.bibid is null ',
				*/
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryBrokenBooking"),
				'countSql' => 'select count(*) as count '
					. 'from booking '
					. 'where booking.due_dt is not null '
					. 'and booking.out_dt is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'DELETE FROM `booking` '
					. 'where booking.due_dt is not null '
					. 'and booking.out_dt is null ',
				*/
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryBrokenOutRef"),
				'countSql' => 'select count(*) as count '
					. 'from booking left join biblio_status_hist '
					. 'on biblio_status_hist.histid=booking.out_histid '
					. 'where booking.out_histid is not null '
					. 'and biblio_status_hist.histid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'DELETE b FROM `booking` as b '
					. 'WHERE b.`out_histid` IN (Select out_histid FROM('
					. 'select DISTINCT bk.`out_histid` from booking as bk '
					. 'left join biblio_status_hist '
					. 'on biblio_status_hist.histid=bk.out_histid '
					. 'where bk.out_histid is not null '
					. 'and biblio_status_hist.histid is null) X)',
				*/
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryBrokenReturnRef"),
				'countSql' => 'select count(*) as count '
					. 'from booking left join biblio_status_hist '
					. 'on biblio_status_hist.histid=booking.ret_histid '
					. 'where booking.ret_histid is not null '
					. 'and biblio_status_hist.histid is null ',
				// NO AUTOMATIC FIX
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryNoAssBooking"),
				'countSql' => 'select count(*) as count '
					. 'from booking_member left join booking '
					. 'on booking.bookingid=booking_member.bookingid '
					. 'where booking.bookingid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete from booking_member '
					. 'using booking_member left join booking '
					. 'on booking.bookingid=booking_member.bookingid '
					. 'where booking.bookingid is null ',
				*/
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryNoAssMember"),
				'countSql' => 'select count(*) as count '
					. 'from booking_member left join member '
					. 'on member.mbrid=booking_member.mbrid '
					. 'where member.mbrid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete from booking_member '
					. 'using booking_member left join member '
					. 'on member.mbrid=booking_member.mbrid '
					. 'where member.mbrid is null ',
				*/
			);
			$this->checks[] = array(
				//'error' => T("%count% copies without site"),
				'error' => T("copies without site"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_copy left join site '
					. 'on site.siteid=biblio_copy.siteid '
					. 'where biblio_copy.siteid is null',
				// NO AUTOMATIC FIX
			);
			$this->checks[] = array(
				//'error' => T("%count% members without sites"),
				'error' => T("members without sites"),
				'countSql' => 'select count(*) as count '
					. 'from member left join site '
					. 'on site.siteid=member.siteid '
					. 'where site.siteid is null ',
				// NO AUTOMATIC FIX
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryUnattachedAccTrans"),
				'countSql' => 'select count(*) as count '
					. 'from member_account left join member '
					. 'on member.mbrid=member_account.mbrid '
					. 'where member.mbrid is null ',
				'fixSql' => 'delete from member_account '
					. 'using member_account left join member '
					. 'on member.mbrid=member_account.mbrid '
					. 'where member.mbrid is null ',
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryChangedCopyStatus"),
				'countSql' => 'select count(*) as count '
					. 'from booking b, biblio_status_hist h, biblio_copy c '
					. 'where b.out_histid is not null and b.ret_histid is null '
					. 'and h.histid=b.out_histid and c.copyid=h.copyid '
					. 'and c.histid != b.out_histid ',
				// NO AUTOMATIC FIX
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryOutRecNoBooking"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_status_hist left join booking '
					. 'on booking.out_histid=biblio_status_hist.histid '
					. 'where biblio_status_hist.status_cd=\'out\' '
					. 'and booking.bookingid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete bsh from biblio_status_hist as bsh where bsh.histid in '
					. '(select histid from (select distinct bs.histid '
					. 'from biblio_status_hist as bs left join booking as b '
					. 'on b.out_histid=bs.histid '
					. 'where bs.status_cd=\'out\' '
					. 'and b.bookingid is null) X) ',
				*/
			);
			$this->checks[] = array(
				//'error' => T("%count% double check outs"),
				'error' => T("double check outs"),
				'countFn' => 'countDoubleCheckouts',
				// NO AUTOMATIC FIX
			);
	}
	function check_el($fix=false) {
		$errors = array();
		$checks = $this->checks;
		foreach ($checks as $chk) {
			assert('isset($chk["error"])');
			//echo $chk["error"]."<br />";
			if (isset($chk['countSql'])) {
				$row = $this->select1($chk['countSql']);
				$count = $row["count"];
			} elseif (isset($chk['countFn'])) {
				$fn = $chk['countFn'];
				assert('method_exists($this, $fn)');
				$count = $this->$fn();
			} else {
				assert('NULL');
			}
			if ($count) {
				//$msg = $count . T($chk["error"], array('count'=>$count));
				$msg = $count." ".$chk["error"];
				if ($fix) {
					if (isset($chk['fixSql'])) {
						$this->act($chk['fixSql']);
						$msg .= ' <b>'.T("FIXED").'</b> ';
					} elseif (isset($chk['listSql'])) {
						$msg .= '<br />list: ';
						$rows = $this->select($chk['listSql']);
						while ($row = $rows->fetch_assoc()) {
							$msg .= '<a href="../catalog/srchForms.php?bibid='.$row['bibid'].'">'.$row['bibid'].'</a>, ';
						}
					} elseif (isset($chk['fixFn'])) {
						$fn = $chk['fixFn'];
						assert('method_exists($this, $fn)');
						$error = $this->$fn();
						if ($error) {
							$msg .= ' <b>'.T("CAN'T FIX").'</b>: '.$error->toStr();
						} else {
							$msg .= ' <b>'.T("FIXED").'</b> ';
						}
					} else {
						$msg .= ' <b>'.T("CANNOT BE FIXED AUTOMATICALLY").'</b>';
					}
				}
				$errors[] = new ObErr($msg);
			}
		}
		return $errors;
	}
	
	/* Remove repeating MARC fields that should not repeat */
	function removeRepeaters () {
		$bibList = array();
		## collect a set of offending cases
		$sql =  'SELECT f.bibid, f.tag, s.subfield_cd, s.subfieldid, COUNT( f.fieldid ) AS count '
					. 'FROM biblio_field f, biblio_subfield s, material_fields m, biblio b '
					. 'WHERE f.bibid=b.bibid AND s.fieldid=f.fieldid '
					.	'AND m.material_cd=b.material_cd AND m.repeatable<2 '
					.	'AND m.tag=f.tag AND m.subfield_cd=s.subfield_cd '
					. 'GROUP BY f.bibid, f.tag, s.subfield_cd '
					. 'HAVING count > 1 ';
		$status = array();
		$errors = 0;
		$ptr = $this->select($sql);
		while ($bib = $ptr->fetch_assoc()) {
			$case = $bib['bibid'].'-'.$bib['tag'].$bib['subfield_cd'];
			$dups[$case] = array('nmbr'=>$bib['count'],
											'bibid'=>$bib['bibid'],
											'tag'=>$bib['tag'], 
											'subcd'=>$bib['subfield_cd'],
											'subId'=>$bib['subfieldid']
											);
		}
		## loop through all cases found above
		foreach ($dups as $case) {
			## attempt to delete all but one entry in this case
			for ($i=0; $i<$case['nmbr']-1; $i++) {
				$sql = "Delete FROM biblio_subfield ".
							 "WHERE (subfieldid = ".$case['subId'].") ";
				$this->act($sql);			 
			}
		}
		return $errors;
	}
	
	/* Count and fix functions below */
	function countDoubleCheckouts() {
		$sql = 'select histid, copyid, status_cd from biblio_status_hist order by histid ';
		$status = array();
		$errors = 0;
		$r = $this->select($sql);
		while ($row = $r->fetch_assoc()) {
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
