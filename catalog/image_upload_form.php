<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "cataloging";
	$nav = "biblio/images/new";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	$bibid = $_REQUEST['bibid'];

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<form name="imageupload" enctype="multipart/form-data"
			action="../catalog/image_manage_action.php" method="post">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="bibid" value="<?php echo H($bibid); ?>" />
<table>
<tr>
<td align="right"><?php echo T("Image File:"); ?></td><td><input type="file" name="image" /></td></tr>
<td align="right"><?php echo T("Caption:"); ?></td><td><input type="text" name="caption" /></td></tr>
<td align="right"><?php echo T("Type:"); ?></td><td>
<label>
<input onclick="document.imageupload.url.disabled=true"
type="radio" name="type" value="Thumb" checked="checked" /><?php echo T("Thumbnailed"); ?></label>
<label>
<input onclick="document.imageupload.url.disabled=false"
type="radio" name="type" value="Link" /><?php echo T("Link"); ?></label>
</td></tr>
<tr><td align="right"><?php echo T("URL:"); ?></td><td><input type="text" name="url" disabled="disabled" /></td></tr>
<tr><td></td><td><input type="Submit" value="<?php echo T("Upload Image"); ?>" class="button" /></td></tr>
</table>
</form>

<?php

	 ;
