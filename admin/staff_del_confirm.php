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
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  #****************************************************************************
  #*  Checking for query string.  Go back to staff list if none found.
  #****************************************************************************
  if (!isset($HTTP_GET_VARS["UID"])){
    header("Location: ../admin/staff_list.php");
    exit();
  }
  $userid = $HTTP_GET_VARS["UID"];
  $last_name = $HTTP_GET_VARS["LAST"];
  $first_name = $HTTP_GET_VARS["FIRST"];

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<center>
<form name="delstaffform" method="POST" action="../admin/staff_del.php?UID=<?php echo $userid;?>&LAST=<?php echo $last_name;?>&FIRST=<?php echo $first_name;?>">
Are you sure you want to delete staff member, <?php echo $first_name;?> <?php echo $last_name;?>?<br><br>
      <input type="submit" value="  Delete  " class="button">
      <input type="button" onClick="parent.location='../admin/staff_list.php'" value="  Cancel  " class="button">
</form>
</center>
<?php include("../shared/footer.php"); ?>
