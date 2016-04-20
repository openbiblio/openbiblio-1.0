<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	$tab = "cataloging";
	$nav = "bulk_delete";
//	$restrictInDemo = true;
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	$focus_form_name=bulk_delete;
	$focus_form_field=barcodes;
	
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
	<h3><?php echo T("Bulk Delete"); ?></h3>
	<?php //print_r($_SESSION); // for debugging ?>

	<div id="crntMbrDiv">to be filled by server</div>
	<p id="errSpace" class="error">to be filled by server</p>

	<!-- ------------------------------------------------------------------------ -->
	<div id="bulkDel_formDiv">
		<form id="bulkDel_form" name="bulkDel_form" >
		<fieldset>
			<legend><?php echo T("Barcodes to delete"); ?></legend>
			<?php echo inputfield('hidden','posted','1'); ?>
			<?php echo inputfield('textarea','barcodes','',array('rows'=>'12'),H($vars['barcodes'])); ?>
			<br />
			<label for="del_items">
				<?php echo inputfield('checkbox','del_items','1','',($vars['del_items']?'1':'')); ?>
				<?php echo T("DeleteItemsIfAllCopiesDeleted") ?>
			</label>
			<br />
			<input type="button" id="bulkDel_btn" value="<?php echo T("Submit");?>" />
		</fieldset>
		</form>
	</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "bulkDelJs.php"));
?>	
</body>
</html>
