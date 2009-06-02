<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "themes";
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  #****************************************************************************
  #*  Checking for query string.  Go back to theme list if none found.
  #****************************************************************************
  if (!isset($_GET["themeid"])){
    header("Location: ../admin/theme_list.php");
    exit();
  }
  $themeid = $_GET["themeid"];
  $name = $_GET["name"];

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delstaffform" method="post" action="../admin/theme_del.php?themeid=<?php echo $themeid;?>&name=<?php echo urlencode($name);?>">
<?php echo T('themeDelConfirmMsg', array('name'=>$name)); ?><br /><br />
      <input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
      <input type="button" onclick="parent.location='../admin/theme_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>

<?php

  Page::footer();
