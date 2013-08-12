<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
/**
 * This class provides an interface for DB functions unique to OB upgrade
 * @author Fred LaPlante
 */

class UpgradeQuery extends InstallQuery {
  public function __construct() {
		parent::__construct();
  }

	/**
   # Returns array($notices, $error).
   # On failure, $error is an Error and $notices should not be used.
   # On success, $error is NULL and $notices is an array of strings
   # notifying the user of upgrade changes.
	 */
  public function performUpgrade_e($fromTablePrfx = DB_TABLENAME_PREFIX, $toTablePrfx = DB_TABLENAME_PREFIX) {
    # Each of these routines should update the given version to the next higher version.
    $upgrades = array(
      '0.3.0' => '_upgrade030_e',
      '0.4.0' => '_upgrade040_e',
      '0.5.2' => '_upgrade052_e',
      '0.6.0' => '_upgrade060_e',
      '0.7.0' => '_upgrade070_e',
    );
    $tmpPrfx = "obiblio_upgrade_";
    # FIXME - translate upgrade messages
    $locale = $this->getCurrentLocale($fromTablePrfx);

    $notices = array();
    # Do this first so new tables always have a prefix, if desired.
    if ($fromTablePrfx != $toTablePrfx) {
      $this->renameTables($fromTablePrfx, $toTablePrfx);
    }
    do {
      $version = $this->getCurrentDatabaseVersion($toTablePrfx);
      if ($version == OBIB_LATEST_DB_VERSION) {
        break;	# Done
      } elseif (isset($upgrades[$version])) {
        $func = $upgrades[$version];
        list($n, $error) = $this->$func($toTablePrfx, $tmpPrfx);
        if ($error) {
          return array(NULL, $error);
        }
        $notices = array_merge($notices, $n);
      } elseif (!$version) {
        $error = new Error(T("NoExistingOpenBiblioDatabasePleasePerformFreshInstall"));
        return array(NULL, $error);
      } else {
        $error = new Error(T("Unknown database version").': '.$version.'. '.T("No automatic upgrade routine available."));
        return array(NULL, $error);
      }
    } while (1);
    return array($notices, NULL);
  }

	## ------------------------------------------------------------------------ ##
  private function insertBiblioFields($tag, $subFieldCd,
                              $fromTablePrfx, $toTablePrfx,  $colName){
    $sql = "insert into ".$toTablePrfx."biblio_field"
          ."(bibid, fieldid, tag,   ind1_cd,ind2_cd,subfield_cd,     field_data) select "
          ." bibid, null,  ".$tag.",null,   null,'".$subFieldCd."',".$colName
          ." from ".$fromTablePrfx."biblio "
          ."where ".$colName." is not null";
    $this->exec($sql);
   }
   
   private function copyDataToNewTable($tableName, $fromTablePrfx, $toTablePrfx, $sqlSelectConversion) {
        $sql = "delete from ".$toTablePrfx.$tableName;
        !$this->exec($sql);
        $conv = "(".implode(", ", array_keys($sqlSelectConversion)).") "
                . "select ".implode(", ", array_values($sqlSelectConversion));
        $sql = "insert into ".$toTablePrfx.$tableName." "
              .$conv
              ." from ".$fromTablePrfx.$tableName;
        $this->exec($sql);
   }
   
   private function renamePrfxedTable($tableName, $fromTablePrfx, $toTablePrfx) {
     return $this->renameTable($fromTablePrfx.$tableName, $toTablePrfx.$tableName);
   }
  
  # Individual upgrade functions
  # Each of these should upgrade the indicated database version by one version.
  # $prfx is the table prefix to be used by both the original and upgraded databases.
  # $tmpPrfx is a prefix which may be used for temporary tables.
  # Return value is the same as performUpgrade_e()
  
