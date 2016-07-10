<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));

$tab = "admin";
$nav = "summary";

require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

    <h1><img src="../images/admin.png" border="0" width="30" height="30" align="top"> '.T("Admin").'</h1>
    <fieldset> <?php echo T("adminIndexDesc"); ?> </fieldset>

<?php
    ReportDisplaysUI::display('admin');
