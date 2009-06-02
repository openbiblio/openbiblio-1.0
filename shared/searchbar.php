<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once(REL(__FILE__, "../model/MaterialTypes.php"));
  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
?>
<form name="phrasesearch" method="get" action="../shared/biblio_search.php">
<table class="primary" width="100%">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php echo T("Search Catalog"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <select name="searchType">
        <option value="keyword" selected="selected"><?php echo T("Keyword"); ?></option>
        <option value="title"><?php echo T("Title"); ?></option>
        <option value="subject"><?php echo T("Subject"); ?></option>
        <option value="series"><?php echo T("Series"); ?></option>
        <option value="publisher"><?php echo T("Publisher"); ?></option>
        <option value="callno"><?php echo T("Item Number"); ?></option>
      </select>
    </td>
    <td class="primary">
      <input style="width: 100%" type="text" name="searchText" size="30" maxlength="256" />
      <input type="hidden" name="sortBy" value="title" />
      <input type="hidden" name="tab" value="<?php echo $tab; ?>" />
      <input type="hidden" name="lookup" value="<?php echo $lookup; ?>" />
    </td>
    <td id="searchsubmit">
      <input type="submit" value="<?php echo T("Search"); ?>" class="button" />
    </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php echo T("Limit Search Results"); ?>
    </th>
  </tr>
  <tr>
    <td align="right" class="primary"><strong><?php echo T("Media Type:"); ?></strong></td>
    <td class="primary">
      <?php
        $mattypes = new MaterialTypes;
        echo inputfield('select', 'mediaType', 'all', NULL, $mattypes->getSelect(true));
      ?>
    </td>
  </tr>
  <tr>
    <td align="right" class="primary"><strong><?php echo T("Audience Level:"); ?></strong></td>
    <td class="primary">
      <select name="audienceLevel">
        <option value="all" selected="selected"><?php echo T("All"); ?></option>
        <option value="K"><?php echo T("Kindergarten"); ?></option>
        <option value="P"><?php echo T("Primary"); ?></option>
        <option value="I"><?php echo T("Intermediate"); ?></option>
        <option value="J"><?php echo T("Junior High"); ?></option>
        <option value="S"><?php echo T("Senior High"); ?></option>
        <option value="A"><?php echo T("Adult"); ?></option>
      </select>
    </td>
  </tr>
  <tr>
    <td align="right" class="primary"><strong><?php echo T("Production Date:"); ?></strong></td>
    <td class="primary">
      <strong>
        <?php echo T("From Year:"); ?> <input type="text" name="from" size="4" />
        <?php echo T("To Year:"); ?> <input type="text" name="to" size="4" />
      </strong>
    </td>
  </tr>
</table>
</form>
