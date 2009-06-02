<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "view";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Copies.php"));
  require_once(REL(__FILE__, "../model/CopiesCustomFields.php"));

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../catalog/biblio_new_form.php");
    exit();
  }
  $copies = new Copies;
  $bibid=$_POST["bibid"];

  #****************************************************************************
  #*  Autobarco
  #*
  #* FIXME RACE: User A and User B each try to insert a copy concurrently.
  #* User A's process gets next copy id, then checks for a duplicate barcode,
  #* Before the final insert, though, User B's process asks for the next copy id
  #* and checks for a duplicate barcode.  At that point, both inserts will succeed
  #* and two copies will have the same barcode.  Several different interleavings
  #* either cause the duplicate barcode check to fail or cause duplicate barcodes
  #* to be entered.  This can be fixed with a lock or by an atomic
  #* get-and-increment-sequence-value operation.  I'll fix it later. -- Micah
  #****************************************************************************

  if (isset($_POST["autobarco"]) and $_POST["autobarco"]) {
    $nzeros = "5";
    $CopyNmbr= $copies->getNextCopy();
    $_POST["barcode_nmbr"] = sprintf("%0".$nzeros."s",$bibid).$CopyNmbr;
  }

  $fields = array(bibid, barcode_nmbr, copy_desc);
                  #vendor, fund, price, expiration);
  $copy = array();
  foreach ($fields as $f) {
    if (isset($_POST[$f])) {
      $copy[$f] = $_POST[$f];
    }
  }
  list($copyid, $errors) = $copies->insert_el($copy);
  if ($errors) {
    FieldError::backToForm('../catalog/biblio_copy_new_form.php?bibid='.U($bibid), $errors);
  }

	$customFields = new CopiesCustomFields;
	$custom = array();
	foreach ($customFields->getSelect() as $name => $title) {
		if (isset($_REQUEST['custom_'.$name])) {
			$custom[$name] = $_POST['custom_'.$name];
		}
  	}
	$copies->setCustomFields($copyid, $custom);


  $msg = T("Copy successfully created.");
  header("Location: ../shared/biblio_view.php?bibid=".U($bibid)."&msg=".U($msg));
  exit();
