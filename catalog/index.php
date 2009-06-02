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
  $nav = "summary";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  include("../shared/header.php");

?>

<h1><img src="../images/catalog.gif" border="0" width="50" height="50" align="top"> Cataloging</h1>
Use the following functions located in the left hand navagation area to manage your library's bibliography records.  
<br><br>
<table class="primary">
  <tr>
    <th nowrap="true">Function</th><th align="left">Description</th>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Bibliography Search</td>
    <td class="primary">Search and view library bibliography records.  Once a bibliography record
      is selected you can
      <ul>
        <li>edit the information</li>
        <li>make a copy</li>
        <li>delete the bibliography</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">New Bibliography</td>
    <td class="primary">Build a new library bibliography record.
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Import Bibliography</td>
    <td class="primary">Build a new library bibliography record from a USMarc record file.
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary">Reports</td>
    <td class="primary">Access bibliography reports.
    </td>
  </tr>
</table>

<?php include("../shared/footer.php"); ?>