  /* ======================================================================== */
	/* Upgrade 0.3.0 to 0.4.0 */
  private function _upgrade030_e($prfx, $tmpPrfx) {
    # 0.3.0 was English only
    $this->freshInstall('en', false, '0.4.0', $tmpPrfx);

    # marc data conversion
    $fields = array(
      'edition' => array(250, 'a'),
      'isbn_nmbr' => array(20, 'a'),
      'lccn_nmbr' => array(10, 'a'),
      'lc_call_nmbr' => array(50, 'a'),
      'lc_item_nmbr' => array(50, 'b'),
      'udc_nmbr' => array(82, 'a'),
      'udc_ed_nmbr' => array(82, '2'),
      'publication_loc' => array(260, 'a'),
      'publisher' => array(260, 'b'),
      'publication_dt' => array(260, 'c'),
      'summary' => array(520, 'a'),
      'pages' => array(300, 'a'),
      'physical_details' => array(300, 'b'),
      'dimensions' => array(300, 'c'),
      'accompanying' => array(300, 'e'),
      'price' => array(20, 'c'),
    );
    foreach ($fields as $fname => $marc) {
      $this->insertBiblioFields($marc[0], $marc[1], $prfx, $tmpPrfx, $fname);
    }
    
    # biblio table conversion
    $this->copyDataToNewTable("biblio", $prfx, $tmpPrfx,
                              array(
                                "bibid" => "bibid",
                                "create_dt" => "create_dt",
                                "last_change_dt" => "last_updated_dt",
                                //TODO: Currently using 1 for last_change_userid, get real id
                                "last_change_userid" => "1",
                                "material_cd" => "material_cd",
                                "collection_cd" => "collection_cd",
                                "call_nmbr1" => "call_nmbr",
                                "call_nmbr2" => "NULL",
                                "call_nmbr3" => "NULL",
                                "title" => "title",
                                "title_remainder" => "subtitle",
                                "responsibility_stmt" => "trim(concat(author,' ',add_author))",
                                "author" => "author",
                                "topic1" => "NULL",
                                "topic2" => "NULL",
                                "topic3" => "NULL",
                                "topic4" => "NULL",
                                "topic5" => "NULL",
                                "opac_flg" => "'Y'",
                              ));
   
    # biblio_status -> biblio_copy conversion
    $sql = "insert into ".$tmpPrfx."biblio_copy "
			."(bibid,copyid,copy_desc,barcode_nmbr,status_cd,status_begin_dt,due_back_dt,mbrid) select"
			." b.bibid,null,null,b.barcode_nmbr,ifnull(bs.status_cd,'in'),ifnull(bs.status_begin_dt,"
			." b.create_dt),bs.due_back_dt,bs.mbrid from "
          .$prfx."biblio as b "
          ."left join ".$prfx."biblio_status as bs on b.bibid=bs.bibid";
    $this->exec($sql);
    
    $sql = "update ".$tmpPrfx."biblio_copy set status_cd = 'hld' where status_cd = 'cll'";
    $this->exec($sql);
    
    $this->dropTable($prfx.'biblio');
    $this->dropTable($prfx.'biblio_copy');

    #collection_dm data conversion
    $this->copyDataToNewTable("collection_dm", $prfx, $tmpPrfx,
                              array(
                                "code" => "code",
                                "description" => "description",
                                "default_flg" => "default_flg",
                                "days_due_back" => "days_due_back",
                                "daily_late_fee" => "0.00",
                              ));
    
    $this->dropTable($prfx.'collection_dm');

    #member table conversion
    $this->copyDataToNewTable("member", $prfx, $tmpPrfx,
                              array(
                                "mbrid" => "mbrid",
                                "barcode_nmbr" => "barcode_nmbr",
                                "create_dt" => "create_dt",
                                "last_change_dt" => "sysdate()",
                                "last_change_userid" => "1",
                                "last_name" => "last_name",
                                "first_name" => "first_name",
                                "address1" => "address1",
                                "address2" => "address2",
                                "city" => "city",
                                "state" => "state",
                                "zip" => "zip",
                                "zip_ext" => "zip_ext",
                                "home_phone" => "home_phone",
                                "work_phone" => "work_phone",
                                "email" => "NULL",
                                "classification" => "classification",
                                "school_grade" => "school_grade",
                                "school_teacher" => "school_teacher",
                              ));

    $this->dropTable($prfx.'member');

    #staff table conversion
    $this->copyDataToNewTable("staff", 
                              $prfx, $tmpPrfx,
                              array(
                                "userid" => "userid",
                                "create_dt" => "create_dt",
                                "last_change_dt" => "last_updated_dt",
                                "last_change_userid" => "1",
                                "username" => "username",
                                "pwd" => "pwd",
                                "last_name" => "last_name",
                                "first_name" => "first_name",
                                "suspended_flg" => "suspended_flg",
                                "admin_flg" => "admin_flg",
                                "circ_flg" => "circ_flg",
                                "circ_mbr_flg" => "circ_flg",
                                "catalog_flg" => "catalog_flg",
                                "reports_flg" => "admin_flg",
                              ));
    
    $this->dropTable($prfx.'staff');

    #settings data conversion
    $this->copyDataToNewTable("settings", 
                              $prfx, $tmpPrfx,
                              array(
                                "library_name" => "library_name",
                                "library_image_url" => "library_image_url",
                                "use_image_flg" => "use_image_flg",
                                "library_hours" => "library_hours",
                                "library_phone" => "library_phone",
                                "library_url" => "library_url",
                                "opac_url" => "opac_url",
                                "session_timeout" => "session_timeout",
                                "items_per_page" => "items_per_page",
                                "version" => "'0.4.0'",
                                "themeid" => "1",
                                "purge_history_after_months" => "6",
                                "block_checkouts_when_fines_due" => "'Y'",
                                "locale" => "'en'",
                                "charset" => "'iso-8859-1'",
                                "html_lang_attr" => "NULL",
                              ));

    $this->dropTable($prfx.'settings');
    
    # moving tables that haven't changed in structure,
    # yet may have been modified by the user
    $this->renamePrfxedTable("material_type_dm", $prfx, $tmpPrfx);
    $this->renamePrfxedTable("theme", $prfx, $tmpPrfx);
    $this->renameTables($tmpPrfx, $prfx);
    $notices = array('Any existing hold requests have been forgotten.');
    return array($notices, NULL); # no error
  }
  
