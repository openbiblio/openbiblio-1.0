<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "member_fields";

  require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));


  $membfields = new MemberCustomFields;
  $fields = $membfields->getAll();

?>
<h1><?php echo T("Custom Member Fields"); ?></h1>
<a href="../admin/member_fields_new_form.php?reset=Y"><?php echo T("Add new custom field"); ?></a><br />
<table class="primary">
  <tr>
    <th colspan="2" valign="top">
      <sup>*</sup><?php echo T("Function"); ?>
    </th>
    <th valign="top" nowrap="yes">
      <?php echo T("Code"); ?>
    </th>
    <th valign="top" nowrap="yes">
      <?php echo T("Description"); ?>
    </th>
  </tr>
  <?php
    $row_class = "primary";
    while (($field = $fields->next()) !== NULL) {
  ?>
  <tr>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <a href="../admin/member_fields_edit_form.php?code=<?php echo HURL($field['code']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("edit"); ?></a>
    </td>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <a href="../admin/member_fields_del_confirm.php?code=<?php echo HURL($field['code']); ?>&amp;desc=<?php echo HURL($field['description']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("del"); ?></a>
    </td>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <?php echo H($field['code']); ?>
    </td>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <?php echo H($field['description']); ?>
    </td>
  </tr>
  <?php
      # swap row color
      if ($row_class == "primary") {
        $row_class = "alt1";
      } else {
        $row_class = "primary";
      }
    }
  ?>
</table>

<?php

  Page::footer();
