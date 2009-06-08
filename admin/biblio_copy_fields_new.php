<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "member_fields";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../admin/member_fields_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $pageErrors = array();
  if (!isset($_POST['description']) or !$_POST['description']) {
    $pageErrors['description'] = T("This is a required field.");
  }
  if (!empty($pageErrors)) {
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/member_fields_new_form.php");
    exit();
  }

  $BCQ = new BiblioCopyFields;
  $biblioCopyField = array(
    'code'=>$_POST["code"],
    'description'=>$_POST["description"],
    'default_flg'=>'N',
  );

  list($id, $errors) = $BCQ->insert_el($biblioCopyField);
  if (empty($errors)) {
    $msg = T('biblioCopyFieldsNewMsg', array('desc'=>H($biblioCopyField['description'])));
    header("Location: ../admin/biblio_copy_fields_list.php?msg=".U($msg));
    exit();
  } else {
    FieldError::backToForm('../admin/biblio_copy_new_form.php', $errors);
  }



  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
