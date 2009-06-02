<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/Sites.php"));

  if (count($_POST) == 0) {
    header("Location: ../admin/sites_list.php");
    exit();
  }

  $sites = new Sites;
  $site = array();
  foreach (array_keys($sites->fields) as $f) {
    $site[$f] = trim($_POST[$f]);
  }

  if (isset($_POST['siteid']) and is_numeric($_POST['siteid'])) {
    $errors = $sites->update_el($site);
  } else {
    list($id, $errors) = $sites->insert_el($site);
  }

  if ($errors) {
    FieldError::backToForm('../admin/sites_edit_form.php', $errors);
  }

	$msg = T("Site, %name%, updated.", array('name'=>$site['name']));
  header("Location: ../admin/sites_list.php?msg=".U($msg));
