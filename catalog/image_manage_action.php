<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../classes/ImageQuery.php"));

  $imgq = new ImageQuery();
  $imgq->connect();
  if ($imgq->errorOccurred()) {
    $imgq->close();
    displayErrorPage($imgq);
  }
  switch ($_REQUEST['action']) {
  case add:
    if ($_REQUEST['type'] == 'Link') {
      $imgq->appendLink($_REQUEST['bibid'], $_REQUEST['caption'],
                         $_FILES['image'], $_REQUEST['url']);
    } else {
      $imgq->appendThumb($_REQUEST['bibid'], $_REQUEST['caption'],
                         $_FILES['image']);
    }
    break;
  case delete:
    if (!$_REQUEST['confirm']) {
      include(REL(__FILE__, "../catalog/image_del_confirm.php"));
      exit();
    }
    $imgq->delete($_REQUEST['bibid'], $_REQUEST['imgurl']);
    break;
  case update_caption:
    $imgq->updateCaption($_REQUEST['bibid'], $_REQUEST['imgurl'], $_REQUEST['caption']);
    break;
  case reposition:
    $imgq->reposition($_REQUEST['bibid'], $_REQUEST['imgurl'], $_REQUEST['position']);
    break;
  }
  if ($imgq->errorOccurred()) {
    $imgq->close();
    displayErrorPage($imgq);
  }

  header("Location: ../catalog/image_manage.php?bibid=".U($_REQUEST['bibid']));
