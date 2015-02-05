<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	
	switch ($_REQUEST['cat']) {
		case 'collect':
			require_once(REL(__FILE__, "../model/Collections.php"));
			# can't set $ptr here as there are three classes used in this model.
			break;
		case 'copyFlds':
			require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
			$ptr = new BiblioCopyFields;
			break;
		case 'hosts':
			require_once(REL(__FILE__, "../model/Online.php"));
			$ptr = new Hosts;
			break;
		case 'media':
			require_once(REL(__FILE__, "../model/MediaTypes.php"));
			$ptr = new MediaTypes;
			break;
		case 'mbrTypes':
			require_once(REL(__FILE__, "../model/MemberTypes.php"));
			$ptr = new MemberTypes;
			break;
		case 'mbrFlds':
			require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
			$ptr = new MemberCustomFields_DM;
			break;
		case 'opts':
			require_once(REL(__FILE__, "../model/Online.php"));
			$ptr = new Opts;
			break;
		case 'settings':
			require_once(REL(__FILE__, "../model/Settings.php"));
			$ptr = new Settings;
			break;
		case 'sites':
			require_once(REL(__FILE__, "../model/Sites.php"));
			$ptr = new Sites;
			require_once(REL(__FILE__, "../model/Calendars.php"));
			$ptr1 = new Calendars;
			## deliberate fall-through, do not remove
		case 'states':
			require_once(REL(__FILE__, "../model/States.php"));
			$ptr2 = new States;
			break;
		case 'staff':
			require_once(REL(__FILE__, "../model/Staff.php"));
			$ptr = new Staff;
			break;
		case 'themes':
			require_once(REL(__FILE__, "../model/Themes.php"));
			$ptr = new Themes;
			require_once(REL(__FILE__, "../model/Settings.php"));
			$ptr2 = new Settings;
			break;
		default:
		  echo "<h4>invalid category: &gt;".$_REQUEST['cat']."&lt;</h4><br />";
		  exit;
			break;
	}

	$updtSuccess = T("Update successful");
	
	switch ($_REQUEST['mode']){
		## don't combine this switch with that above.
		## doing so would require multiple 'switch' statements,
		## as well as multiple 'default' blocks 

	  #-.-.-.-.-.- Calendars -.-.-.-.-.-.-
		case 'getAllCalendars':
		  $cals = array();
			$set = $ptr1->getAll('description');
			while ($row = $set->fetch_assoc()) {
			  $cals[] = $row;
			}
			echo json_encode($cals);
			break;

	  #-.-.-.-.-.- Collections -.-.-.-.-.-.-
		case 'getCirc_collect':
			$ptr = new CircCollections;
		  $colls = array();
			$set = $ptr->getAll('code');
			while ($row = $set->fetch_assoc()) {
			  $colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'getDist_collect':
			$ptr = new DistCollections;
		  $colls = array();
			$set = $ptr->getAll('code');
			while ($row = $set->fetch_assoc()) {
			  $colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'getType_collect':
			$ptr = new Collections;
			echo json_encode($ptr->getTypeSelect());
			break;
		case 'getAll_collect':
			$ptr = new Collections;
		  $colls = array();
			$set = $ptr->getAllWithStats();
			while ($row = $set->fetch_assoc()) {
			  $colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'addNew_collect':
			$ptr = new Collections;
			$col = array(
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'type'=>$_POST["type"],
				'days_due_back'=>$_POST["days_due_back"],
				'daily_late_fee'=>$_POST["daily_late_fee"],
				'restock_threshold'=>$_POST["restock_threshold"],
			);
			list($id, $errors) = $ptr->insert_el($col);
			if (empty($errors)) {
				$msg = T("Collection, %desc%, has been added.", array('desc'=>H($col['description'])));
				echo $msg;
			}
			break;
		case 'update_collect':
			$ptr = new Collections;
			$coll = array(
				'code'=>$_POST["code"],
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'type'=>$_POST["type"],
				'days_due_back'=>$_POST["days_due_back"],
				'daily_late_fee'=>$_POST["daily_late_fee"],
				'restock_threshold'=>$_POST["restock_threshold"],
			);
			$errors = $ptr->update_el($coll);
			if (empty($errors)) {
				$msg = T("Collection, %desc%, has been updated.", array('desc'=>H($coll['description'])));
			}
			echo $msg;
			break;
		case 'd-3-L-3-t_collect':
			$ptr = new Collections;
			$ptr->deleteOne($_POST['code']);
			$msg = T("Collection, %desc%, has been deleted.", array('desc'=>$description));
			echo $msg;
			break;
			
	  #-.-.-.-.-.-Custom Copy Fields -.-.-.-.-.-.-
		case 'getAll_copyFlds':
			$flds = array();
			$set = $ptr->getAll();
			while ($row = $set->fetch_assoc()) {
			  $flds[] = $row;
			}
			echo json_encode($flds);
			break;
		case 'addNew_copyFlds':
			list($id, $errs) = $ptr->insert_el(array(
				'code'=>@$_POST['code'],
				'description'=>@$_POST['description'],
			));
			if ($errs) {echo $errs;} else {echo T("Add New successful");}
			break;
		case 'update_copyFlds':
			$errs = $ptr->update_el(array(
				'code'=>@$_POST["code"],
				'description'=>@$_POST["description"],
			));
			if ($errs) {echo $errs;} else {echo $updtSuccess;}
			break;
		case 'd-3-L-3-t_copyFlds':
			$ptr->deleteOne($_POST[code]);
			if ($errs) {echo $errs;} else {echo T("Delete completed");}
			break;

	  #-.-.-.-.-.- Custom Member Fields -.-.-.-.-.-.-
		case 'getAll_mbrFlds':
			$flds = array();
			$set = $ptr->getAll();
			while ($row = $set->fetch_assoc()) {
			  $flds[] = $row;
			}
			echo json_encode($flds);
			break;
		case 'addNew_mbrFlds':
			list($id, $errs) = $ptr->insert_el(array(
				'code'=>@$_POST['code'],
				'description'=>@$_POST['description'],
			));
			if ($errs) {echo $errs;} else {echo T("Add New successful");}
			break;
		case 'update_mbrFlds':
			$errs = $ptr->update_el(array(
				'code'=>@$_POST["code"],
				'description'=>@$_POST["description"],
			));
			if ($errs) {echo $errs;} else {echo $updtSuccess;}
			break;
		case 'd-3-L-3-t_mbrFlds':
			$ptr->deleteOne($_POST[code]);
			if ($errs) {echo $errs;} else {echo T("Delete completed");}
			break;

	  #-.-.-.-.-.- Member Type Classification -.-.-.-.-.-.-
		case 'getAll_mbrTypes':
			$flds = array();
			$set = $ptr->getAll();
			while ($row = $set->fetch_assoc()) {
			  $flds[] = $row;
			}
			echo json_encode($flds);
			break;
		case 'addNew_mbrTypes':
			list($id, $errs) = $ptr->insert_el(array(
				'code'=>@$_POST['code'],
				'max_fines'=>@$_POST['max_fines'],
				'default_flg'=>@$_POST['default_flg'],
				'description'=>@$_POST['description'],
			));
			if ($errs) {echo $errs;} else {echo T("Add New successful");}
			break;
		case 'update_mbrTypes':
			$errs = $ptr->update_el(array(
				'code'=>@$_POST["code"],
				'max_fines'=>@$_POST['max_fines'],
				'default_flg'=>@$_POST['default_flg'],
				'description'=>@$_POST["description"],
			));
			if ($errs) {echo $errs;} else {echo $updtSuccess;}
			break;
		case 'd-3-L-3-t_mbrTypes':
			$ptr->deleteOne($_POST[code]);
			if ($errs) {echo $errs;} else {echo T("Delete completed");}
			break;

  	#-.-.-.-.-.- Media Types -.-.-.-.-.-.-
		case 'getAll_media':
			$med = array();
			$set = $ptr->getAllWithStats();
			while ($row = $set->fetch_assoc()) {
			  $med[] = $row;
			}
			echo json_encode($med);
			break;
		case 'addNew_media':
			$type = array(
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'adult_checkout_limit'=>$_POST["adult_checkout_limit"],
				'juvenile_checkout_limit'=>$_POST["juvenile_checkout_limit"],
				'image_file'=>$_POST["image_file"],
        'srch_disp_lines'=>$_POST["srch_disp_lines"],
				);
			list($id, $errors) = $ptr->insert_el($type);
			if (empty($errors)) {
				$msg = T("Media Type")." '".H($type['description'])."' ".T("has been added");
				echo $msg;
			}
			break;
		case 'update_media':
			if (strpos($_POST["image_file"],'\\')) {
				$imgInfo = pathinfo($_POST["image_file"]);
				$imgStuff = explode('\\',$imgInfo['filename']);
				$imgFile = $imgStuff[2].'.'.$imgInfo['extension'];
			} else {
				$imgFile = $_POST["image_file"];
			}
			$type = array(
				'code'=>$_POST["code"],
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'adult_checkout_limit'=>$_POST["adult_checkout_limit"],
				'juvenile_checkout_limit'=>$_POST["juvenile_checkout_limit"],
				'image_file'=>$imgFile,
        'srch_disp_lines'=>$_POST["srch_disp_lines"],
			);
			$errors = $ptr->update_el($type);
			if (empty($errors)) {
				$msg = T("Media Type")." '".H($type['description'])."' ".T("has been updated");
				echo $msg;
			}
			break;
		case 'd-3-L-3-t_media':
			$code = $_POST["code"];
			$description = $_POST["desc"];
			$ptr->deleteOne($code);
			$msg = T("Media Type")." '".H($type['description'])."' ".T("has been deleted");
			echo $msg;
			break;
				
  	#-.-.-.-.-.- Online Hosts -.-.-.-.-.-.-
		case 'getAll_hosts':
		  $hosts = array();
			$set = $ptr->getAll('seq');
			while ($row = $set->fetch_assoc()) {
			  $hosts[] = $row;
			}
			echo json_encode($hosts);
			break;
		case 'getSvcs_hosts':
			$svcs = array('Z3950','SRU','SRW');
			echo json_encode($svcs);
			break;
		case 'addNew_hosts':
			if (empty($_POST[active])) $_POST[active] = 'N';
			echo $ptr->insert($_POST);
			break;
		case 'update_hosts':
			if (empty($_POST[active])) $_POST[active] = 'N';
			echo $ptr->update($_POST);
			break;
		case 'd-3-L-3-t_hosts':
			$sql = "DELETE FROM $ptr->name WHERE `id`=$_POST[id]";
			echo $ptr->act($sql);
			break;

	  #-.-.-.-.-.- Online Options -.-.-.-.-.-.-
		case 'getOpts':
	  	$opts = array();
			$set = $ptr->getAll();
			$row = $set->fetch_assoc();
			echo json_encode($row);
			break;
		case 'updateOpts':
		  $_POST[id] = 1;
			if (empty($_POST[autoDewey])) $_POST[autoDewey] = 'n';
			if (empty($_POST[defaultDewey])) $_POST[defaultDewey] = 'n';
			if (empty($_POST[autoCutter])) $_POST[autoCutter] = 'n';
			if (empty($_POST[autoCollect])) $_POST[autoCollect] = 'n';
			$rslt = $ptr->update($_POST);
			if(empty($rslt)) $rslt = '1';
			echo $rslt;
			break;

	  #-.-.-.-.-.- Settings -.-.-.-.-.-.-
		case 'getFormData':
			echo json_encode($ptr->getFormData ('admin','name,title,type,value'));
			break;
		case 'update_settings':
			echo $ptr->setAll_el($_REQUEST);
			break;

	  #-.-.-.-.-.- Sites -.-.-.-.-.-.-
		case 'getAll_sites':
		  $sites = array();
			$set = $ptr->getAll('name');
			while ($row = $set->fetch_assoc()) {
			  $sites[] = $row;
			}
			echo json_encode($sites);
			break;
		case 'addNew_sites':
			echo $ptr->insert($_REQUEST);
			break;
		case 'update_sites':
			echo $ptr->update($_REQUEST);
			break;
		case 'd-3-L-3-t_sites':
			echo $ptr->deleteOne($_POST['siteid']);
			break;

	  #-.-.-.-.-.- Staff -.-.-.-.-.-.-
		case 'getAll_staff':
		  $staff = array();
			$set = $ptr->getAll('last_name');
			while ($row = $set->fetch_assoc()) {
			  $staff[] = $row;
			}
			echo json_encode($staff);
			break;
		case 'addNew_staff':
		case 'update_staff':
			foreach (array('suspended','admin','circ','circ_mbr','catalog','reports','tools') as $flg) {
				if (isset($_POST[$flg.'_flg'])) {
					$_POST[$flg.'_flg'] = 'Y';
				} else {
					$_POST[$flg.'_flg'] = 'N';
				}
			}
			if ($_POST['mode'] == 'addNew_staff')
				echo $ptr->insert_el($_POST);
			else {
				$_POST[pwd2] = $_POST[pwd]; // no PW changes allowed in update screen
				echo $ptr->update($_POST);
			}
			break;
		case 'd-3-L-3-t_staff':
			echo $ptr->deleteOne($_POST['userid']);
			break;
		case 'setPwd_staff':
			$rec = array('userid'=>$_POST['userid'], 'pwd'=>$_POST['pwd'], 'pwd2'=>$_POST['pwd2']);
			$errs = $ptr->update_el($rec);
			if ($errs) 
				echo $errs;
			else
				echo T("Password has been reset.");
			break;

	  #-.-.-.-.-.- States / Provinces -.-.-.-.-.-.-
		case 'getAll_states':
		  $states = array();
			$set = $ptr2->getAll('description');
			while ($row = $set->fetch_assoc()) {
			  $states[] = $row;
			}
			echo json_encode($states);
			break;
		case 'addNew_states':
			echo $ptr2->insert($_REQUEST);
			break;
		case 'update_states':
			echo $ptr2->update($_REQUEST);
			break;
		case 'd-3-L-3-t_states':
			echo $ptr2->deleteOne($_POST['code']);
			break;

	  #-.-.-.-.-.- Themes -.-.-.-.-.-.-
		case 'getAllThemes':
		  $thms = array();
			$set = $ptr->getAll('theme_name');
			while ($row = $set->fetch_assoc()) {
			  $thms[] = $row;
			}
			echo json_encode($thms);
			break;
		case 'getThemeDirs':
			echo json_encode($ptr2->getThemeDirs ());
			break;
		case 'setCrntTheme':
			echo $ptr2->setOne_el('themeid', $_POST['themeid']);
			break;
		case 'addNewTheme':
			echo $ptr->insert_el($_POST);
			break;
		case 'updateTheme':
			echo $ptr->update_el($_POST);
			break;
		case 'd-3-L-3-tTheme':
			echo $ptr->deleteOne($_POST['themeid']);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>".T("invalid mode").": &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
