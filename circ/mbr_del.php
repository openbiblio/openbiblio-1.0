<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "circulation";
  $restrictToMbrAuth = TRUE;
  $nav = "deletedone";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Members.php"));
  require_once(REL(__FILE__, "../classes/MemberAccountQuery.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));


  $mbrid = $_GET["mbrid"];
  $mbrName = $_GET["name"];

  #**************************************************************************
  #*  Delete library member
  #**************************************************************************
  $members = new Members;
  $members->deleteOne($mbrid);

  #**************************************************************************
  #*  Delete Member History
  #**************************************************************************
/**** FIXME !!!! ***
  $histQ = new BiblioStatusHistQuery();
  $histQ->connect();
  if ($histQ->errorOccurred()) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  if (!$histQ->deleteByMbrid($mbrid)) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  $histQ->close();
*****************/

  #**************************************************************************
  #*  Delete Member Account
  #**************************************************************************
  $transQ = new MemberAccountQuery();
  $transQ->connect();
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  $trans = $transQ->delete($mbrid);
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  $transQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
  echo T("Member, %name%, has been deleted.", array("name"=>$mbrName));

?>
<br /><br />
<a href="../circ/index.php"><?php echo T("Return to Member Search"); ?></a>

<?php

  Page::footer();
