<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	session_cache_limiter(null);

	$tab = "admin";
	$nav = "new";
	$helpPage = "customCopyFields";

	if (isset($_GET["msg"])) {
		$msg = '<p class="error">'.H($_GET["msg"]).'</p><br /><br />';
	} else {
		$msg = "";
	}
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
	require_once(REL(__FILE__, "../functions/errorFuncs.php"));

	echo $msg;
?>
<br />
<a href="biblio_copy_fields_new_form.php"><?php echo T("Add a custom biblio copy field"); ?></a><br /><br />
<?php
  $BCQ = new BiblioCopyFields;
  $rows = $BCQ->getAll();

  if (empty($rows)) {
    echo T("No fields found!");
  } else {
?>

<table class="primary">
<tr><th colspan="2" valign="top"><?php echo T("Function"); ?></th>
<th><?php echo T("Label");?></th>
</tr>
<?php
while ($row = $rows->next()) {
?>
<tr>
<td valign="top" class="primary">
<a href="biblio_copy_fields_edit_form.php?code=<?php echo HURL($row["code"])?>">
<?php echo T("edit"); ?></a>
</td>
<td valign="top" class="primary">
<a href="biblio_copy_fields_del_confirm.php?code=<?php echo HURL($row["code"])?>&amp;desc=<?php echo HURL($row['description'])?>">
<?php echo T("del"); ?></a>
</td>
<td class="primary" align="center"><?php echo H($row['description']);?></td>
</tr>
<?php
    }
    echo "</table>";
  }
  Page::footer();
