<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Online.php"));
	require_once(REL(__FILE__, "../model/Settings.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));
	require_once(REL(__FILE__, "../model/Staff.php"));
	require_once(REL(__FILE__, "../model/States.php"));
	require_once(REL(__FILE__, "../model/Themes.php"));

	switch ($_REQUEST[mode]){
		case 'getCircList':
			$ptr = new CircCollections;
		  $colls = array();
			$set = $ptr->getAll('code');
			while ($row = $set->next()) {
			  $colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'getDistList':
			$ptr = new DistCollections;
		  $colls = array();
			$set = $ptr->getAll('code');
			while ($row = $set->next()) {
			  $colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'getTypes':
			$ptr = new Collections;
			echo json_encode($ptr->getTypeSelect());
			break;
		case 'getAllCollections':
			$ptr = new Collections;
		  $colls = array();
			$set = $ptr->getAllWithStats();
			while ($row = $set->next()) {
			  $colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'addNewCollection':
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
		case 'updateCollection':
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
		case 'd-3-L-3-tCollections':
			$ptr = new Collections;
			$ptr->deleteOne($_POST['code']);
			$msg = T("Collection, %desc%, has been deleted.", array('desc'=>$description));
			echo $msg;
			break;
			
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getAllMedia':
			$ptr = new MediaTypes;
			$mtls = array();
			$set = $ptr->getAllWithStats();
			while ($row = $set->next()) {
			  $mtls[] = $row;
			}
			echo json_encode($mtls);
			break;
		case 'addNewMedia':
			$ptr = new MediaTypes;
			$type = array(
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'adult_checkout_limit'=>$_POST["adult_checkout_limit"],
				'juvenile_checkout_limit'=>$_POST["juvenile_checkout_limit"],
				'image_file'=>$_POST["image_file"],
				);
			list($id, $errors) = $ptr->insert_el($type);
			if (empty($errors)) {
				$msg = T("Material type, %desc%, has been added.", array('desc'=>H($type['description'])));
				echo $msg;
			}
			break;
		case 'updateMedia':
			$ptr = new MediaTypes;
			$type = array(
				'code'=>$_POST["code"],
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'adult_checkout_limit'=>$_POST["adult_checkout_limit"],
				'juvenile_checkout_limit'=>$_POST["juvenile_checkout_limit"],
				'image_file'=>$_POST["image_file"],
			);
			$errors = $ptr->update_el($type);
			if (empty($errors)) {
				$msg = T("Material type, %desc%, has been updated.", array('desc'=>H($type['description'])));
				echo $msg;
			}
			break;
		case 'd-3-L-3-tMedia':
			$code = $_POST["code"];
			$description = $_POST["desc"];
			$ptr = new MediaTypes;
			$ptr->deleteOne($code);
			$msg = T("Material type, %desc%, has been deleted.", array('desc'=>$description));
			echo $msg;
			break;
				
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getHosts':
			$hptr = new Hosts;
		  $hosts = array();
			$hSet = $hptr->getAll('seq');
			while ($row = $hSet->next()) {
			  $hosts[] = $row;
			}
			echo json_encode($hosts);
			break;
		case 'addNewHost':
			$hptr = new Hosts;
			if (empty($_POST[active])) $_POST[active] = 'n';
			echo $hptr->insert($_POST);
			break;
		case 'updateHost':
			$hptr = new Hosts;
			if (empty($_POST[active])) $_POST[active] = 'n';
			echo $hptr->update($_POST);
			break;
		case 'd-3-L-3-tHost':
			$hptr = new Hosts;
			$key = $hptr->key;
			$sql = "DELETE FROM $hptr->name WHERE `id`=$_GET[id]";
			echo $hptr->db->act($sql);
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getOpts':
			$optr = new Opts;
	  	$opts = array();
			$oSet = $optr->getAll();
			$row = $oSet->next();
			echo json_encode($row);
			break;
		case 'updateOpts':
			$optr = new Opts;
		  $_POST[id] = 1;
			if (empty($_POST[autoDewey])) $_POST[autoDewey] = 'n';
			if (empty($_POST[defaultDewey])) $_POST[defaultDewey] = 'n';
			if (empty($_POST[autoCutter])) $_POST[autoCutter] = 'n';
			if (empty($_POST[autoCollect])) $_POST[autoCollect] = 'n';
			$rslt = $optr->update($_POST);
			if(empty($rslt)) $rslt = '1';
			echo $rslt;
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getAllSites':
			$sptr = new Sites;
		  $sites = array();
			$sSet = $sptr->getAll('name');
			while ($row = $sSet->next()) {
			  $sites[] = $row;
			}
			echo json_encode($sites);
			break;
		case 'addNewSite':
			$sptr = new Sites;
			echo $sptr->insert($_REQUEST);
			break;
		case 'updateSite':
			$sptr = new Sites;
			echo $sptr->update($_REQUEST);
			break;
		case 'd-3-L-3-tSite':
			$sptr = new Sites;
			echo $sptr->deleteOne($_REQUEST);
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getAllStaff':
			$ptr = new Staff;
		  $staff = array();
			$set = $ptr->getAll('last_name');
			while ($row = $set->next()) {
			  $staff[] = $row;
			}
			echo json_encode($staff);
			break;
		case 'addNewStaff':
			foreach (array('suspended','admin','circ','circ_mbr','catalog','reports','tools') as $flg) {
				if (isset($_POST[$flg.'_flg'])) {
					$_POST[$flg.'_flg'] = 'Y';
				} else {
					$_POST[$flg.'_flg'] = 'N';
				}
			}
			$ptr = new Staff;
			echo $ptr->insert_el($_POST);
			break;
		case 'updateStaff':
			$ptr = new Staff;
			echo $ptr->update_el($_POST);
			break;
		case 'd-3-L-3-tStaff':
			$ptr = new Staff;
			echo $ptr->deleteOne($_POST['userid']);
			break;
		case 'setStaffPwd':
			$ptr = new Staff;
			$rec = array('userid'=>$_POST['userid'], 'pwd'=>$_POST['pwd'], 'pwd2'=>$_POST['pwd2']);
			echo $ptr->update_el($rec);
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getAllStates':
			$ptr = new States;
		  $states = array();
			$set = $ptr->getAll('description');
			while ($row = $set->next()) {
			  $states[] = $row;
			}
			echo json_encode($states);
			break;
		case 'addNewState':
			$ptr = new States;
			echo $ptr->insert($_REQUEST);
			break;
		case 'updateState':
			$ptr = new States;
			echo $ptr->update($_REQUEST);
			break;
		case 'd-3-L-3-tState':
			$ptr = new States;
			echo $ptr->deleteOne($_REQUEST);
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getAllThemes':
			$ptr = new Themes;
		  $thms = array();
			$set = $ptr->getAll('theme_name');
			while ($row = $set->next()) {
			  $thms[] = $row;
			}
			echo json_encode($thms);
			break;
		case 'setCrntTheme':
			$ptr = new Settings;
			echo $ptr->setOne_el('themeid', $_POST['themeid']);
			break;
		case 'addNewTheme':
			$ptr = new Themes;
			echo $ptr->insert_el($_POST);
			break;
		case 'updateTheme':
			$ptr = new Themes;
			echo $ptr->update_el($_POST);
			break;
		case 'd-3-L-3-tTheme':
			$ptr = new Themes;
			echo $ptr->deleteOne($_POST['themeid']);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
