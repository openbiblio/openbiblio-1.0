<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "checkin";
$focus_form_name = "barcodesearch";
$focus_form_field = "barcodeNmbr";

require_once(REL(__FILE__, "../functions/inputFuncs.php"));
require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

$barcode = $_GET["barcode"];

echo '<h1>'.T("Copy Has Been Placed On Hold!").'</h1>';
echo T("holdMessageMsg1",array("barcode"=>$barcode)).'<br /><br />';
echo '<a href="../circ/checkin_form.php">'.T("Return to copy check in").'</a>';
Page::footer();
