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
<a href="../admin/staff_new_form.php?reset=Y">Add New Staff Member</a><br><br>
<h1> Staff Members:</h1>
<table class="primary">
  <tr>
    <th colspan="3" rowspan="2" valign="top">
      Function
    </th>
    <th rowspan="2" valign="top" nowrap="yes">
      Last Name
    </th>
    <th rowspan="2" valign="top" nowrap="yes">
      First Name
    </th>
    <th rowspan="2" valign="top">
      Userid
    </th>
    <th colspan="5">
      Authorization
    </th>
    <th rowspan="2" valign="top">
      Suspended
    </th>
  </tr>
  <tr>
    <th>
      Circ
    </th>
    <th>
      Member
    </th>
    <th>
      Catalog
    </th>
    <th>
      Admin
    </th>
    <th>
      Reports
    </th>
  </tr>
  <?php
    $row_class = "primary";
    while ($staff = $staffQ->fetchStaff()) {
  ?>
  <tr>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/staff_edit_form.php?UID=<?php echo $staff->getUserid();?>" class="<?php echo $row_class;?>">edit</a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/staff_pwd_reset_form.php?UID=<?php echo $staff->getUserid();?>" class="<?php echo $row_class;?>">pwd</a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/staff_del_confirm.php?UID=<?php echo $staff->getUserid();?>&LAST=<?php echo urlencode($staff->getLastName());?>&FIRST=<?php echo urlencode($staff->getFirstName());?>" class="<?php echo $row_class;?>">del</a>
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
        echo "yes";
      } else {
        echo "no";
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasCircMbrAuth()) {
        echo "yes";
      } else {
        echo "no";
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasCatalogAuth()) {
        echo "yes";
      } else {
        echo "no";
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasAdminAuth()) {
        echo "yes";
      } else {
        echo "no";
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->hasReportsAuth()) {
        echo "yes";
      } else {
        echo "no";
      } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($staff->isSuspended()) {
        echo "yes";
      } else {
        echo "no";
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
