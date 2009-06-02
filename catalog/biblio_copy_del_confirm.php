<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "view";
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/Copies.php"));
  require_once(REL(__FILE__, "../model/History.php"));


  $copyid = $_GET["copyid"];

  $copies = new Copies;
  $history = new History;
  $copy = $copies->getOne($copyid);
  $status = $history->getOne($copy['histid']);

  #****************************************************************************
  #*  Make sure copy is checked in before it is deleted
  #****************************************************************************
  if ($status['status_cd'] == OBIB_STATUS_OUT) {
    $msg = T("biblioCopyDelConfirmErr1");
    header("Location: ../shared/biblio_view.php?bibid=".U($copy['bibid'])."&msg=".U($msg));
    exit();
  }

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delcopyform" method="post"
  action="../catalog/biblio_copy_del.php?bibid=<?php echo HURL($copy['bibid']) ?>&copyid=<?php echo HURL($copyid);?>&barcode=<?php echo HURL($copy['barcode_nmbr']);?>">
  <?php echo T("biblioCopyDelConfirmMsg", array("barcodeNmbr"=>H($copy['barcode_nmbr']))); ?>
  <br /><br />
  <input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
  <input type="button" onclick="parent.location='../shared/biblio_view.php?bibid=<?php echo HURL($copy['bibid']);?>'" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>

<?php

  Page::footer();
