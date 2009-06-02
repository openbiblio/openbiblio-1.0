<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "circulation";
  $nav = "checkin";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/Copies.php"));


  if (count($_POST) == 0) {
    header("Location: ../circ/checkin_form.php?reset=Y");
    exit();
  }
  $copies = new Copies;
  $massCheckinFlg = $_POST["massCheckin"];
  if ($massCheckinFlg == "Y") {
    $copies->massCheckin();
  } else {
    $bibids = array();
    $copyids = array();
    foreach($_POST as $key => $value) {
      if ($value == "copyid") {
        parse_str($key,$output);
        $bibids[] = $output["bibid"];
        $copyids[] = $output["copyid"];
      }
    }
    if (empty($bibids)) {
      $msg = T("No items have been selected.");
      header("Location: ../circ/checkin_form.php?msg=".U($msg));
      exit();
    }
    $copies->checkin($bibids, $copyids);
  }

  header("Location: ../circ/checkin_form.php?reset=Y");
