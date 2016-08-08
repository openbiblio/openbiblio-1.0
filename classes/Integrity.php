<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Queryi.php"));
require_once(REL(__FILE__, "../plugins/supportFuncs.php"));

ini_set('display_errors', 1);

class Integrity extends Queryi{
	private $checks= array();
	public function __construct() {
		parent::__construct();
            $this->checks[] = array(
                'error' => T("table structure does not match it's model"),
				'countFn' => 'chkFields',
				'fixFn' => 'fixFields',
            );
			$this->checks[] = array(
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
				'error' => T("OB size row is missing from Settings"),
				'countSql' => 'SELECT (CASE (COUNT(*)) WHEN 0 THEN 1 ELSE 0 END) AS count '
                    .  'FROM settings WHERE name = "OBsize" ',
				'fixSql' => 'INSERT INTO settings '
					. ' (name,title,type,width) '
                    . " VALUES('OBsize', 'Current Size','number',16)",
			);

			$this->checks[] = array(
				'error' => T("Secret key column is missing for staff members"),
				'countSql' => 'SELECT (CASE (COUNT(COLUMN_NAME)) WHEN 0 THEN 1 ELSE 0 END) AS count '
					. 'FROM information_schema.COLUMNS '
					. 'WHERE TABLE_NAME = "staff"'
					. 'AND COLUMN_NAME = "secret_key"',
				'fixSql' => 'alter table staff '
					. 'add column secret_key char(32) NOT NULL'
			);


			$this->checks[] = array(
				'error' => T("Member Fields DM Table should allow null values for default_flg field"),
				'countSql' => 'SELECT COUNT(COLUMN_NAME) '
					. 'FROM information_schema.COLUMNS '
					. "WHERE TABLE_NAME='member_fields_dm' AND COLUMN_NAME='default_flg' AND IS_NULLABLE='NO'",
				'fixSql' => 'ALTER TABLE member_fields_dm MODIFY default_flg char(1) NULL'
			);

			$this->checks[] = array(
				'error' => T("Staff member does not have secret key"),
				'countSql' => 'select ( select count(*) as count from staff as s '
					. 'where secret_key="") as count '
					. 'from (select 1 as secret_key) as dummy;',
				'fixSql' => 'update staff '
					. 'set secret_key ="' . md5(time()) .'" '
					. 'where secret_key="" ',
			);

			$this->checks[] = array(
        			'error' => T("Legal First Name column is missing from Members table"),
        			'countSql' => 'SELECT (CASE (COUNT(COLUMN_NAME)) WHEN 0 THEN 1 ELSE 0 END) AS count '
                			. 'FROM information_schema.COLUMNS '
                			. 'WHERE TABLE_NAME = "member"'
                			. 'AND COLUMN_NAME = "first_legal_name"',
        			'fixSql' => 'alter table member '
                			. 'add column first_legal_name varchar(50) DEFAULT NULL'
			);
			$this->checks[] = array(
        			'error' => T("Legal Last Name column is missing from Members table"),
        			'countSql' => 'SELECT (CASE (COUNT(COLUMN_NAME)) WHEN 0 THEN 1 ELSE 0 END) AS count '
                			. 'FROM information_schema.COLUMNS '
                			. 'WHERE TABLE_NAME = "member"'
                			. 'AND COLUMN_NAME = "last_legal_name"',
        			'fixSql' => 'alter table member '
                			. 'add column last_legal_name varchar(50) DEFAULT NULL'
			);
			$this->checks[] = array(
        			'error' => T("Collection circ table is missing important circulation features"),
        			'countSql' => 'SELECT (CASE (COUNT(COLUMN_NAME)) WHEN 0 THEN 1 ELSE 0 END) AS count '
                			. 'FROM information_schema.COLUMNS '
                			. 'WHERE TABLE_NAME = "collection_circ"'
                			. 'AND COLUMN_NAME = "due_date_calculator"',
        			'fixSql' => 'alter table collection_circ '
					. 'add column minutes_due_back int NOT NULL, '
  					. "add column due_date_calculator varchar(30) NOT NULL DEFAULT 'simple', "
  					. 'add column minutes_before_closing smallint DEFAULT 0, '
					. 'add column important_date timestamp NULL DEFAULT NULL, '
					. "add column important_date_purpose varchar(30) NOT NULL DEFAULT 'not enabled', "
  					. 'add column number_of_minutes_between_fee_applications int NOT NULL DEFAULT 1440, '
					. 'add column number_of_minutes_in_grace_period int DEFAULT 0, '
					. 'change daily_late_fee regular_late_fee decimal(4,2) NOT NULL'
			);
			$this->checks[] = array(
        			'error' => T("Collection circ table is missing important circulation features"),
        			'countSql' => 'SELECT (CASE (COUNT(COLUMN_NAME)) WHEN 0 THEN 1 ELSE 0 END) AS count '
                			. 'FROM information_schema.COLUMNS '
                			. 'WHERE TABLE_NAME = "collection_circ"'
                			. 'AND COLUMN_NAME = "pre_closing_padding"',
        			'fixSql' => 'alter table collection_circ '
					. 'add column pre_closing_padding int DEFAULT 0 '
			);

			$this->checks[] = array(
				'error' => T("Hours not attached to sites"),
				'countSql' => 'select count(*) as count '
					. 'from open_hours left join site '
					. 'on open_hours.siteid=site.siteid '
					. 'where site.siteid is null ',
				'fixSql' => 'delete from open_hours '
					. 'using open_hours left join site '
					. 'on open_hours.siteid=site.siteid '
					. 'where site.siteid is null ',
			);
			$this->checks[] = array(
				'error' => T("Hours overlap"),
				'countSql' => 'select count(*) as count '
					. 'from open_hours a, open_hours b '
					. 'where a.start_time<b.start_time '
					. 'and a.end_time>=b.start_time '
					. 'and a.siteid=b.siteid '
					. 'and a.day=b.day '
					. 'and a.effective_start_date IS NULL '
					. 'and a.effective_end_date IS NULL ',
					// NO AUTOMATIC FIX
			);

			$this->checks[] = array(
				'error' => T("copies with broken status references"),
				'countSql' => 'select count(*) as count '
					. 'from biblio_copy left join biblio_status_hist '
					. 'on biblio_status_hist.histid=biblio_copy.histid '
					. 'where biblio_status_hist.histid is null ',
				// NO AUTOMATIC FIX
			);
			
			$this->checks[] = array(
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
			);
			$this->checks[] = array(
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
				'error' => T("unattached copy status history records"),
				'countSql' => 'select count(*) as count '
                    . 'from biblio_status_hist as bsh, biblio_copy as bc '
                    . 'where bsh.bibid = bc.bibid '
                    . 'and bc.copyid is null ',
				// NO AUTOMATIC FIX
				/*
				'fixSql' => 'delete from biblio_status_hist '
					. 'using biblio_status_hist left join biblio_copy '
					. 'on biblio_copy.copyid=biblio_status_hist.copyid '
					. 'where biblio_copy.copyid is null ',
				*/
			);
			$this->checks[] = array(
				'error' => T("invalid biblio in copy status history records"),
				'countSql' => 'select count(*) as count '
                    . 'from biblio_status_hist as bsh '
                    . 'where bsh.bibid is null ',
				'fixSql' => 'delete from biblio_status_hist '
                   . 'where bibid is null ',
			);
			$this->checks[] = array(
				'error' => T("IntegrityQueryInvalidStatusCodes"),
				'countSql' => 'select count(*) as count '
                    		. 'from biblio_status_hist as bsh '
                    		. 'where bsh.status_cd NOT IN ('
                    		. 'select code from biblio_status_dm as bsd ) '
				// NO AUTOMATIC FIX
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
//			$this->checks[] = array(
//				//'error' => T("%count% double check outs"),
//				'error' => T("double check outs"),
//				'countFn' => 'countDoubleCheckouts',
//				// NO AUTOMATIC FIX
//			);
	}

	function check_el($fix=false) {
		$errors = array();
		$checks = $this->checks;
		foreach ($checks as $chk) {
			assert('isset($chk["error"])');
			//echo $chk["error"]."<br />";
			if (isset($chk['countSql'])) {
				$rslt = $this->select1($chk['countSql']);
                $row = $rslt[0];
                //echo "in Integrity::check_el(), countSQL => ".$chk['countSql']." <br />\n";
                //echo "in Integrity::check_el(), countSQL => ";print_r($row);echo "<br />\n";
                $count = $row["count"];
                //$count = count($row['count']);
                //echo "count= $count <br />\n";
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
                        //echo "in Integrity::check_el(), fixSql = {$chk['fixSql']} <br />\n";
						$this->act($chk['fixSql']);
						$msg .= ' <b>'.T("FIXED").'</b> ';
					} elseif (isset($chk['listSql'])) {
						$msg .= '<br />list: ';
						$rows = $this->select($chk['listSql']);
						//while ($row = $rows->fetch_assoc()) {
                        foreach ($rows as $row) {
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

    function getColmnList ($table) {
        $sql = "select COLUMN_NAME"
             . "  from information_schema.columns"
             . "  where " //table_schema = 'your_DB_name'
             . "    table_name = '$table'";
        //echo "sql = $sql<br />\n";
        $set = $this->select($sql);
        $rslt = $set->fetchAll();
        foreach ($rslt as $entry) {
            $list[] = $entry['COLUMN_NAME'];
        };
        //print_r($list);echo"<br />\n";
        return $list;
    }

    /* compare field definition in each model against related DB table */
    function chkFields() {
        $fileList = getFileList('../model');
        foreach ($fileList as $file) {
            ## first collect model definition
            //echo "$file <br />\n";
//            if ($file == '../model/Integrity.php') continue;
            if ($file == '../model/MarcStore.php') continue;
            if ($file == '../model/OpenHours.php') continue;
            include_once($file);
            $className = pathInfo($file, PATHINFO_FILENAME);
            //echo "Model: $className <br />\n";
            $obj = new $className;
            $tblName = $obj->getName();
            $fldNames = array_keys($obj->getFields());
            $fldParms = array_values($obj->getFields());
            //echo "db table: $tblName<br />\n";
            //echo "model fields: ";print_r($fldset);echo "<br />\n";
            $obj = null;

            ## now get current db field names
            $dbflds = $this->getColmnList($tblName);

            ## finally compare them
            $errors = array_diff($fldNames, $dbflds);
            if (sizeof($errors) > 0) {
                echo "field error(s) in table $tblName: ";print_r($errors); echo"<br />\n";
                $this->tblErrs[$tblName] = $errors;
            }
        }
        //$this->fixFields(); // intended for debug use only
        return sizeof($this->tblErrs);
    }

    /* fix db field descrepencies */
    function fixFields() {
        if (!isset($this->tblErrs)) return;
        foreach ($this->tblErrs as $tbl=>$flds) {
            //echo "table: $tbl<br />";print_r($flds);echo"<br />\n";
            $sql = "ALTER $tbl ADD COLUMN (";
            foreach ($flds as $fld) {
                $sql = $sql." $fld char(20) NOT NULL,";
            }
            $sql = substr($sql, 0, -1) . ')';
            echo "$sql <br />\n";
//            $this->act($sql);
        }
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
		//$status = array();
		$errors = 0;
        $dups = array();
		$ptr = $this->select($sql);
		//while ($bib = $ptr->fetch_assoc()) {
        foreach ($ptr as $bib) {
			$caseNm = $bib['bibid'].'-'.$bib['tag'].$bib['subfield_cd'];
			$dups[$caseNm] = array('nmbr'=>$bib['count'],
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
		//while ($row = $r->fetch_assoc()) {
        foreach ($r as $row) {
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