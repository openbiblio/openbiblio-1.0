<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../install/InstallQuery.php"));

class UpgradeQuery extends InstallQuery {
	function UpgradeQuery() {
		# Call query constructor so database connection gets made
		$this->Query();
	}
	function insertBiblioFields($tag, $subFieldCd, $fromTablePrfx, $toTablePrfx,  $colName){
		$sql = "insert into ".$toTablePrfx."biblio_field"
			."(bibid, fieldid, tag,   ind1_cd,ind2_cd,subfield_cd,     field_data) select "
			." bibid, null,  ".$tag.",null,   null,'".$subFieldCd."',".$colName
			." from ".$fromTablePrfx."biblio "
			."where ".$colName." is not null";
		$this->act($sql);
	 }

	 function copyDataToNewTable($tableName, $fromTablePrfx, $toTablePrfx, $sqlSelectConversion) {
		$sql = "delete from ".$toTablePrfx.$tableName;
		$this->act($sql);
		$conv = "(".implode(", ", array_keys($sqlSelectConversion)).") "
			. "select ".implode(", ", array_values($sqlSelectConversion));
		$sql = "insert into ".$toTablePrfx.$tableName." "
			.$conv
			." from ".$fromTablePrfx.$tableName;
		$this->act($sql);
	 }

	 function renamePrfxedTable($tableName, $fromTablePrfx, $toTablePrfx) {
		 return $this->renameTable($fromTablePrfx.$tableName, $toTablePrfx.$tableName);
	 }

