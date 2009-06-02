<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  session_cache_limiter(null);

  $tab = "admin";
  $nav = "staff";
  $focus_form_name = "newstaffform";
  $focus_form_field = "last_name";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../shared/get_form_vars.php"));
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<form name="newstaffform" method="post" action="../admin/staff_new.php">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      <?php echo T("Add New Staff Member");?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Last Name:");?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("last_name",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("First Name:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("first_name",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Login Username:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("username",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Password:"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="password" name="pwd" size="20" maxlength="20"
      value="<?php if (isset($postVars["pwd"])) echo $postVars["pwd"]; ?>" ><br />
      <?php if (isset($pageErrors["pwd"])) {
				echo '<span class="error">'.$pageErrors["pwd"].'</span>';
				} ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Re-enter Password:"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="password" name="pwd2" size="20" maxlength="20"
      value="<?php if (isset($postVars["pwd2"])) echo $postVars["pwd2"]; ?>" ><br />
      <?php if (isset($pageErrors["pwd2"])) {
				echo '<span class="error">'.$pageErrors["pwd2"].'</span>';
				} ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Authorization:");?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="circ_flg" value="CHECKED"
        <?php if (isset($postVars["circ_flg"])) echo $postVars["circ_flg"]; ?> />
      <?php echo T("Circ");?>
      <input type="checkbox" name="circ_mbr_flg" value="CHECKED"
        <?php if (isset($postVars["circ_mbr_flg"])) echo $postVars["circ_mbr_flg"]; ?> />
      <?php echo T("Update Member"); ?>
      <input type="checkbox" name="catalog_flg" value="CHECKED"
        <?php if (isset($postVars["catalog_flg"])) echo $postVars["catalog_flg"]; ?> />
      <?php echo T("Catalog"); ?>
      <input type="checkbox" name="admin_flg" value="CHECKED"
        <?php if (isset($postVars["admin_flg"])) echo $postVars["admin_flg"]; ?> />
      <?php echo T("Admin");?>
      <input type="checkbox" name="reports_flg" value="CHECKED"
        <?php if (isset($postVars["reports_flg"])) echo $postVars["reports_flg"]; ?> />
     <?php echo T("Reports"); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
      <input type="button" onClick="parent.location='../admin/staff_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
    </td>
  </tr>

</table>
</form>

<?php

  Page::footer();
