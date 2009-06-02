<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "collections";
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  #****************************************************************************
  #*  Checking for query string.  Go back to collection list if none found.
  #****************************************************************************
  if (!isset($_GET["code"])){
    header("Location: ../admin/collections_list.php");
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
<form name="delstaffform" method="post" action="../admin/collections_del.php?code=<?php echo $code;?>&desc=<?php echo urlencode($description);?>">
<?php echo T('collectionsDelConfirmMsg', array('desc'=>$description)); ?><br /><br />
      <input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
      <input type="button" onclick="parent.location='../admin/collections_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>

<?php

  Page::footer();
