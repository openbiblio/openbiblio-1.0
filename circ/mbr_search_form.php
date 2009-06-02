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

?>

<form name="barcodesearch" method="POST" action="../circ/mbr_search.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      Search Member by Card Number:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Card Number:
      <input type="text" name="searchText" size="18" maxlength="18">
      <input type="hidden" name="searchType" value="barcodeNmbr">
      <input type="submit" value="Search">
    </td>
  </tr>
</table>
</form>


<form name="phrasesearch" method="POST" action="../circ/mbr_search.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      Search Member by Last Name:
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Last name starts with:
      <input type="text" name="searchText" size="30" maxlength="80">
      <input type="hidden" name="searchType" value="lastName">
      <input type="submit" value="Search">
    </td>
  </tr>
</table>
</form>

<?php include("../shared/footer.php"); ?>
