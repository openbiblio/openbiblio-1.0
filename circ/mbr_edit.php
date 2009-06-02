<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$restrictToMbrAuth = TRUE;
	$nav = "edit";
	$restrictInDemo = true;
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../model/Members.php"));
	require_once(REL(__FILE__, "../model/MemberCustomFields.php"));


  	if (count($_POST) == 0) {
    	header("Location: ../circ/index.php");
    	exit();
  	}

  	$mbr = array(
		'mbrid'=>$_POST["mbrid"],
    	'siteid'=>$_POST["siteid"],
    	'barcode_nmbr'=>$_POST["barcode_nmbr"],
    	'last_name'=>$_POST["last_name"],
    	'first_name'=>$_POST["first_name"],
    	'address1'=>$_POST["address1"],
    	'address2'=>$_POST["address2"],
    	'city'=>$_POST["city"],
    	'state'=>$_POST["state"],
    	'zip'=>$_POST["zip"],
    	'zip_ext'=>$_POST["zip_ext"],
    	'home_phone'=>$_POST["home_phone"],
    	'work_phone'=>$_POST["work_phone"],
    	'email'=>$_POST["email"],
    	'password'=>$_POST["password"],
    	'confirm-pw'=>$_POST["confirm-pw"],
    	'classification'=>$_POST["classification"],
	  );

 	$members = new Members;
	$errors = $members->update_el($mbr);
  	if ($errors) {
		FieldError::backToForm('../circ/mbr_edit_form.php', $errors);
	}

  	$customFields = new MemberCustomFields;
	$custom = array();
	foreach ($customFields->getSelect() as $name => $title) {
		if (isset($_REQUEST['custom_'.$name])) {
			$custom[$name] = $_POST['custom_'.$name];
		}
  	}
	$members->setCustomFields($mbr['mbrid'], $custom);

  	$msg = T("Member has been successfully updated.");
  	header("Location: ../circ/mbr_view.php?mbrid=".U($mbr['mbrid'])."&reset=Y&msg=".U($msg));
