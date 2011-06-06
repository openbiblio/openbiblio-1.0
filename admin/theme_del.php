<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "admin";
$nav = "themes";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Themes.php"));

#****************************************************************************
#*  Checking for query string.  Go back to theme list if none found.
#****************************************************************************
if (!isset($_GET["themeid"])){
	header("Location: ../admin/theme_list.php");
	exit();
}
$themeid = $_GET["themeid"];
$name = $_GET["name"];

#**************************************************************************
#*  Delete row
#**************************************************************************
$themes = new Themes;
$themes->deleteOne($themeid);

#**************************************************************************
#*  Show success page
#**************************************************************************
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo T("Theme, %name%, has been deleted.", array('name'=>H($name))).'<br /><br />';
echo '<a href="../admin/theme_list.php">'.T("Return to theme list").'</a>';

 ;
