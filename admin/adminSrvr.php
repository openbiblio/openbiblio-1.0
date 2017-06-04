<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");

	ini_set('display_errors', 1);

	switch ($_POST['cat']) {
		case 'collect':
			require_once(REL(__FILE__, "../model/Collections.php"));
			# can't set $ptr here as there are three classes used in this model.
			break;
		case 'copyFlds':
			require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
			$ptr = new BiblioCopyFields;
			break;
		case 'hosts':
			require_once(REL(__FILE__, "../model/Hosts.php"));
			$ptr = new Hosts;
			break;
		case 'hours':
			require_once(REL(__FILE__, "../model/OpenHours.php"));
			$ptr = new OpenHours;
			break;
        case 'integrity':
            require_once(REL(__FILE__, "../classes/Integrity.php"));
            $ptr = new Integrity;
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
			require_once(REL(__FILE__, "../model/MemberCustomFields_DM.php"));
			$ptr = new MemberCustomFields_DM;
			break;
		case 'opts':
			require_once(REL(__FILE__, "../model/Opts.php"));
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
		  echo "<h4>invalid category: &gt;".$_POST['cat']."&lt;</h4><br />";
		  exit;
			break;
	}

	$updtSuccess = T("Update successful");
    $deleteComplete = T("Delete completed");
	
	switch ($_POST['mode']){
		## don't combine this switch with that above.
		## doing so would require multiple, nested, 'switch' statements,
		## as well as multiple 'default' blocks 

	  #-.-.-.-.-.- Calendars -.-.-.-.-.-.-
		case 'getAllCalendars':
		  $cals = array();
			$set = $ptr1->getAll('description');
			//while ($row = $set->fetch_assoc()) {
            		foreach ($set as $row) {
				$cals[] = $row;
			}
			echo json_encode($cals);
			break;

	  #-.-.-.-.-.- Collections -.-.-.-.-.-.-
		case 'getCirc_collect':
			$ptr = new CircCollections;
		    	$colls = array();
			$set = $ptr->getAll('code');
            		foreach ($set as $row) {
				$colls[] = $row;
			}
			echo json_encode($colls);
			break;
		case 'getDist_collect':
			$ptr = new DistCollections;
		    	$colls = array();
			$set = $ptr->getAll('code');
            		foreach ($set as $row) {
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
            		foreach ($set as $row) {
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
				'minutes_due_back'=>$_POST["minutes_due_back"],
				'regular_late_fee'=>$_POST["regular_late_fee"],
				'restock_threshold'=>$_POST["restock_threshold"],
				'due_date_calculator'=>$_POST['due_date_calculator'],
				'important_date'=>$_POST['important_date'],
				'important_date_purpose'=>$_POST['important_date_purpose'],
				'number_of_minutes_between_fee_applications'=>$_POST['number_of_minutes_between_fee_applications'],
				'number_of_minutes_in_grace_period'=>$_POST['number_of_minutes_in_grace_period'],
				'pre_closing_padding'=>$_POST['pre_closing_padding'],
			);
			list($id, $errors) = $ptr->insert_el($col);
			if (empty($errors)) {
				$msg = "Success - '".$_POST["description"]."' ".T("Collection has been added.");
			} else {
				$msg = $errors;
			}
			echo json_encode($msg);
			break;
		case 'update_collect':
			$ptr = new Collections;
			$coll = array(
				'description'=>$_POST["description"],
				'default_flg'=>$_POST['default_flg'],
				'type'=>$_POST["type"],
				'days_due_back'=>$_POST["days_due_back"],
				'minutes_due_back'=>$_POST["minutes_due_back"],
				'regular_late_fee'=>$_POST["regular_late_fee"],
				'restock_threshold'=>$_POST["restock_threshold"],
				'important_date'=>$_POST['important_date'],
				'important_date_purpose'=>$_POST['important_date_purpose'],
				'number_of_minutes_between_fee_applications'=>$_POST['number_of_minutes_between_fee_applications'],
				'number_of_minutes_in_grace_period'=>$_POST['number_of_minutes_in_grace_period'],
				'pre_closing_padding'=>$_POST['pre_closing_padding'],
			);
			$errors = $ptr->update_el($coll);
			if (empty($errors)) {
				$msg = "Success - '".$_POST["description"]."' ".$updtSuccess;
			} else {
				$msg = $errors;
			}
			echo json_encode($msg);
			break;
		case 'd-3-L-3-t_collect':
			$ptr = new Collections;
			$errs = $ptr->deleteOne($_POST['code']);
			if ($errs) {echo $errs;} else {echo $deleteComplete;}
			break;

	  #-.-.-.-.-.-Custom Copy Fields -.-.-.-.-.-.-
		case 'getAll_copyFlds':
			$flds = array();
			$set = $ptr->getAll();
            		foreach ($set as $row){
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
			if ($errs) {echo json_encode($errs);} else {echo json_encode($updtSuccess);}
			break;
		case 'd-3-L-3-t_copyFlds':
			$ptr->deleteOne($_POST[code]);
			if ($errs) {echo $errs;} else {echo $deleteComplete;}
			break;

	  #-.-.-.-.-.- Custom Member Fields -.-.-.-.-.-.-
		case 'getAll_mbrFlds':
			$flds = array();
			$set = $ptr->getAll();
            		foreach ($set as $row) {
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
			if ($errs) {echo json_encode($errs);} else {echo json_encode($updtSuccess);}
			break;
		case 'd-3-L-3-t_mbrFlds':
			$ptr->deleteOne($_POST[code]);
			if ($errs) {echo $errs;} else {echo T("Delete completed");}
			break;

	  #-.-.-.-.-.- Hours -.-.-.-.-.-.-
		case 'getAll_hours':
			$flds = array();
			$set = $ptr->getAll();
            foreach ($set as $row) {
			  $flds[] = $row;
			}
			echo json_encode($flds);
			break;
		case 'addNew_hours':
			//$rslt = $ptr->insert_el($_POST);
            //echo json_encode($rslt);
			list($id, $errors) = $ptr->insert_el($type);
			if (!empty($id) || empty($errors)) {
				$msg = T("Hours open")." ".T("has been added");
				echo json_encode(array($id, $msg));
			} else {
				echo json_encode(array(null, $errors));
            }
			break;
		case 'update_hours':
			$errs = $ptr->update_el(array
				('hourid'=>$_POST["hourid"],
				 'siteid'=>$POST['siteid'],
				 'day'=>$_POST['day'],
				 'start_time'=>$_POST['start_time'],
				 'end_time'=>$_POST['end_time'],
				'by_appointment'=>$_POST['by_appointment'],
				'public_note'=>$_POST['public_note'],
				'private_note'=>$_POST['private_note'],
				 )
			);
			if ($errs) {echo json_encode($errs);} else {echo json_encode($updtSuccess);}
			break;

	  #-.-.-.-.-.- Database Integrity testing -.-.-.-.-.-.-
        case 'checkDB':
            $fixDB = false;
            $errs = $ptr->check_el($fixDB);
            echo json_encode($errs);
            break;
        case 'fixDB':
            $fixDB = true;
            $errs = $ptr->check_el($fixDB);
            echo json_encode($errs);
            break;

	  #-.-.-.-.-.- Member Type Classification -.-.-.-.-.-.-
		case 'getAll_mbrTypes':
			$flds = array();
			$set = $ptr->getAll();
			//while ($row = $set->fetch_assoc()) {
            		foreach ($set as $row) {
		 		$flds[] = $row;
			}
			echo json_encode($flds);
			break;
		case 'addNew_mbrTypes':
			$desc = $_POST['description'];
			list($id, $errors) = $ptr->insert_el($_POST);
			if (!empty($id) || empty($errors)) {
				$msg = T("MemberType")." '".$desc."' ".T("has been added");
				echo json_encode(array(0, $msg));
			} else {
				echo json_encode(array(null, $errors));
            }
			break;
		case 'update_mbrTypes':
			$errs = $ptr->update_el(array(
				'code'=>@$_POST["code"],
				'max_fines'=>@$_POST['max_fines'],
				'default_flg'=>@$_POST['default_flg'],
				'description'=>@$_POST["description"],
			));
			if ($errs) {echo json_encode($errs);} else {echo json_encode($updtSuccess);}
			break;
		case 'd-3-L-3-t_mbrTypes':
			$ptr->deleteOne($_POST[code]);
			if ($errs) {echo $errs;} else {echo $deleteComplete;}
			break;

  	#-.-.-.-.-.- Media Types -.-.-.-.-.-.-
		case 'getAll_media':
			$med = array();
			$set = $ptr->getAllWithStats();
			//while ($row = $set->fetch_assoc()) {
            foreach ($set as $row) {
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
			if (!empty($id) || empty($errors)) {
				$msg = T("Media Type")." '".H($type['description'])."' ".T("has been added");
				echo json_encode(array($id, $msg));
			} else {
				echo json_encode(array(null, $errors));
            }
			break;
		case 'update_media':
			if (isset($POST["image_file"])) {
				if (strpos($_POST["image_file"],'\\')) {
					$imgInfo = pathinfo($_POST["image_file"]);
					$imgStuff = explode('\\',$imgInfo['filename']);
					$imgFile = $imgStuff[2].'.'.$imgInfo['extension'];
				} else {
					$imgFile = $_POST["image_file"];
				}
			} else {
				$imgFile = $_POST["crntImage_file"];
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
			if ((stripos($errors, 'Success') > -1) || empty($errors) ) {
				$msg = $errors."! ".T("Media Type")." '".H($type['description'])."' ".T("has been updated");
				echo json_encode($msg);
			} else {
				echo json_encode($errors);
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
			//while ($row = $set->fetch_assoc()) {
            foreach ($set as $row) {
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
			//echo $ptr->insert($_POST);
			$rslt = $ptr->insert($_POST);
            echo json_encode($rslt);
			break;
		case 'update_hosts':
			if (empty($_POST[active])) $_POST[active] = 'N';
			echo json_encode($ptr->update($_POST));
			break;
		case 'd-3-L-3-t_hosts':
//			$sql = "DELETE FROM ".$ptr->getName()." WHERE `id`=$_POST[id]";
//echo "sql=$sql<br />\n";
//			echo $ptr->act($sql);
			$id = $_POST["id"];
			$ptr->deleteOne($id);
			$msg = T("Host")." #".$id." ".T("has been deleted");
			echo $msg;
			break;

	  #-.-.-.-.-.- Online Options -.-.-.-.-.-.-
		case 'getOpts':
	  	$opts = array();
			$set = $ptr->getAll();
			//$row = $set->fetch_assoc();
            $row = $set->fetchAll();
			echo json_encode($row[0]);
			break;
		case 'updateOpts':
		  $_POST[id] = 1;
			if (empty($_POST[autoDewey])) $_POST[autoDewey] = 'n';
			if (empty($_POST[defaultDewey])) $_POST[defaultDewey] = 'n';
			if (empty($_POST[autoCutter])) $_POST[autoCutter] = 'n';
			if (empty($_POST[autoCollect])) $_POST[autoCollect] = 'n';
			$rslt = $ptr->update($_POST);
			if(empty($rslt)) $rslt = '1';
			echo json_encode($rslt);
			break;

	  #-.-.-.-.-.- Settings -.-.-.-.-.-.-
		case 'getFormData':
			$formData = $ptr->getFormData ('admin','name,title,type,value');
			$fd = array();
			// translate these form titles
			foreach($formData as $entry) {
				$entry['title'] = T($entry['title']);
				$fd[] = $entry;
			}
			echo json_encode($fd);
			break;
		case 'update_settings':
			$rslt = $ptr->setAll_el($_POST);
			echo json_encode($rslt);
			break;

	  #-.-.-.-.-.- Sites -.-.-.-.-.-.-
		case 'getAll_sites':
		  $sites = array();
			$set = $ptr->getAll('name');
			//while ($row = $set->fetch_assoc()) {
            foreach ($set as $row) {
			  $sites[] = $row;
			}
			echo json_encode($sites);
			break;
		case 'addNew_sites':
			echo $ptr->insert($_POST);
			break;
		case 'update_sites':
			echo json_encode($ptr->update($_POST));
			break;
		case 'd-3-L-3-t_sites':
			echo $ptr->deleteOne($_POST['siteid']);
			break;

	  #-.-.-.-.-.- Staff -.-.-.-.-.-.-
		case 'getAll_staff':
		  	$staff = array();
			$set = $ptr->getAll('last_name');
			//while ($row = $set->fetch_assoc()) {
            foreach ($set as $row) {
			  $staff[] = $row;
			}
			echo json_encode($staff);
			break;
		case 'addNew_staff':
		case 'update_staff':
			if (!isset($_POST['suspended_flg'])) {
				$_POST['suspended_flg'] = 'N';
			}

			if ($_POST['mode'] == 'addNew_staff') {
				//echo $ptr->insert_el($_POST);
				$rslt = $ptr->insert_el($_POST);
            	list($id, $response) = $rslt;
            	if ($id == NULL)
                	echo json_encode($response);
            	else
                	echo json_encode($rslt);
			} else {
				//$_POST[pwd2] = $_POST[pwd]; // no PW changes allowed in update screen
				//echo $ptr->update($_POST);
				$rslt =  $ptr->update($_POST); // will call $Staff::validate()
                echo json_encode($rslt);
			}
			break;
		case 'fetchStartPage':
			$staff = $_POST('user');
			echo json_encode($staff);
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
			//while ($row = $set->fetch_assoc()) {
            foreach ($set as $row) {
			  $states[] = $row;
			}
			echo json_encode($states);
			break;
		case 'addNew_states':
			$rslt = $ptr2->insert($_POST);
            echo json_encode($rslt);
			break;
		case 'update_states':
			echo $ptr2->update($_POST);
			break;
		case 'd-3-L-3-t_states':
			echo $ptr2->deleteOne($_POST['code']);
			break;

	  #-.-.-.-.-.- Themes -.-.-.-.-.-.-
		case 'getAllThemes':
		  $thms = array();
			$set = $ptr->getAll('theme_name');
			//while ($row = $set->fetch_assoc()) {
            foreach ($set as $row) {
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
		  echo "<h4>".T("invalid mode").": &gt;$_POST[mode]&lt;</h4><br />";
		break;
	}
