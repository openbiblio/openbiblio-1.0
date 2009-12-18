<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "materials";
	$focus_form_name = "editmaterialform";
	$focus_form_field = "description";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	#****************************************************************************
	#*  Checking for query string flag to read data from database.
	#****************************************************************************
	if (isset($_GET["code"])){
		$code = $_GET["code"];
		$postVars["code"] = $code;
		include_once(REL(__FILE__, "../model/MaterialTypes.php"));
		$mattypes = new MaterialTypes;
		$type = $mattypes->getOne($code);

		$postVars["description"] = $type['description'];
		$postVars["adult_checkout_limit"] = $type['adult_checkout_limit'];
		$postVars["juvenile_checkout_limit"] = $type['juvenile_checkout_limit'];
		$postVars["image_file"] = $type['image_file'];
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}
	$attrs = array('size'=>40,
								 'maxlength'=>40,
								 'style'=>"visibility: visible");

?>
<h3><?php echo T("Material Types"); ?></h3>

<form name="editmaterialform" method="post" action="../admin/materials_edit.php">
<input type="hidden" name="code" value="<?php echo $postVars["code"];?>">

<fieldset>
<legend><?php echo T("Edit Material Properties"); ?></legend>
<table class="primary">
	<tbody class="unstriped">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Description"); ?>:
		</td>
		<td colspan="2" valign="top" class="primary">
			<?php echo inputfield('text','description',$postVars["description"],$attrs); ?>
		</td>
	</tr>
	  <td>&nbsp;</td>
	  <th align="left" class="primary"><?php echo T("Adult");?></th>
	  <th align="left" class="primary"><?php echo T("Juvenile");?></th>
	<tr>
	</tr>
	<tr>
	  <?php $attrs[size]= 2; $attrs[maxlength] = 2; ?>
		<td nowrap="true" class="primary">
			<?php echo T("Checkout Limit");?>:<br /><span class="small"><?php echo T("(enter 0 for unlimited)"); ?></span>
		</td>
		<td valign="top" class="primary">
		  
			<?php echo inputfield('text','adult_checkout_limit',$postVars["adult_checkout_limit"],$attrs); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text','juvenile_checkout_limit',$postVars["juvenile_checkout_limit"],$attrs); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Image File");?>:
		</td>
		<td colspan="2" valign="top" class="primary">
	  	<?php $attrs[size]= 40; $attrs[maxlength] = 128; ?>
			<?php echo inputfield('text','image_file',$postVars["image_file"],$attrs); ?>
		</td>
	</tr>
	</tbody>
	
	<tfoot>
	<tr>
		<td align="center" colspan="3" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" onClick="parent.location='../admin/materials_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
	
</table>
</fieldset>
</form>

<p class="note">
<sup>*</sup><?php echo T("Note:"); ?><br />
<?php echo T("Image file located in directory"); ?></p>

<?php

	Page::footer();
