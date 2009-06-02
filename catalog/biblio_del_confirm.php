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

  $tab = "cataloging";
  $nav = "delete";
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  $bibid = $HTTP_GET_VARS["bibid"];
  $title = $HTTP_GET_VARS["title"];

  #****************************************************************************
  #*  Check for copies and holds
  #****************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->execSelect($bibid);
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyCount = $copyQ->getRowCount();
  $copyQ->close();

  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $holdQ->queryByBibid($bibid);
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

  if (($copyCount > 0) or ($holdCount > 0)) {
?>
<center>
  <?php echo $loc->getText("biblioDelConfirmWarn",array("copyCount"=>$copyCount,"holdCount"=>$holdCount)); ?>
  <br><br>
  <a href="../shared/biblio_view.php?bibid=<?php echo $bibid;?>"><?php echo $loc->getText("biblioDelConfirmReturn"); ?></a>
</center>

<?php
  } else {
?>
<center>
<form name="delbiblioform" method="POST" action="../shared/biblio_view.php?bibid=<?php echo $bibid;?>">
<?php echo $loc->getText("biblioDelConfirmMsg",array("title"=>$title)); ?>
<br><br>
      <input type="button" onClick="parent.location='../catalog/biblio_del.php?bibid=<?php echo $bibid;?>&title=<?php echo urlencode($title);?>'" value="<?php echo $loc->getText("catalogDelete"); ?>" class="button">
      <input type="submit" value="<?php echo $loc->getText("catalogCancel"); ?>" class="button">
</form>
</center>
<?php 
  }
  include("../shared/footer.php");
?>
