<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "biblio/editcopy";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/Copies.php"));
  require_once(REL(__FILE__, "../model/History.php"));
  require_once(REL(__FILE__, "../model/CopiesCustomFields.php"));


  #****************************************************************************
  #*  Checking for post vars.  Go back to search if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../catalog/index.php");
    exit();
  }
  $copyid = $_POST["copyid"];

  #****************************************************************************
  #*  Ready copy record
  #****************************************************************************
  $copies = new Copies;
  $history = new History;
  $copy = $copies->getOne($copyid);
  $status = $history->getOne($copy['histid']);

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $copyChanged = False;
  $fields = array('copyid', 'barcode_nmbr', 'copy_desc');
                 #vendor, fund, price, expiration);
  foreach ($fields as $f) {
    if ($_POST[$f] != $copy[$f]) {
      $copy[$f] = $_POST[$f];
      $copyChanged = True;
    }
  }
  if ($copyChanged) {
    $errors = $copies->update_el($copy);
    if ($errors) {
      FieldError::backToForm('../catalog/biblio_copy_edit_form.php', $errors);
    }
  }

	$customFields = new CopiesCustomFields;
	$custom = array();
	foreach ($customFields->getSelect() as $name => $title) {
		if (isset($_REQUEST['custom_'.$name])) {
			$custom[$name] = $_POST['custom_'.$name];
		}
  	}

	$copies->setCustomFields($copyid, $custom);

  $newStat = $_POST['status_cd'];
  $oldStat = $status['status_cd'];
  $illegal = array(OBIB_STATUS_OUT,
                   OBIB_STATUS_ON_HOLD,
                   OBIB_STATUS_SHELVING_CART);
  if ($newStat != $oldStat
      and !in_array($newStat, $illegal)
      and !in_array($oldStat, $illegal)) {
      	$history->insert(array(
      	'bibid'=>$copy['bibid'], 'copyid'=>$copy['copyid'], 'status_cd'=>$newStat
    ));
  }

  $msg = T("Copy successfully updated.");
  header("Location: ../shared/biblio_view.php?bibid=".U($copy[bibid])."&msg=".U($msg));
  exit();
