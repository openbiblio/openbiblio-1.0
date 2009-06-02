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
  $nav = "summary";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  include("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

?>

<h1><img src="../images/circ.gif" border="0" width="50" height="50" align="top"> <?php print $loc->getText("indexHeading"); ?></h1>
<?php print $loc->getText("indexIntro"); ?>

<br><br>
<table class="primary">
  <tr>
    <th nowrap="true"><?php print $loc->getText("indexFunc"); ?></th><th align="left"><?php print $loc->getText("indexDesc"); ?></th>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary"><?php print $loc->getText("indexMbrSrch"); ?></td>
    <td class="primary"><?php print $loc->getText("indexMbrSrchDesc1"); ?>
      <ul>
        <li><?php print $loc->getText("indexMbrSrchDesc2"); ?></li>
        <li><?php print $loc->getText("indexMbrSrchDesc3"); ?></li>
        <li><?php print $loc->getText("indexMbrSrchDesc4"); ?></li>
        <li><?php print $loc->getText("indexMbrSrchDesc5"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary"><?php print $loc->getText("indexNewMbr"); ?></td>
    <td class="primary"><?php print $loc->getText("indexNewMbrDesc"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary"><?php print $loc->getText("indexCheckIn"); ?></td>
    <td class="primary"><?php print $loc->getText("indexCheckInDesc"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" align="center" valign="top" class="primary"><?php print $loc->getText("indexReports"); ?></td>
    <td class="primary"><?php print $loc->getText("indexReportsDesc"); ?>
    </td>
  </tr>
</table>

<?php include("../shared/footer.php"); ?>
