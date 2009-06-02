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
  $nav = "summary";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  include("../shared/header.php");

?>

<h1><img src="../images/admin.gif" border="0" width="50" height="50" align="top"> Admin</h1>
Use the following functions located in the left hand navagation area to manage your library's staff
and administrative records.  
<br><br>
<table class="primary">
  <tr>
    <th nowrap="true">Function</th><th align="left">Description</th>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Staff Admin</td>
    <td class="primary">View the library staff member list.  From this list you can
      <ul>
        <li>build a new staff member</li>
        <li>edit the staff member information</li>
        <li>reset a staff member's password</li>
        <li>delete a staff member</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Library Settings</td>
    <td class="primary">Update general library settings.
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Material Types</td>
    <td class="primary">View the list of library material types.  
      A material type describes the physical appearance of a bibliography, such as
      "book" or "video".  Checkout limits can be specified at the material type level.
      From the material type list you can
      <ul>
        <li>build a new material type</li>
        <li>edit a material type</li>
        <li>delete a material type</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Collections</td>
    <td class="primary">View the list of library collections.  
      A collection describes a grouping of bibliographies in the library, such as
      "reference" or "adult fiction".  Collections are usually more specific than
      material types.  The days due back for a bibliography can be specified at a
      collection level.  From the collection list you can.
      <ul>
        <li>build a new material type</li>
        <li>edit a material type</li>
        <li>delete a material type</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Theme Editor</td>
    <td class="primary">View the list of library look and feel themes.  From this list you can 
      <ul>
        <li>set the theme in use for your library</li>
        <li>build a new library theme</li>
        <li>edit an existing library theme</li>
        <li>delete a library theme</li>
      </ul>
    </td>
  </tr>
</table>

<?php include("../shared/footer.php"); ?>
