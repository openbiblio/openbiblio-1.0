<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  $tab = "admin";
  $nav = "new";

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/MaterialFields.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../admin/material_list.php");
    exit();
  }

  $rec = array();
  $rec['material_cd'] = $_POST["material_cd"];
  $rec['material_field_id'] = $_POST["material_field_id"];
  $rec['tag'] = $_POST["tag"];
  $rec['subfield_cd']  = $_POST["subfield_cd"];
  $rec['label']  = $_POST["label"];
  $rec['required'] = $_POST["required"];
  $rec['form_type'] = $_POST["form_type"];
  $rec['repeatable'] = $_POST["repeatable"];
  $rec['search_results'] = $_POST["search_results"];
  $rec['position']  = $_POST["position"];
  if (!$rec['label']) {
    $pageErrors['label'] = T("Field is required.");
  }
  if (!empty($pageErrors)) {
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/material_fields_edit_form.php");
    exit();
  }

  $mf = new MaterialFields;
  $mf->update($rec);

  $msg = T("Field Updated successfully");
  header("Location: material_fields_view.php?material_cd=".U($rec['material_cd'])."&msg=".U($msg));
  Page::footer();