  /* ======================================================================== */
  /* Upgrade 0.4.0 to 0.5.2 */
  private function _upgrade040_e($prfx, $tmpPrfx) {
    $settings = $this->exec('select * from '.$prfx.'settings ');
    if (is_dir('../locale/'.$settings[0]['locale'].'/sql/0.5.2/domain')) {
      $domainDir = '../locale/'.$settings[0]['locale'].'/sql/0.5.2/domain';
    } else {
      $domainDir = '../locale/en/sql/0.5.2/domain';
    }
    $this->exec('alter table '.$prfx.'staff modify pwd char(32)');
    $this->exec('update '.$prfx.'staff set pwd=md5(lower(username))');
    $this->exec('alter table '.$prfx.'biblio_copy '
                . 'add renewal_count tinyint unsigned not null default 0 '
                . 'after mbrid ');
    $this->exec('alter table '.$prfx.'biblio_status_hist '
                . 'add renewal_count tinyint unsigned not null default 0 '
                . 'after mbrid ');
    $this->executeSqlFile('../install/0.5.2/sql/checkout_privs.sql', $prfx);
    $this->exec('insert into '.$prfx.'checkout_privs '
                . 'select mat.code material_cd, 1 classification, '
                . 'mat.adult_checkout_limit checkout_limit, '
                . '0 renewal_limit '
                . 'from material_type_dm mat ');
    $this->exec('insert into '.$prfx.'checkout_privs '
                . 'select mat.code material_cd, 2 classification, '
                . 'mat.juvenile_checkout_limit checkout_limit, '
                . '0 renewal_limit '
                . 'from material_type_dm mat ');
    $this->exec('alter table '.$prfx.'material_type_dm '
                . 'drop adult_checkout_limit, '
                . 'drop juvenile_checkout_limit ');
    $this->executeSqlFile('../install/0.5.2/sql/material_usmarc_xref.sql', $prfx);
    $this->exec("update ".$prfx."mbr_classify_dm set code='1' where code='a' ");
    $this->exec("update ".$prfx."mbr_classify_dm set code='2' where code='j' ");
    $this->exec('alter table '.$prfx.'mbr_classify_dm '
                . 'modify code smallint auto_increment, '
                . 'add max_fines decimal(4,2) not null after default_flg ');
    $this->executeSqlFile('../install/0.5.2/sql/member_fields.sql', $prfx);
    $this->executeSqlFile('../install/0.5.2/sql/member_fields_dm.sql', $prfx);
    $this->exec('insert '.$prfx.'member_fields '
                . "select mbrid, 'schoolGrade' code, school_grade data "
                . "from member where school_grade is not null "
                . "and school_grade <> '' ");
    $this->exec('insert '.$prfx.'member_fields '
                . "select mbrid, 'schoolTeacher' code, school_teacher data "
                . "from member where school_teacher is not null "
                . "and school_teacher <> '' ");
    $this->executeSqlFile($domainDir.'/member_fields_dm.sql', $prfx);
    $this->exec("update ".$prfx."member set classification=1 where classification='a' ");
    $this->exec("update ".$prfx."member set classification=2 where classification='j' ");
    $this->exec('alter table '.$prfx.'member '
                . 'add address text null after first_name, '
                . 'modify classification smallint not null ');
    # What a mess
    $this->exec('update '.$prfx.'member set address= '
                . "concat_ws('\n', nullif(address1, ''), nullif(address2, ''), "
                . "concat_ws('', city, concat(', ', nullif(state, '')), "
                . "concat(' ', nullif(zip, ''), ifnull(concat('-', zip_ext), '')))) ");
    $this->exec('alter table '.$prfx.'member '
                . 'drop address1, drop address2, '
                . 'drop city, drop state, drop zip, drop zip_ext, '
                . 'drop school_grade, drop school_teacher ');
    $this->exec('alter table '.$prfx.'settings '
                . 'add hold_max_days smallint not null '
                . 'after block_checkouts_when_fines_due ');
    $this->exec('update '.$prfx.'settings '
                . 'set hold_max_days=14 ');
    $this->exec('drop table '.$prfx.'state_dm ');

    $this->exec('update '.$prfx.'settings set version=\'0.5.2\'');
    $notices = array('All staff passwords have been reset to be equal to the corresponding usernames.');
    return array($notices, NULL); # no error
  }
  
