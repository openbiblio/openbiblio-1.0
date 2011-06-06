<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "cataloging";
	$nav = "upload_usmarc";

	include(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h1><span id="searchHdr" class="title"><?php echo T('MARC Import'); ?></span></h1>

<form enctype="multipart/form-data" action="../catalog/upload_usmarc.php" method="post">
<fieldset>
<table class="primary">
<tbody>
	<tr>
		<td align="right" class="primary"><?php echo T("Test Load:"); ?></td>
		<td class="primary"><input type="checkbox" value="true" name="test" checked="checked" /></td>
	</tr>
	<tr>
		<td align="right" class="primary"><?php echo T("MARC File:"); ?></td>
		<td class="primary"><input type="file" name="usmarc_data"></td>
	</tr>
	<tr>
		<td align="right" class="primary"><?php echo T("Show in OPAC:"); ?></td>
		<td class="primary"><input type="checkbox" name="opac" id="opac" value="Y" /></td>
	</tr>
	<tr>
		<td align="right" class="primary"><?php echo T("Collection:"); ?></td>
		<td class="primary">
			<?php
			$collections = new Collections;
			echo inputfield('select', "collectionCd", $collections->getDefault(), NULL, $collections->getSelect());
			?>
		</td>
	</tr>
	<tr>
		<td align="right" class="primary"><?php echo T("Type of Material:"); ?></td>
		<td class="primary">
			<?php
			$mattypes = new MaterialTypes;
			echo inputfield('select', "materialCd", $mattypes->getDefault(), NULL, $mattypes->getSelect());
			?>
		</td>
	</tr>
</tbody>
	<tr>
		<td class="primary" colspan="2" align="right"><input type="submit" value="<?php echo T("Upload File"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<?php

	 ;
