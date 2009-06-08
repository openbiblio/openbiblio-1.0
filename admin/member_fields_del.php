<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "member_fields";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/MemberCustomFields.php"));

  if (!isset($_GET["code"])){
    header("Location: ../admin/member_fields_list.php");
    exit();
  }
  $code = $_GET["code"];
  $description = $_GET["desc"];

  $fields = new MemberCustomFields;
  $fields->deleteOne($code);

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T('memberFieldsDelMsg', array('desc'=>$description)).'<br /><br />';
  echo '<a href="../admin/member_fields_list.php">'.T("Return to member fields list").'</a>';

  Page::footer();
