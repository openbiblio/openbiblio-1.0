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

  #****************************************************************************
  #*  Checking for get vars.
  #****************************************************************************
  $bibid = $_GET["bibid"];
  $copyid = $_GET["copyid"];
  $holdid = $_GET["holdid"];
  if (isset($_GET["mbrid"])) {
    $mbrid = $_GET["mbrid"];
    $tab = "circulation";
    $nav = "view";
    $returnUrl = "../circ/mbr_view.php?mbrid=".$mbrid;
  } else {
    $mbrid = "";
    $tab = "cataloging";
    $nav = "holds";
    $returnUrl = "../catalog/biblio_hold_list.php?bibid=".$bibid;
  }
  
  $restrictInDemo = TRUE;
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,"shared");

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<center>
<form name="delbiblioform" method="POST" action="<?php echo $returnUrl;?>">
<?php echo $loc->getText("holdDelConfirmMsg"); ?>
<br><br>
      <input type="button" onClick="parent.location='../shared/hold_del.php?bibid=<?php echo $bibid;?>&copyid=<?php echo $copyid;?>&holdid=<?php echo $holdid;?>&mbrid=<?php echo $mbrid;?>'" value="<?php echo $loc->getText("sharedDelete"); ?>" class="button">
      <input type="submit" value="<?php echo $loc->getText("sharedCancel"); ?>" class="button">
</form>
</center>

<?php include("../shared/footer.php");?>