	# Returns array($notices, $error).
	# On failure, $error is an Error and $notices should not be used.
	# On success, $error is NULL and $notices is an array of strings
	# notifying the user of upgrade changes.
	function performUpgrade_e($fromTablePrfx = DB_TABLENAME_PREFIX, $toTablePrfx = DB_TABLENAME_PREFIX) {
		# Each of these routines should update the given version to the next higher version.
		$upgrades = array(
			'0.3.0' => '_upgrade030_e',
			'0.4.0' => '_upgrade040_e',
			'0.4.0-mps' => '_upgrade040mps_e',
			'0.4.0-mps2' => '_upgrade040mps2_e',
			'0.5.2' => '_upgrade052_e',
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
				$error = new Error("No existing OpenBiblio database, please perform a fresh install.");
				return array(NULL, $error);
			} else {
				$error = new Error('Unknown database version: '.$version.'.  No automatic upgrade routine available.');
				return array(NULL, $error);
			}
		} while (1);
		return array($notices, NULL);
	}
	# Individual upgrade functions
	# Each of these should upgrade the indicated database version by one version.
	# $prfx is the table prefix to be used by both the original and upgraded databases.
	# $tmpPrfx is a prefix which may be used for temporary tables.
	# Return value is the same as performUpgrade_e()

	/* Upgrade 0.3.0 to 0.4.0 */
	function _upgrade030_e($prfx, $tmpPrfx) {
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
		$this->copyDataToNewTable("biblio", $prfx, $tmpPrfx, array(
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
			."(bibid,  copyid,copy_desc,barcode_nmbr,  status_cd, status_begin_dt, due_back_dt,   mbrid) select"
			." b.bibid,null,  null, b.barcode_nmbr,ifnull(bs.status_cd,'in'),ifnull(bs.status_begin_dt,b.create_dt),bs.due_back_dt,bs.mbrid from "
			.$prfx."biblio as b "
			."left join ".$prfx."biblio_status as bs on b.bibid=bs.bibid";
		$this->act($sql);

		$sql = "update ".$tmpPrfx."biblio_copy set status_cd = 'hld' where status_cd = 'cll'";
		$this->act($sql);

		$this->dropTable($prfx.'biblio');
		$this->dropTable($prfx.'biblio_copy');

		#collection_dm data conversion
		$this->copyDataToNewTable("collection_dm", $prfx, $tmpPrfx, array(
			"code" => "code",
			"description" => "description",
			"default_flg" => "default_flg",
			"days_due_back" => "days_due_back",
			"daily_late_fee" => "0.00",
		));

		$this->dropTable($prfx.'collection_dm');

		#member table conversion
		$this->copyDataToNewTable("member", $prfx, $tmpPrfx, array(
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
		$this->copyDataToNewTable("staff", $prfx, $tmpPrfx, array(
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
		$this->copyDataToNewTable("settings", $prfx, $tmpPrfx, array(
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
	/* Upgrade 0.4.0 to 0.5.2 */
	function _upgrade040_e($prfx, $tmpPrfx) {
		$settings = $this->select1('select * from '.$prfx.'settings ');
		if (is_dir('../locale/'.$settings['locale'].'/sql/0.5.2/domain')) {
			$domainDir = '../locale/'.$settings['locale'].'/sql/0.5.2/domain';
		} else {
			$domainDir = '../locale/en/sql/0.5.2/domain';
		}
		$this->act('alter table '.$prfx.'staff modify pwd char(32)');
		$this->act('update '.$prfx.'staff set pwd=md5(lower(username))');
		$this->act('alter table '.$prfx.'biblio_copy '
								. 'add renewal_count tinyint unsigned not null default 0 '
								. 'after mbrid ');
		$this->act('alter table '.$prfx.'biblio_status_hist '
								. 'add renewal_count tinyint unsigned not null default 0 '
								. 'after mbrid ');
		$this->executeSqlFile('../install/0.5.2/sql/checkout_privs.sql', $prfx);
		$this->act('insert into '.$prfx.'checkout_privs '
								. 'select mat.code material_cd, 1 classification, '
								. 'mat.adult_checkout_limit checkout_limit, '
								. '0 renewal_limit '
								. 'from material_type_dm mat ');
		$this->act('insert into '.$prfx.'checkout_privs '
								. 'select mat.code material_cd, 2 classification, '
								. 'mat.juvenile_checkout_limit checkout_limit, '
								. '0 renewal_limit '
								. 'from material_type_dm mat ');
		$this->act('alter table '.$prfx.'material_type_dm '
								. 'drop adult_checkout_limit, '
								. 'drop juvenile_checkout_limit ');
		$this->executeSqlFile('../install/0.5.2/sql/material_usmarc_xref.sql', $prfx);
		$this->act("update ".$prfx."mbr_classify_dm set code='1' where code='a' ");
		$this->act("update ".$prfx."mbr_classify_dm set code='2' where code='j' ");
		$this->act('alter table '.$prfx.'mbr_classify_dm '
								. 'modify code smallint auto_increment, '
								. 'add max_fines decimal(4,2) not null after default_flg ');
		$this->executeSqlFile('../install/0.5.2/sql/member_fields.sql', $prfx);
		$this->executeSqlFile('../install/0.5.2/sql/member_fields_dm.sql', $prfx);
		$this->act('insert '.$prfx.'member_fields '
								. "select mbrid, 'schoolGrade' code, school_grade data "
								. "from member where school_grade is not null "
								. "and school_grade <> '' ");
		$this->act('insert '.$prfx.'member_fields '
								. "select mbrid, 'schoolTeacher' code, school_teacher data "
								. "from member where school_teacher is not null "
								. "and school_teacher <> '' ");
		$this->executeSqlFile($domainDir.'/member_fields_dm.sql', $prfx);
		$this->act("update ".$prfx."member set classification=1 where classification='a' ");
		$this->act("update ".$prfx."member set classification=2 where classification='j' ");
		$this->act('alter table '.$prfx.'member '
								. 'add address text null after first_name, '
								. 'modify classification smallint not null ');
		# What a mess
		$this->act('update '.$prfx.'member set address= '
								. "concat_ws('\n', nullif(address1, ''), nullif(address2, ''), "
								. "concat_ws('', city, concat(', ', nullif(state, '')), "
								. "concat(' ', nullif(zip, ''), ifnull(concat('-', zip_ext), '')))) ");
		$this->act('alter table '.$prfx.'member '
								. 'drop address1, drop address2, '
								. 'drop city, drop state, drop zip, drop zip_ext, '
								. 'drop school_grade, drop school_teacher ');
		$this->act('alter table '.$prfx.'settings '
								. 'add hold_max_days smallint not null '
								. 'after block_checkouts_when_fines_due ');
		$this->act('update '.$prfx.'settings '
								. 'set hold_max_days=14 ');
		$this->act('drop table '.$prfx.'state_dm ');

		$this->act('update '.$prfx.'settings set version=\'0.5.2\'');
		$notices = array('All staff passwords have been reset to be equal to the corresponding usernames.');
		return array($notices, NULL); # no error
	}
	/* Upgrade 0.5.2 to 0.6.0 */
	function _upgrade052_e($prfx, $tmpPrfx) {
		$this->act('alter table '.$prfx.'biblio_copy '
								. 'add create_dt datetime not null '
								. 'after copyid ');
		$this->act('update biblio_copy bc, biblio b '
								. 'set bc.create_dt=b.create_dt '
								. 'where b.bibid=bc.bibid ');
		$this->act("update settings set version='0.6.0'");
		$notices = array();
		return array($notices, NULL);
	}
	/* Upgrade 0.4.0-mps to 0.4.0-mps2 */
	function _upgrade040mps_e($prfx, $tmpPrfx) {
		$locale = $this->select1("SELECT * FROM ".$prfx."settings WHERE name='locale' ");
		if (is_dir('../locale/'.$settings['value'].'/sql/0.4.0-mps2/domain')) {
			$domainDir = '../locale/'.$settings['locale'].'/sql/0.4.0-mps2/domain';
		} else {
			$domainDir = '../locale/en/sql/0.4.0-mps2/domain';
		}
		$this->executeSqlFile('../install/0.4.0-mps2/sql/biblio_stock.sql', $prfx);
		$this->executeSqlFile('../install/0.4.0-mps2/sql/collection_circ.sql', $prfx);
		$this->executeSqlFile('../install/0.4.0-mps2/sql/collection_dist.sql', $prfx);
		$this->executeSqlFile('../install/0.4.0-mps2/sql/report_displays.sql', $prfx);
		$this->executeSqlFile($domainDir.'/report_displays.sql', $prfx);
		$this->act('INSERT INTO '.$prfx.'collection_circ '
								. 'SELECT code, days_due_back, daily_late_fee '
								. 'FROM '.$prfx.'collection_dm ');
		$this->act('ALTER TABLE '.$prfx.'collection_dm '
								. "ADD type enum('Circulated','Distributed') DEFAULT 'Circulated' NOT NULL "
								. 'AFTER default_flg, '
								. 'DROP days_due_back, '
								. 'DROP daily_late_fee ');
		$this->act("UPDATE settings SET value='0.4.0-mps2' WHERE name='version'");
		$notices = array();
		return array($notices, NULL);
	}
	/* Upgrade 0.4.0-mps2 to 0.4.0-mps3 */
	function _upgrade040mps2_e($prfx, $tmpPrfx) {
		$this->act('ALTER TABLE '.$prfx.'member '
								. "ADD password CHAR(32) DEFAULT '' NOT NULL ");
		$this->act('ALTER TABLE '.$prfx.'site '
								. "ADD delivery_note TEXT DEFAULT '' NOT NULL ");
		$this->act("UPDATE ".$prfx."settings SET value='0.4.0-mps3' WHERE name='version'");
		$notices = array();
		return array($notices, NULL);
	}
	function _upgrade040mps3_e($prfx, $tmpPrfx) {
		# FIXME -- not done yet
		# delete session table
	}
	function _upgrade100_e($prfx,tmpPrfx) {
		# FIXME -- not done yet by a LONG ways
		### ################################################### ###
		### mods made to wip structure for F.L. software adds/mods
		### ################################################### ###
		$this->act('ALTER TABLE '.$prfx.'staff '
							. "ADD tools_flg CHAR(1) DEFAULT '' NOT NULL ");
 		$this->act("ALTER TABLE ".$prfx."`settings` "
		 					."ADD `menu` ENUM('admin','tools','none') NOT NULL DEFAULT 'admin'");
		$this->act("INSERT INTO ".$prfx."`settings` ".
							."(`name`,`position`,`title`,`type`,`width`,`type_data`,`validator`,`value`,`menu`)"
							."VALUES "
							."('plugins_list',NULL,NULL,'text',NULL,NULL,NULL,NULL,'none'),"
							."('allow_plugins_flg',NULL,'Allow Plugins','bool',NULL,NULL,NULL,'Y','tools'),"
							."('item_autoBarcode_flg', NULL , 'Item Auto Barcodes', 'bool', NULL , NULL , NULL , 'Y', 'tools'),"
							."('mbr_autoBarcode_flg', NULL , 'Member Auto Barcodes', 'bool', NULL , NULL , NULL , 'Y', 'tools'),"
							."('item_barcode_flg','NULL','Use item barcodes','bool',NULL,NULL,NULL,'N','tools'),"
							."('mbr_barcode_flg',NULL,'Use Member barcodes','bool',NULL,NULL,NULL,'N','tools'),"
							."('show_checkout_mbr',NULL,'Show member who has an item checkout','bool',NULL,NULL,NULL,'N','tools'),"
							."('show_detail_opac',NULL,'Show copy details in OPAC','bool',NULL,NULL,NULL,'N','tools'),"
							."('multi_site_func',NULL,'Default site for multiple site functionality (0 = disabled)','int',NULL,NULL,NULL,'0','tools'),"
							."('show_item_photos',NULL,'Show Item Photos','bool',NULL,NULL,NULL,'N','tools')");
		$this->act("ALTER TABLE `member` "
							."CHANGE `create_dt` `create_dt` DATETIME NOT NULL ,"
							."CHANGE `last_change_dt` `last_change_dt` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL");
		$this->act("ALTER TABLE `member` "
							."ADD `address2` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `address1` ,"
							."ADD `city` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `address2` ,"
							."ADD `state` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `city` ,"
							."ADD `zip` VARCHAR( 10 ) NULL DEFAULT NULL AFTER `state` ,"
							."ADD `zip_ext` VARCHAR( 10 ) NULL DEFAULT NULL AFTER `zip` ");
		$this->act("ALTER TABLE `biblio_copy` "
							."ADD  `siteid` TINYINT( 3 ) NOT NULL DEFAULT  '1'");
							 
		### ################################################### ###
		### conversion process begins here.
		### ################################################### ###
    //------------------------//

		#### see also module convert06To10.php to copy legacy biblio data to new structure

		## Admin support tables
		$sql = "INSERT INTO `openbibliowork`.`theme` "
					."SELECT * "
					."  FROM `openbiblio`.`theme` "
					;
    $this->act($sql);
		## Biblio support tables
		$sql = "INSERT INTO `openbibliowork`.`collection_dm` "
					."(`code`,`description`,`default_flg`,`type`)"
					."SELECT `code`,`description`,`default_flg`,'Circulated' "
					."  FROM `openbiblio`.`collection_dm` "
					;
    $this->act($sql);
    //-------------------------//
		$sql = "INSERT INTO `openbibliowork`.`material_type_dm` "
					."(`code`,`description`,`default_flg`,`adult_checkout_limit`,`juvenile_checkout_limit`,`image_file`)"
					."SELECT `code`,`description`,`default_flg`,'10','5',`image_file` "
					."  FROM `openbiblio`.`material_type_dm` "
					;
    $this->act($sql);
    //-------------------------//
		$sql = "INSERT INTO `openbibliowork`.`biblio` "
					."(`bibid`,`create_dt`,`last_change_dt`,`last_change_userid`,`material_cd`,`collection_cd`,`opac_flg`)"
					."SELECT `bibid`,`create_dt`,`last_change_dt`,`last_change_userid`,`material_cd`,`collection_cd`,`opac_flg` "
					."  FROM `openbiblio`.`biblio` "
					;
    $this->act($sql);
    //-------------------------//
		$sql = "INSERT INTO `openbibliowork`.`biblio_field` "
					."(`bibid`,`fieldid`,`seq`,`tag`,`ind1_cd`,`ind2_cd`,`field_data`,`display`)"
					."SELECT `bibid`,`fieldid`,NULL,`tag`,`ind1_cd`,`ind2_cd`,`field_data`,NULL "
					."  FROM `openbiblio`.`biblio_field` "
					;
    $this->act($sql);
		//-------------------------//
		#### this is not ready! there are several tables involved which I cannot decode - FL
		$sql = "Insert into `openbibliowork`.`biblio_copy` "
		      ."(`bibid`,`copyid`,`create_dt`,`last_change_dt`,`last_change_userid`,"
					." `barcode_nmbr`,`copy_desc`,`vendor`,`fund`,`price`,`experation`,`histid`,`siteid`)"
		      ."SELECT `bibid`,`copyid`,`create_dt`,NULL,NULL,`barcode_nmbr`,NULL,NULL,NULL,NULL,NULL,NULL,'3' "
		      ."  FROM `openbiblio`.`biblio_copy`"
		      ;
    $this->act($sql);
		//-------------------------//
		## member loan priveleges denied support
    $sql = "INSERT INTO `openbibliowork`.`mbr_classify_dm` "
					."(`code`, `description`, `default_flg`) VALUES ('3', 'denied', 'N')"
					;
	  $this->act($sql);
		//-------------------------//
		## aditional tool setting
	  $sql = "INSERT INTO `openbibliowork`.`settings` "
					."(`name` ,`position` ,`title` ,`type` ,`width` ,`type_data` ,`validator` ,`value` ,`menu`)"
					."VALUES "
					."('site_login', '25', 'Site Logon', 'bool', NULL , NULL , NULL , '''N''', 'tool'),"
					."('checkout_interval','26','Checkout_Interval','select',NULL,NULL,NULL,'Days','tools')"
					."('item_barcode_width', '27', 'Item Barcode Width', 'int', NULL, NULL, NULL, 13, 'tools')"
     			;
	  $this->act($sql);
		//-------------------------//

		}  //function

	} //class

