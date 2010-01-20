<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));

$tab = "cataloging";
$nav = "summary";

include(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo '<h1><img src="../images/catalog.png" border="0" width="30" height="30" align="top"> '.T("Cataloging").'</h1>';
echo "<fieldset>\n";
echo T("catalogIndexDesc");
echo "</fieldset>\n";

ReportDisplaysUI::display('catalog');
Page::footer();
