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

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
	$bibid = $_GET["bibid"];
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  	$BCQ = new BiblioCopyFields;

	$fields = array(
    	T("Barcode Number") => inputfield("text","barcode_nmbr",NULL,$attr=array("size"=>20,"max"=>20),$pageErrors),
		T("Auto Barcode") => inputfield("checkbox","autobarco",NULL,NULL,$pageErrors),
		T("Description") => inputfield("text", "copy_desc", NULL, $attr=array("size"=>40,"max"=>40), $pageErrors),
	);

	$rows = $BCQ->getAll();

	while ($row = $rows->next()) {
		$fields[$row["description"].':'] = inputfield('text', 'custom_'.$row["description"], NULL,NULL,$pageErrors);
	}
?>

<p class="note">
<?php echo T("Fields marked are required"); ?>
</p>

<form name="newCopyForm" method="post" action="../catalog/biblio_copy_new.php">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      <?php echo T("Add New Copy"); ?>
    </th>
  </tr>
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

  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
      <input type="button" onclick="parent.location='../shared/biblio_view.php?bibid=<?php echo $bibid; ?>'" value="<?php echo T("Cancel"); ?>" class="button" />
    </td>
  </tr>

</table>
<input type="hidden" name="bibid" value="<?php echo $bibid;?>" />
</form>


<?php

  Page::footer();
