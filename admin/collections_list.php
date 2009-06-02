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

  $tab = "admin";
  $nav = "collections";

  require_once("../classes/Dm.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../shared/header.php");

  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelectWithStats("collection_dm");
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }

?>
<a href="../admin/collections_new_form.php?reset=Y">Add New Collection</a><br>
<h1>Collections:</h1>
<table class="primary">
  <tr>
    <th colspan="2" valign="top">
      <font class="small">*</font>Function
    </th>
    <th valign="top">
      Description
    </th>
    <th valign="top">
      Days<br>Due Back
    </th>
    <th valign="top">
      Daily<br>Late Fee
    </th>
    <th valign="top">
      Bibliography<br>Count
    </th>
  </tr>
  <?php
    $row_class = "primary";
    while ($dm = $dmQ->fetchRow()) {
  ?>
  <tr>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/collections_edit_form.php?code=<?php echo $dm->getCode();?>" class="<?php echo $row_class;?>">edit</a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($dm->getCount() == 0) { ?>
        <a href="../admin/collections_del_confirm.php?code=<?php echo $dm->getCode();?>&desc=<?php echo urlencode($dm->getDescription());?>" class="<?php echo $row_class;?>">del</a>
      <?php } else { echo "del"; }?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php echo $dm->getDescription();?>
    </td>
    <td valign="top" align="center" class="<?php echo $row_class;?>">
      <?php echo $dm->getDaysDueBack();?>
    </td>
    <td valign="top" align="center" class="<?php echo $row_class;?>">
      <?php echo moneyFormat($dm->getDailyLateFee(),2); ?>
    </td>
    <td valign="top" align="center"  class="<?php echo $row_class;?>">
      <?php echo $dm->getCount();?>
    </td>
  </tr>
  <?php
      # swap row color
      if ($row_class == "primary") {
        $row_class = "alt1";
      } else {
        $row_class = "primary";
      }
    }
    $dmQ->close();
  ?>
</table>
<br>
<table class="primary"><tr><td valign="top" class="noborder"><font class="small">*Note:</font></td>
<td class="noborder"><font class="small">The delete function is only available on collections that have a bibliography count of zero.
If you wish to delete a collection with a bibliography count greater than zero you will first need to
change the material type on those bibliographies to another material type.<br></font>
</td></tr></table>
<?php include("../shared/footer.php"); ?>
