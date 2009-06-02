<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "materials";

  require_once(REL(__FILE__, "../model/MaterialTypes.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  if ($_GET["msg"]) {
    echo '<p class="error">'.H($_GET["msg"]).'</p><br /><br />';
  }
  $mattypes = new MaterialTypes;
  $types = $mattypes->getAllWithStats();
?>
<h1><?php echo T("Material Types"); ?></h1>
<a href="../admin/materials_new_form.php?reset=Y"><?php echo T("Add New Material Type"); ?></a><br />
<table class="primary">
  <tr>
    <th colspan="3" rowspan="2" valign="top">
      <sup>*</sup><?php echo T("Function"); ?>
    </th>
    <th rowspan="2" valign="top" nowrap="yes">
      <?php echo T("Description"); ?>
    </th>
    <th colspan="2" valign="top">
      <?php echo T("Checkout Limit"); ?>
    </th>
    <th rowspan="2" valign="top">
      <?php echo T("Image File:"); ?>
    </th>
    <th rowspan="2" valign="top">
      <?php echo T("Item Count"); ?>
    </th>
  </tr>
  <tr>
    <th valign="top">
      <?php echo T("Adult"); ?>
    </th>
    <th>
      <?php echo T("Juvenile"); ?>
    </th>
  </tr>
  <?php
    $row_class = "primary";
    while ($type = $types->next()) {
  ?>
  <tr>
    <td valign="top" class="<?php echo H($row_class);?>">
      <a href="../admin/materials_edit_form.php?code=<?php echo H($type['code']);?>" class="<?php echo H($row_class);?>"><?php echo T("edit"); ?></a>
    </td>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <?php if ($type['count'] == 0) { ?>
        <a href="../admin/materials_del_confirm.php?code=<?php echo HURL($type['code']); ?>&desc=<?php echo HURL($type['description']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("del"); ?></a>
      <?php } else { echo T("del"); } ?>
    </td>
	<td valign="top" nowrap="true" class="<?php echo H($row_class); ?>">
    	<a href="../admin/material_fields_view.php?material_cd=<?php echo HURL($type['code']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("MARC Fields"); ?></a>
   	</td>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <?php echo H($type['description']); ?>
    </td>
    <td valign="top" align="center" class="<?php echo H($row_class); ?>">
      <?php echo H($type['adult_checkout_limit']); ?>
    </td>
    <td valign="top" align="center" class="<?php echo H($row_class); ?>">
      <?php echo H($type['juvenile_checkout_limit']); ?>
    </td>
    <td valign="top" class="<?php echo H($row_class); ?>">
      <img src="../images/<?php echo H($type['image_file']); ?>" width="20" height="20" align="middle" alt="<?php echo H($type['description']); ?>">
      <?php echo H($type['image_file']); ?>
    </td>
    <td valign="top" align="center"  class="<?php echo H($row_class); ?>">
      <?php echo H($type['count']); ?>
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
<br />
<p class="note">
<sup>*</sup><?php echo T("Note:"); ?><br />
<?php echo T('materialsListNoteMsg'); ?></p>

<?php

  Page::footer();
