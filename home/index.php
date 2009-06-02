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

  $tab = "home";
  $nav = "home";

  require_once("../shared/common.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
?>
<h1><?php print $loc->getText("indexHeading"); ?></h1>

<?php
# print $loc->getText("searchResults",array("items"=>0))."<br>";
?>

<?php print $loc->getText("indexIntro"); ?>

<br><br>
<table class="primary">
  <tr>
    <th><?php print $loc->getText("indexTab"); ?></th><th align="left"><?php print $loc->getText("indexDesc"); ?></th>
  </tr>
  <tr>
    <td align="center" valign="top" class="primary"><?php print $loc->getText("indexCirc"); ?><br><br>
      <img src="../images/circ.png" border="0" width="30" height="30"></td>
    <td class="primary"><?php print $loc->getText("indexCircDesc1"); ?>
      <ul>
        <li><?php print $loc->getText("indexCircDesc2"); ?></li>
        <li><?php print $loc->getText("indexCircDesc3"); ?></li>
        <li><?php print $loc->getText("indexCircDesc4"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td align="center"  valign="top" class="primary"><?php print $loc->getText("indexCat"); ?><br><br>
      <img src="../images/catalog.png" border="0" width="30" height="30"><br><br></td>
    <td valign="top" class="primary"><?php print $loc->getText("indexCatDesc1"); ?>
      <ul>
        <li><?php print $loc->getText("indexCatDesc2"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td align="center"  valign="top" class="primary"><?php print $loc->getText("indexAdmin"); ?><br><br>
      <img src="../images/admin.png" border="0" width="30" height="30"></td>
    <td class="primary"><?php print $loc->getText("indexAdminDesc1"); ?>

      <ul>
        <li><?php print $loc->getText("indexAdminDesc2"); ?></li>
        <li><?php print $loc->getText("indexAdminDesc3"); ?></li>
        <li><?php print $loc->getText("indexAdminDesc4"); ?></li>
        <li><?php print $loc->getText("indexAdminDesc5"); ?></li>
        <li><?php print $loc->getText("indexAdminDesc6"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td align="center"  valign="top" class="primary"><?php print $loc->getText("indexReports"); ?><br><br>
      <img src="../images/reports.png" border="0" width="30" height="30"><br><br></td>
    <td class="primary" valign="top"><?php print $loc->getText("indexReportsDesc1"); ?>

      <ul>
        <li><?php print $loc->getText("indexReportsDesc2"); ?></li>
        <li><?php print $loc->getText("indexReportsDesc3"); ?></li>
      </ul>
    </td>
  </tr>
</table>

<?php include("../shared/footer.php"); ?>
