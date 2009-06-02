<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "delimage";
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../classes/ImageQuery.php"));


  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $bibid = $_REQUEST["bibid"];
  $imgurl = $_REQUEST["imgurl"];

  $imgq = new ImageQuery();
  $imgq->connect();
  if ($imgq->errorOccurred()) {
    $imgq->close();
    displayErrorPage($imgq);
  }
  $imgs = $imgq->get($bibid, $imgurl);
  if ($imgq->errorOccurred()) {
    $imgq->close();
    displayErrorPage($imgq);
  }
  $imgq->close();

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
  if (count($imgs) != 1) {
?>
<p class="error"><?php echo T("No such image."); ?></p>
<?php
  } else {
    $img = $imgs[0];
?>
<center>
<a href="<?php echo H($img['url']) ?>"><img src="<?php echo H($imgurl) ?>" alt="<?php echo H($img['caption']) ?>" /></a><br />
<form name="delimageform" method="post" action="../catalog/image_manage_action.php">
  <input type="hidden" name="bibid" value="<?php echo H($bibid) ?>" />
  <input type="hidden" name="imgurl" value="<?php echo H($imgurl) ?>" />
  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="confirm" value="1" />
  <p><?php echo T("Really delete this image?"); ?></p>
  <input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
  <input type="button" onclick="parent.location='../catalog/image_manage.php?bibid=<?php echo HURL($bibid) ?>'" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>
<?php
  }
Page::footer();
