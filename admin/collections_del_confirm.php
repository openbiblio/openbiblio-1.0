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
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  #****************************************************************************
  #*  Checking for query string.  Go back to collection list if none found.
  #****************************************************************************
  if (!isset($HTTP_GET_VARS["code"])){
    header("Location: ../admin/collections_list.php");
    exit();
  }
  $code = $HTTP_GET_VARS["code"];
  $description = $HTTP_GET_VARS["desc"];

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<center>
<form name="delstaffform" method="POST" action="../admin/collections_del.php?code=<?php echo $code;?>&desc=<?php echo urlencode($description);?>">
Are you sure you want to delete collection, <?php echo $description;?>?<br><br>
      <input type="submit" value="  Delete  ">
      <input type="button" onClick="parent.location='../admin/collections_list.php'" value="  Cancel  ">
</form>
</center>
<?php include("../shared/footer.php"); ?>
