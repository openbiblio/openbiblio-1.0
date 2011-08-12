<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "cataloging";
	$nav = "upload_usmarc";

	include(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="searchHdr"><?php echo T('MARC Import'); ?></h3>

<form enctype="multipart/form-data" action="../catalog/upload_usmarc.php" method="post">
<fieldset>
<table>
<tbody>
	<tr>
		<td><label for="test"><?php echo T("Test Load:"); ?></label></td>
		<td><input id="test" name="test" type="checkbox" value="true" checked /></td>
	</tr>
	<tr>
		<td><label for="usmarc_data"><?php echo T("MARC File:"); ?></label></td>
		<td><input id="usmarc_data" name="usmarc_data" type="file"></td>
	</tr>
	<tr>
		<td><label for="opac"><?php echo T("Show in OPAC:"); ?></label></td>
		<td><input  name="opac" id="opac" type="checkbox" value="Y" /></td>
	</tr>
	<tr>
		<td><label for="collectionCd"><?php echo T("Collection:"); ?></label></td>
		<td>
			<?php
			$collections = new Collections;
			echo inputfield('select', "collectionCd", $collections->getDefault(), NULL, $collections->getSelect());
			?>
		</td>
	</tr>
	<tr>
		<td><label for="materialCd"><?php echo T("Media Type:"); ?></label></td>
		<td>
			<?php
			$mattypes = new MediaTypes;
			echo inputfield('select', "materialCd", $mattypes->getDefault(), NULL, $mattypes->getSelect());
			?>
		</td>
	</tr>
</tbody>
	<tr>
		<td colspan="2"><input type="submit" value="<?php echo T("Upload File"); ?>" class="button" /></td>
	</tr>
</table>
</fieldset>
</form>

<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
?>	
