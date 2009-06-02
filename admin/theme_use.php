<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "themes";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  if (count($_POST) == 0) {
    header("Location: ../admin/theme_list.php");
    exit();
  }

  $newThemeId = $_POST["themeid"];
  if ($err = Settings::setOne_e('themeid', $newThemeId)) {
    Fatal::internalError(T("Unexpected error: ").$err->toStr());
  }

  header("Location: ../admin/theme_list.php");