  /* ======================================================================== */
  /* Upgrade 0.5.2 to 0.6.0 */
  private function _upgrade052_e($prfx, $tmpPrfx) {
    $this->exec('alter table '.$prfx.'biblio_copy '
                . 'add create_dt datetime not null '
                . 'after copyid ');
    $this->exec('update biblio_copy bc, biblio b '
                . 'set bc.create_dt=b.create_dt '
                . 'where b.bibid=bc.bibid ');
    $this->exec("update settings set version='0.6.0'");
    $notices = array();
    return array($notices, NULL);
  }
  
  /* ======================================================================== */
  /* Upgrade 0.6.0 to 0.7.0 */
  private function _upgrade060_e($prfx, $tmpPrfx) {
    $this->executeSqlFile('../install/0.7.0/sql/biblio_copy_fields.sql', $prfx);
    $this->executeSqlFile('../install/0.7.0/sql/biblio_copy_fields_dm.sql', $prfx);
    $this->exec("update settings set version='0.7.0'");
    $notices = array();
    return array($notices, NULL);
  }
  
  /* ======================================================================== */
  /* Upgrade 0.7.0 to 1.0b */
  private function _upgrade070_e($prfx, $tmpPrfx) {
  
  	#### this function is here solely for clarity - intended for internal use only 
  	function __reset_strings() {
    	#### the existing biblio & biblio_fields tables will be re-organized
    	#		 so here we simply prepare the skeletons
			$bibSql = "INSERT INTO `$tmpPrfx`.`biblio` "
							. "(`bibid`,`create_dt`,`last_change_dt`,`last_change_userid`,`material_cd`,`collection_cd`,`opac_flg`) "
							. "VALUES ";
			$fldSql = "INSERT INTO `$tmpPrfx`.`biblio_field` "
							. "(`bibid`,`fieldid`,`seq`,`tag`,`ind1_cd`,`ind2_cd`,`field_data`,`display`) "
							. "VALUES ";
			$subSql = "INSERT INTO `$tmpPrfx`.`biblio_subfield` "
							. "(`bibid`,`fieldid`,`subfieldid`,`seq`,`subfield_cd`,`subfield_data`) "
							. "VALUES ";
		} 
		
    $this->__freshInstall('en', false, '1.0b', $tmpPrfx);
    reset_strings();	// local function
						
		#### scan all existing biblio entries in biblio_id order
		$sql = "SELECT * FROM `$prfx`.`biblio` ORDER BY `bibid` ";
		$bibs = $this->select($sql);
		$n = 0; $fldid = 1; $subid = 1;

		while (($bib = $bibs->fetch_assoc()) != NULL) {
			# we will be posting to the db in blocks of 100 records to avoid server overload
			$n++;
			$bibSql .= '('.$bib[bibid].',"'.$bib[create_dt].'", "'.$bib[last_change_dt].'", "'
										.$bib[last_change_userid].'", "'.$bib[material_cd].'", "'
										.$bib[collection_cd].'", "'.$bib[opac_flg].'"),';

			### get those fields & sub-fields previosly kept in biblio table
			$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", "0", "245", NULL, NULL, NULL, NULL),';
			$bib[title] = preg_replace("/'/","\\'",$bib[title]);
			$bib[title] = preg_replace('/"/','\\"',$bib[title]);
 			$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[title].'"),'; $subid++;
			$bib[title_remainder] = preg_replace("/'/","\\'",$bib[title_remainder]);
			$bib[title_remainder] = preg_replace('/"/','\\"',$bib[title_remainder]);
			if ($bib[title_remainder]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "b", "'.$bib[title_remainder].'"),'; $subid++;}
			$bib[responsibility_stmt] = preg_replace("/'/","\\'",$bib[responsibility_stmt]);
			$bib[responsibility_stmt] = preg_replace('/"/','\\"',$bib[responsibility_stmt]);
			if ($bib[responsibility_stmt]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "c", "'.$bib[responsibility_stmt].'"),'; $subid++;}
  	  $fldid++;
  	  
			$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "100", NULL, NULL, NULL, NULL),';  
			$bib[author] = preg_replace("/'/","''",$bib[author]);
			$bib[author] = preg_replace("/\\\\/"," ",$bib[author]);
			if ($bib[author]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[author].'"),'; $subid++;}
	    $fldid++;
    
			$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "099", NULL, NULL, NULL, NULL),';
			if ($bib[call_nmbr1]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[call_nmbr1].'"),'; $subid++;}
	    $fldid++;

