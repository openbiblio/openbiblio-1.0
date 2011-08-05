<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));
	require_once(REL(__FILE__, "../functions/info_boxes.php"));

	session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "searchform";
	$focus_form_name = "barcodesearch";
	$focus_form_field = "searchText";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	currentMbrBox();
?>
<h1><img src="../images/catalog.png" border="0" width="30" height="30" align="top"> <?php echo T("Cataloging"); ?></h1>

<?php
	if (isset($_REQUEST["msg"]) && !empty($_REQUEST["msg"])) {
		echo '<p class="error">'.H($_REQUEST["msg"]).'</p><br /><br />';
	}
?>

<form name="barcodesearch" method="post" action="../shared/biblio_search.php">
<fieldset>
<legend><?php echo T("Find Item by Barcode Number"); ?></legend>
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<label for="searchText"><?php echo T("Barcode Number:");?></label>
			<?php echo inputfield('text','searchText','',array('size'=>'20','maxlength'=>'20')); ?>
			<?php echo inputfield('hidden','searchType','barcodeNmbr'); ?>
			<?php echo inputfield('hidden','sortBy','default'); ?>
			<input type="submit" value="<?php echo T("Search"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>
<br />
<?php

	include(REL(__FILE__, "../shared/searchbar.php"));
	ReportDisplaysUI::display('catalog');
	 ;
