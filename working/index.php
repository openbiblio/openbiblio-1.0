<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "working";
$nav = "summary";

include(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo '<h1><img src="../images/tools.png" border="0" width="30" height="30" align="top"> '.T("Under Construction").'</h1>';

echo "<fieldset> \n";
echo "<legend>Various items from time-to-time, Use at your own risk</legend>";
echo "</fieldset> \n";
 ;
