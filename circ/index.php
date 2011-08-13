<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "searchform";
if ($_SESSION['mbrBarcode_flg'] == 'Y') {
	$focus_form_name = "barcodesearch";
	$focus_form_field = "barcode_field";
} else {
	$focus_form_name = "phrasesearch";
	$focus_form_field = "mbrName_field";
}

require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
echo HTML(file_get_contents('index.jsont'), array(
	'member_barcodes' => Settings::get('mbr_barcode_flg')=='Y',
));

ReportDisplaysUI::display('circ');
?>
<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
?>	
