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
  $nav = "staff";

  require_once("../classes/Staff.php");
  require_once("../classes/StaffQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../shared/read_settings.php");

  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  require_once("../shared/logincheck.php");

  require_once("../shared/header.php");

  $staffQ = new StaffQuery();
  $staffQ->connect();
  if ($staffQ->errorOccurred()) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  $staffQ->execSelect();
  if ($staffQ->errorOccurred()) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }

?>
<a href="../admin/staff_new_form.php?reset=Y"><? echo $loc->getText("adminStaff_list_formHeader"); ?></a><br><br>
<h1><? echo $loc->getText("adminStaff_list_Columnheader"); ?></h1>
<table class="primary">
  <tr>
    <th colspan="3" rowspan="2" valign="top">
      <? echo $loc->getText("adminStaff_list_Function"); ?>
    </th>
    <th rowspan="2" valign="top" nowrap="yes">
      <? echo $loc->getText("adminStaff_edit_formLastname"); ?>
    </th>
    <th rowspan="2" valign="top" nowrap="yes">
      <? echo $loc->getText("adminStaff_edit_formFirstname"); ?>
    </th>
    <th rowspan="2" valign="top">
      <? echo $loc->getText("adminStaff_edit_formLogin"); ?>
    </th>
    <th colspan="5">
      <? echo $loc->getText("adminStaff_edit_formAuth"); ?>
    </th>
    <th rowspan="2" valign="top">
      <? echo $loc->getText("adminStaff_edit_formSuspended"); ?>
    </th>
  </tr>
  <tr>
    <th>
      <? echo $loc->getText("adminStaff_edit_formCirc"); ?>
    </th>
    <th>
      <? echo $loc->getText("adminStaff_edit_formUpdatemember"); ?>
    </th>
    <th>
      <? echo $loc->getText("adminStaff_edit_formCatalog"); ?>
    </th>
    <th>
      <? echo $loc->getText("adminStaff_edit_formAdmin"); ?>
    </th>
    <th>
      <? echo $loc->getText("adminStaff_edit_formReports"); ?>
    </th>
  </tr>
  <?php
    $row_class = "primary";
    while ($staff = $staffQ->fetchStaff()) {
  ?>
  <tr>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/staff_edit_form.php?UID=<?php echo $staff->getUserid();?>" class="<?php echo $row_class;?>"><? echo $loc->getText("adminStaff_list_Edit"); ?></a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/staff_pwd_reset_form.php?UID=<?php echo $staff->getUserid();?>" class="<?php echo $row_class;?>"><? echo $loc->getText("adminStaff_list_Pwd"); ?></a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/staff_del_confirm.php?UID=<?php echo $staff->getUserid();?>&LAST=<?php echo urlencode($staff->getLastName());?>&FIRST=<?php echo urlencode($staff->getFirstName());?>" class="<?php echo $row_class;?>"><? echo $loc->getText("adminStaff_list_Del"); ?></a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php echo $staff->getLastName();?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php echo $staff->getFirstName();?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php echo $staff->getUsername();?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasCircAuth()) {
        echo $loc->getText("adminStaff_Yes");
      } else {
        echo $loc->getText("adminStaff_No");
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasCircMbrAuth()) {
        echo $loc->getText("adminStaff_Yes");
      } else {
        echo $loc->getText("adminStaff_No");
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasCatalogAuth()) {
        echo $loc->getText("adminStaff_Yes");
      } else {
        echo $loc->getText("adminStaff_No");
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasAdminAuth()) {
        echo $loc->getText("adminStaff_Yes");
      } else {
        echo $loc->getText("adminStaff_No");
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasReportsAuth()) {
        echo $loc->getText("adminStaff_Yes");
      } else {
        echo $loc->getText("adminStaff_No");
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->isSuspended()) {
        echo $loc->getText("adminStaff_Yes");
      } else {
        echo $loc->getText("adminStaff_No");
      } ?>
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
    $staffQ->close();
  ?>
</table>
<?php include("../shared/footer.php"); ?>
