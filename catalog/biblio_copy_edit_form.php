<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "biblio/editcopy";
	$focus_form_name = "editCopyForm";
	$focus_form_field = "barcodeNmbr";
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/History.php"));
	require_once(REL(__FILE__, "../model/CopyStates.php"));
	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));


	if (isset($_GET["copyid"])){
		$copyid = $_GET["copyid"];

		$copies = new Copies;
		$history = new History;
		$copy = $copies->getOne($copyid);

		foreach ($copy as $k=>$v) {
			$_SESSION["postVars"][$k] = $v;
		}

		$custom = $copies->getCustomFields($copyid);
		while ($row = $custom->next() ) {
			$_SESSION["postVars"]['custom_'.$row["code"]] = $row["data"];
		}

		$status = $history->getOne($copy['histid']);
		$postVars['status_cd'] = $status['status_cd'];
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
		$copyid = $postVars["copyid"];
	}

	#**************************************************************************
	#*  disable status code drop down for shelving cart and out status codes
	#**************************************************************************
	$statusDisabled = FALSE;
	if ($postVars[status_cd] == OBIB_STATUS_SHELVING_CART
			or $postVars[status_cd] == OBIB_STATUS_OUT) {
		$statusDisabled = TRUE;
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	$BCQ = new BiblioCopyFields;
	$fields = array(
	 ### corrected to suit inputfield (which gets $pageErrors internally) -- Fred
	T("Barcode Number") => inputfield("text","barcode_nmbr",$_SESSION["postVars"]["barcode_nmbr"],$attr=array("size"=>20,"max"=>20)));
		
//	T("Auto Barcode") => inputfield("checkbox","autobarco",NULL,$attr=array("size"=>1,"max"=>1),$postVars),
	// Seems that this should only be used on initial addition of a copy, and once a barcode is set not to be ticked any way - LJ
	if(!empty($_SESSION["postVars"]["barcode_nmbr"])){
		$fields[T("Auto Barcode")] = inputfield("checkbox","autobarco",'N',NULL);
	} else {
		$fields[T("Auto Barcode")] = inputfield("checkbox","autobarco",$_SESSION['item_autoBarcode_flg']);
	}
	$fields[T("Description")] = inputfield("text", "copy_desc", $_SESSION["postVars"]["copy_desc"], $attr=array("size"=>40,"max"=>40));
	if($_SESSION['multi_site_func'] > 0){
		$sites_table = new Sites;
		$sites = $sites_table->getSelect();
		$fields[T("Site:")] = inputfield("select", "siteid", $_SESSION["postVars"]["siteid"], NULL, $sites);
	}
?>

<script language="JavaScript1.4" >
bcnf = {
	init: function () {
	  // set 'required' marker on 'barcode_nmbr' field label; probably a simpler way!
	  $('<sup>*</sup>').prependTo($('#newCpyTbl tbody tr:first td:first'));

	  // to handle startup condition
		if ($('#autobarco:checked').length > 0) {
			$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').bind('change',null,function (){
		  if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
			}
			else {
				$('#barcode_nmbr').enable();
			}
		})
	}
}
$(document).ready(bcnf.init);
</script>

<?php

	## add custom copy fields
	$rows = $BCQ->getAll();
	while ($row = $rows->next()) {
		$fields[$row["description"].':'] = inputfield('text', 'custom_'.$row["code"], $_SESSION["postVars"]['custom_'.$row["code"]],NULL);
	}
	
?>

<p class="note">
<?php echo T("Fields marked are required"); ?>
</p>

<form name="editCopyForm" method="post" action="../catalog/biblio_copy_edit.php">
<fieldset>
<legend><?php echo T("Edit Copy"); ?></legend>
<table class="primary">
<?php
	foreach ($fields as $title => $html) {
?>
	<tbody class="unstriped">
	<tr>
		<td nowrap="true" class="primary" valign="top">
			<?php echo T($title); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo $html; ?>
		</td>
	</tr>
<?php
	}
?>

	<tr>
		<td nowrap="true" class="primary" valign="top">
			<?php echo T("Status:"); ?>
		</td>
		<td valign="top" class="primary">

<?php
	#**************************************************************************
	#*  only show status codes for valid transitions
	#**************************************************************************
	$states = new CopyStates;
	$state_select = $states->getSelect();
	$attrs = array();
	if ($postVars['status_cd'] == OBIB_STATUS_OUT
			or $postVars['status_cd'] == OBIB_STATUS_ON_HOLD
			or $postVars['status_cd'] == OBIB_STATUS_SHELVING_CART) {
		$attrs['disabled'] = 1;
	} else {
		unset($state_select[OBIB_STATUS_OUT]);
		unset($state_select[OBIB_STATUS_ON_HOLD]);
		unset($state_select[OBIB_STATUS_SHELVING_CART]);
	}
	echo inputfield(select, status_cd, $postVars['status_cd'], $attrs, $state_select);
?>

		</td>
	</tr>
	</tbody>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='../shared/biblio_view.php?bibid=<?php echo $_GET[bibid] ?>'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>

</table>
<input type="hidden" name="copyid" value="<?php echo $copyid;?>" />
</fieldset>
</form>

<?php

	Page::footer();
