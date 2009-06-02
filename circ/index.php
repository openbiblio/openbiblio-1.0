<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  session_cache_limiter(null);

  $tab = "circulation";
  $nav = "searchform";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcode_field";

  require_once(REL(__FILE__, "../shared/logincheck.php"));
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
  require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));

?>

<h1><img src="../images/circ.png" border="0" width="30" height="30" align="top"> <?php echo T("Circulation"); ?></h1>
<form name="barcodesearch" method="post" action="../circ/mbr_search.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Get Member by Card Number"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Card Number:"); ?>
      <input type="text" id="barcode_field" name="rpt_terms[0][text]" size="20" maxlength="20" />
      <input type="hidden" name="rpt_terms[0][type]" value="barcode" />
      <input type="hidden" name="rpt_terms[0][exact]" value="1" />
      <input type="submit" value="<?php echo T("Search"); ?>" class="button" />
    </td>
  </tr>
</table>
</form>


<form name="phrasesearch" method="post" action="../circ/mbr_search.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Search Member by Name"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Name Contains:"); ?>
      <input type="text" name="rpt_terms[0][text]" size="30" maxlength="80" />
      <input type="hidden" name="rpt_terms[0][type]" value="name" />
      <input type="hidden" name="rpt_terms[0][exact]" value="0" />
      <input type="submit" value="<?php echo T("Search"); ?>" class="button" />
    </td>
  </tr>
</table>
</form>

<?php

  ReportDisplaysUI::display('circ');
  Page::footer();
