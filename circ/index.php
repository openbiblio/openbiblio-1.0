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

  session_cache_limiter(null);

  $tab = "circulation";
  $nav = "searchform";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "searchText";

  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

?>

<h1><img src="../images/circ.png" border="0" width="30" height="30" align="top"> <?php print $loc->getText("indexHeading"); ?></h1>
<form name="barcodesearch" method="POST" action="../circ/mbr_search.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("indexCardHdr"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("indexCard"); ?>
      <input type="text" name="searchText" size="20" maxlength="20">
      <input type="hidden" name="searchType" value="barcodeNmbr">
      <input type="submit" value="<?php print $loc->getText("indexSearch"); ?>" class="button">
    </td>
  </tr>
</table>
</form>


<form name="phrasesearch" method="POST" action="../circ/mbr_search.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("indexNameHdr"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("indexName"); ?>
      <input type="text" name="searchText" size="30" maxlength="80">
      <input type="hidden" name="searchType" value="lastName">
      <input type="submit" value="<?php print $loc->getText("indexSearch"); ?>" class="button">
    </td>
  </tr>
</table>
</form>

<?php include("../shared/footer.php"); ?>
