<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "biblio/newcopy";
	$focus_form_name = "newCopyForm";
	$focus_form_field = "barcode_nmbr";
	
	#****************************************************************************
	#*  Checking for get vars.  Go back to form if none found.
	#****************************************************************************
	if (count($_GET) == 0) {
		header("Location: ../catalog/index.php");
		exit();
	}

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));

	#****************************************************************************
	#*  Retrieving get var
	#****************************************************************************
	$bibid = $_GET["bibid"];
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<script language="JavaScript" >
bcnf = {
	init: function () {
	  // set 'required' marker on 'barcode_nmbr' field label; probably a simpler way!
	  $('<sup>*</sup>').prependTo($('#newCpyTbl tbody tr:first td:first'));

	  // to handle startup condition
		if ($('#autobarco:checked').length > 0) {
			$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').on('change',null,function (){
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
	$BCQ = new BiblioCopyFields;
	$fields = array(
	  ### corrected to suit inputfield (which gets $pageErrors internally) -- Fred
		T("Barcode Number") => inputfield("text","barcode_nmbr",NULL,$attr=array("size"=>20,"max"=>20)),
		T("Auto Barcode") => inputfield("checkbox","autobarco",$_SESSION['item_autoBarcode_flg'],NULL,$_SESSION['item_autoBarcode_flg']),
		T("Description") => inputfield("text", "copy_desc", NULL, $attr=array("size"=>40,"max"=>40)));
	if($_SESSION['multi_site_func'] > 0){
		$sites_table = new Sites;
		$sites = $sites_table->getSelect();
		$fields[T("Site:")] = inputfield("select", "siteid", $_SESSION["postVars"]["siteid"], NULL, $sites);
	}


	$rows = $BCQ->getAll();

	while ($row = $rows->next()) {
	  ### corrected to suit inputfield (which gets $pageErrors internally) -- Fred
		$fields[$row["description"].':'] = inputfield('text', 'custom_'.$row["code"], NULL,NULL);
	}
?>

<p class="note">
<?php echo T("Fields marked are required"); ?>
</p>

<form name="newCopyForm" method="post" action="../catalog/biblio_copy_new.php">
<fieldset>
<legend><?php echo T("Add New Copy"); ?></legend>
<table id="newCpyTbl" class="primary">
	<tbody>
<?php
	foreach ($fields as $title => $html) {
?>
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
	</tbody>
	<tfoot>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='../shared/biblio_view.php?bibid=<?php echo $bibid; ?>'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
</table>
<input type="hidden" name="bibid" value="<?php echo $bibid;?>" />
</fieldset>
</form>


<?php

	 ;
