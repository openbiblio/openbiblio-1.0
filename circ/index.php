<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "circulation";
	$nav = "searchform";
	$focus_form_name = "barcodesearch";
	if ($_SESSION[mbr_barcode] == 'Y')
		$focus_form_field = "barcode_field";
	else
		$focus_form_field = "mbrName_field";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));

?>

<h1><img src="../images/circ.png" border="0" width="30" height="30" align="top"> <?php echo T("Circulation"); ?></h1>
<?php if ($_SESSION['mbrBarcode_flg'] != 'N') { ?>

<form name="barcodesearch" method="post" action="../circ/mbr_search.php">
<fieldset>
<legend><?php echo T("Get Member by Card Number"); ?></legend>
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Card Number:"); ?>
			<input type="text" id="barcode_field" name="rpt_terms[0][text]" size="20" maxlength="20" />
			<input type="hidden" name="rpt_terms[0][type]" value="barcode" />
			<input type="hidden" name="rpt_terms[0][exact]" value="1" />
			<input type="submit" value="<?php echo T("Search"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>
<?php } ?>

<form name="phrasesearch" method="post" action="../circ/mbr_search.php">
<fieldset>
<legend><?php echo T("Search Member by Name"); ?></legend>
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Name Contains:"); ?>
			<!--input type="text" id="mbrName_field" name="rpt_terms[0][text]" size="30" maxlength="80" /-->
			<?php echo inputfield('text','rpt_terms[0][text]','',array(
															'size'=>'30',
															'maxlength'=>'80',
															'id'=>'mbrName_field'
															)); ?>
			<input type="hidden" name="rpt_terms[0][type]" value="name" />
			<input type="hidden" name="rpt_terms[0][exact]" value="0" />
			<input type="submit" value="<?php echo T("Search"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<?php

	ReportDisplaysUI::display('circ');
	Page::footer();
