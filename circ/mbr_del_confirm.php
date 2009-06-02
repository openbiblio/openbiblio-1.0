<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

  $tab = "circulation";
  $restrictToMbrAuth = TRUE;
  $nav = "delete";
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  $mbrid = $_GET["mbrid"];

  #****************************************************************************
  #*  Getting member name
  #****************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  if ($mbrQ->errorOccurred()) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  if (!$mbrQ->execSelect($mbrid)) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  $mbr = $mbrQ->fetchMember();
  $mbrQ->close();
  $mbrName = $mbr->getFirstName()." ".$mbr->getLastName();

  #****************************************************************************
  #*  Getting checkout count
  #****************************************************************************
  $biblioQ = new BiblioSearchQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->query(OBIB_STATUS_OUT,$mbrid)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  $checkoutCount = $biblioQ->getRowCount();
  $biblioQ->close();

  #****************************************************************************
  #*  Getting hold request count
  #****************************************************************************
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $holdQ->queryByMbrid($mbrid);
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $holdCount = $holdQ->getRowCount();
  $holdQ->close();
  
  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");

  if (($checkoutCount > 0) or ($holdCount > 0)) {
?>
<center>
  <?php echo $loc->getText("mbrDelConfirmWarn",array("name"=>$mbrName,"checkoutCount"=>$checkoutCount,"holdCount"=>$holdCount)); ?>
  <br><br>
  <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>&reset=Y"><?php echo $loc->getText("mbrDelConfirmReturn"); ?></a>
</center>

<?php
  } else {
?>
<center>
<form name="delbiblioform" method="POST" action="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>&reset=Y">
<?php echo $loc->getText("mbrDelConfirmMsg",array("name"=>$mbrName)); ?>
<br><br>
      <input type="button" onClick="parent.location='../circ/mbr_del.php?mbrid=<?php echo $mbrid;?>&name=<?php echo urlencode($mbrName);?>'" value="<?php echo $loc->getText("circDelete"); ?>" class="button">
      <input type="submit" value="<?php echo $loc->getText("circCancel"); ?>" class="button">
</form>
</center>
<?php 
  }
  include("../shared/footer.php");
?>
