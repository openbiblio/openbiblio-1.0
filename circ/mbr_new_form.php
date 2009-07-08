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
?>
<script>
mnf = {
	init: function () {
		$('<sup>*</sup>').prependTo('#newmbrform table tr:has(input.required) td:first-child');
	}
};
$(document).ready(mnf.init);
</script>

<h1><span id="searchHdr" class="title"><?php echo$headerWording;?> <?php echo T("Member"); ?></span></h1>
<form id="newmbrform" name="newmbrform" method="post" action="../circ/mbr_new.php">

<?php>
include(REL(__FILE__, "../circ/mbr_fields.php"));
Page::footer();