			$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "650", NULL, NULL, NULL, NULL),';  
			$bib[topic1] = preg_replace("/'/","''",$bib[topic1]);
			if ($bib[topic1]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[topic1].'"),'; $subid++;}
			$bib[topic2] = preg_replace("/'/","''",$bib[topic2]);
			if ($bib[topic2]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 1, "a", "'.$bib[topic2].'"),'; $subid++;}
			$bib[topic3] = preg_replace("/'/","''",$bib[topic3]);
			if ($bib[topic3]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 2, "a", "'.$bib[topic3].'"),'; $subid++;}
			$bib[topic4] = preg_replace("/'/","''",$bib[topic4]);
			if ($bib[topic4]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 3, "a", "'.$bib[topic4].'"),'; $subid++;}
			$bib[topic5] = preg_replace("/'/","''",$bib[topic5]);
			if ($bib[topic5]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 4, "a", "'.$bib[topic5].'"),'; $subid++;}
			$fldid++;
    
			### get each biblio_field entry for this biblio in MARC tag order
			$sql = "SELECT * FROM `$prfx`.`biblio_field` WHERE (`bibid`=$bib[bibid]) ORDER BY `tag` ";
			$flds = $this->select($sql);
			while ($fld = $flds->fetch_assoc()) {
			  $tag = sprintf("%03d",$fld[tag]);
				$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "'.$tag.'", NULL, NULL, NULL, NULL),';
				$fld[field_data] = preg_replace("/'/","''",$fld[field_data]);
				$fld[field_data] = preg_replace('/"/','',$fld[field_data]);
				$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "'.$fld[subfield_cd].'", "'.$fld[field_data].'"),'; $subid++;
	      $fldid++;
			}

			if ($n % 100 == 99) {
				# remove trailing ','
				$bibSql = substr($bibSql,0,-1);
				$fldSql = substr($fldSql,0,-1);
				$subSql = substr($subSql,0,-1);

				# process this batch
				$rslt = $db->Act($bibSql);	
				$rslt = $db->Act($fldSql);	
				$rslt = $db->Act($subSql);
			
				# interim progress report
				echo "$n biblio records written.<br />";
				echo "$fldid field records written.<br />";
				echo "$subid sub-field records written.<br />";
								
				__reset_strings();	// reset skeleton strings for next batch
			}
			//if ($n=1)break; ## for bebug only
		}
		
		### final results for main biblio tables
		echo "$n biblio records written.<br />";
		echo "$fldid field records written.<br />";
		echo "$subid sub-field records written.<br />";  
		#### =========== end of the biblio, biblio_field, biblio_subfield processing =========== ####
		
    $this->exec('insert into '.$tmpPrfx.'biblio_copy'
    						.'(`bibid`,`copyid`,`create_dt`,`barcode_nmbr`,`copy_desc`)'
								.' select  `bibid`,`copyid`,`create_dt`,`barcode_nmbr`,`copy_desc`'
								.' from biblio_copy');        
		$this->exec('insert into '.$tmpPrfx.'biblio_copy_fields'
								.' select * from biblio_copy_fields' );      
    // no change - biblio_copy_fields_dm
    // no change - biblio_hold
    // no change - biblio_status_dm
    $this->exec('insert into '.$tmpPrfx.'biblio_status_hist'
    						.' (`histid`,`bibid`,`copyid`,`status_cd`,`status_begin_dt`)'
                .' select NULL,`bibid`,`copyid`,`status_cd`,`status_begin_dt`'
								.' from biblio_status_hist' );
		// new table from full install - biblio_stock		
		// new table from full install - booking		
		// new table from full install - booking_member		
		// new table from full install - calendar		
		// new table from full install - calendar_dm		
		// new table from full install - collection_circ
		// new table from full install - collection_dist
		// new table from full install - collection_dm
    // no change - cutter
		// new table from full install - images
		$this->exec('replace into '.$tmpPrfx.'lookup_hosts'
								.' select * from lookup_hosts' );
		$this->exec('replace into '.$tmpPrfx.'lookup_settings'
								.' select * from lookup_settings' );
		// new table from full install - material_fields
		$this->exec('replace into '.$tmpPrfx.'material_type_dm'
								.' select * from material_type_dm' );
		$this->exec('replace into '.$tmpPrfx.'mbr_classify_dm'
								.' select * from mbr_classify_dm' );
		$this->exec('insert into '.$tmpPrfx.'member'
								.' select * from member' );
		$this->exec('insert into '.$tmpPrfx.'member_account'
								.' select * from member_account' );
		$this->exec('replace into '.$tmpPrfx.'member_fields'
								.' select * from member_fields' );
		$this->exec('replace into '.$tmpPrfx.'member_fields_dm'
								.' select * from member_fields_dm' );
		$this->exec('insert into '.$tmpPrfx.'member_fields_dm'
								.' select * from member_fields_dm' );
		// === start block
		$row = $this->select1('select * from settings');
		$sql = 'replace into '.$tmpPrfx.'settings'.'(`name`,`value`)';	
		foreach ($row as $key => $val) {
			$sql .= "(`$key`, '$val'),";
		}
		$this->exec($sql);
		// === end block
		// new table from full install - site
		$this->exec('insert into '.$tmpPrfx.'staff'
								.' select * from staff' );
		// new table from full install - state_dm
		$this->exec('replace into '.$tmpPrfx.'theme'
								.' select * from theme' );
		$this->exec('replace into '.$tmpPrfx.'transaction_type_dm'
								.' select * from transaction_type_dm' );
		// new table from full install - transentries
		// new table from full install - transkeys
		// new table from full install - translinksectionlocale
		// new table from full install - translocales
		// new table from full install - transsections
    // no change - usmarc_block_dm
    // no change - usmarc_indicator_dm
    // no change - usmarc_subfield_dm
    // no change - usmarc_tag_dm
					 
		#### ========= 1.0 table conversion complete ====================== ####
 	}
}
?>
