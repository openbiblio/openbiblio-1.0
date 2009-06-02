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

  $tab = "opac";
  $nav = "home";
  $focus_form_name = "phrasesearch";
  $focus_form_field = "searchText";
  require_once("../shared/read_settings.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  require_once("../shared/header_opac.php");

  $lookup = "N";
  if (isset($HTTP_GET_VARS["lookup"])) {
    $lookup = "Y";
  }
?>

<h1><?php echo $loc->getText("opac_Header");?></h1>
<?php echo $loc->getText("opac_WelcomeMsg");?>
<form name="phrasesearch" method="POST" action="../shared/biblio_search.php">
<br />
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("opac_SearchTitle");?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <select name="searchType">
        <option value="title" selected><?php echo $loc->getText("opac_Title");?>
        <option value="author"><?php echo $loc->getText("opac_Author");?>
        <option value="subject"><?php echo $loc->getText("opac_Subject");?>
      </select>
      <input type="text" name="searchText" size="30" maxlength="256">
      <input type="hidden" name="sortBy" value="default">
      <input type="hidden" name="tab" value="<?php echo $tab; ?>">
      <input type="hidden" name="lookup" value="<?php echo $lookup; ?>">
      <input type="submit" value="<?php echo $loc->getText("opac_Search");?>" class="button">
    </td>
  </tr>
</table>
</form>

<?php include("../shared/footer.php"); ?>
