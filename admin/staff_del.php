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
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/StaffQuery.php");
  require_once("../functions/errorFuncs.php");

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
  #*  Delete staff member
  #**************************************************************************
  $staffQ = new StaffQuery();
  $staffQ->connect();
  if ($staffQ->errorOccurred()) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  if (!$staffQ->delete($userid)) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  $staffQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
Staff member, <?php echo $first_name;?> <?php echo $last_name;?>, has been deleted.<br><br>
<a href="../admin/staff_list.php">return to staff list</a>

<?php require_once("../shared/footer.php"); ?>
