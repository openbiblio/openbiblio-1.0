<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  session_cache_limiter(null);

  $tab = "admin";
  $nav = "bibliocopys";
  $focus_form_name = "editbibliocopyform";
  $focus_form_field = "description";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($_GET["code"])){
    $code = $_GET["code"];
    $postVars["code"] = $code;

    include_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
    $bibliocopys = new BiblioCopyFields;
    $BCF = $bibliocopys->getOne($code);
    $postVars = $BCF;
    $_SESSION['postVars'] = $postVars;
  } else {
    require(REL(__FILE__, "../shared/get_form_vars.php"));
  }
?>

<form name="editbibliocopyform" method="post" action="../admin/biblio_copy_fields_edit.php">
<input type="hidden" name="code" value="<?php echo $postVars["code"];?>">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      <?php echo T("Edit Biblio Copy Field:"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <sup>*</sup><?php echo T("Description:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo inputfield('text','description'); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
      <input type="button" onclick="parent.location='../admin/biblio_copy_fields_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
    </td>
  </tr>

</table>
</form>

<?php

  Page::footer();
