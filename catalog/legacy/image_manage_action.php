<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/BiblioImages.php"));

$bibimages = new BiblioImages;
switch ($_REQUEST['action']) {
case add:
	if ($_REQUEST['type'] == 'Link') {
		$err = $bibimages->appendLink_e($_REQUEST['bibid'], $_REQUEST['caption'],
			$_FILES['image'], $_REQUEST['url']);
	} else {
		$err = $bibimages->appendThumb_e($_REQUEST['bibid'], $_REQUEST['caption'],
			$_FILES['image']);
	}
	break;
case delete:
	if (!$_REQUEST['confirm']) {
		include(REL(__FILE__, "../catalog/image_del_confirm.php"));
		exit();
	}
	$bibimages->deleteOne($_REQUEST['bibid'], $_REQUEST['imgurl']);
	$err = NULL;
	break;
case update_caption:
	$err = $bibimages->updateCaption_e($_REQUEST['bibid'], $_REQUEST['imgurl'], $_REQUEST['caption']);
	break;
case reposition:
	$bibimages->reposition($_REQUEST['bibid'], $_REQUEST['imgurl'], $_REQUEST['position']);
	break;
}

// FIXME -- handle errors

header("Location: ../catalog/image_manage.php?bibid=".U($_REQUEST['bibid']));
