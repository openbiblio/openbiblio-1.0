<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

session_cache_limiter(null);

$tab = "circulation";
$restrictToMbrAuth = TRUE;
$nav = "new";
$cancelLocation = "../circ/index.php";
$focus_form_name = "newmbrform";
$focus_form_field = "barcodeNmbr";

require_once(REL(__FILE__, "../functions/inputFuncs.php"));
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../shared/get_form_vars.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
$headerWording = T("Add New");

echo '<form name="newmbrform" method="post" action="../circ/mbr_new.php">';

include(REL(__FILE__, "../circ/mbr_fields.php"));
Page::footer();
