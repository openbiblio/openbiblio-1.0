<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  #****************************************************************************
  #*  Checking for query string.  Go back to collection list if none found.
  #****************************************************************************
  if (!isset($_GET["code"])){
    header("Location: ../admin/biblio_copy_fields_list.php");
    exit();
  }
  $code = $_GET["code"];
  $description = $_GET["desc"];

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delcopyform" method="post" action="../admin/biblio_copy_fields_del.php?code=<?php echo $code;?>&desc=<?php echo U($description);?>">
<p>
<?php echo T('biblioCopyFieldsDelConfirmSure', array('desc'=>$description)); ?>
</p>
<br /><br />
<input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
<input type="button" onClick="parent.location='../admin/collections_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>
<?php

  Page::footer();
