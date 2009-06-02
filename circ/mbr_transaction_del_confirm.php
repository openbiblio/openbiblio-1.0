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
  $nav = "account";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $HTTP_GET_VARS["mbrid"];
  $transid = $HTTP_GET_VARS["transid"];

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");
?>

<center>
<form name="delbiblioform" method="POST" action="../circ/mbr_account.php?mbrid=<?php echo $mbrid;?>">
<?php echo $loc->getText("mbrTransDelConfirmMsg"); ?>
<br><br>
      <input type="button" onClick="parent.location='../circ/mbr_transaction_del.php?mbrid=<?php echo $mbrid;?>&transid=<?php echo $transid;?>'" value="<?php echo $loc->getText("circDelete"); ?>" class="button">
      <input type="submit" value="<?php echo $loc->getText("circCancel"); ?>" class="button">
</form>
</center>
