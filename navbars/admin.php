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

  require_once("../classes/Localize.php");
  $navLoc = new Localize(OBIB_LOCALE,"navbars");

?>
<input type="button" onClick="parent.location='../shared/logout.php'" value="<?php echo $navLoc->getText("logout");?>" class="navbutton"><br />
<br />

<?php if ($nav == "summary") { ?>
 &raquo; <?php echo $navLoc->getText("adminSummary");?><br>
<?php } else { ?>
 <a href="../admin/index.php" class="alt1"><?php echo $navLoc->getText("adminSummary");?></a><br>
<?php } ?>

<?php if ($nav == "staff") { ?>
 &raquo; <?php echo $navLoc->getText("adminStaff");?><br>
<?php } else { ?>
 <a href="../admin/staff_list.php" class="alt1"><?php echo $navLoc->getText("adminStaff");?></a><br>
<?php } ?>

<?php if ($nav == "settings") { ?>
 &raquo; <?php echo $navLoc->getText("adminSettings");?><br>
<?php } else { ?>
 <a href="../admin/settings_edit_form.php?reset=Y" class="alt1"><?php echo $navLoc->getText("adminSettings");?></a><br>
<?php } ?>

<?php if ($nav == "materials") { ?>
 &raquo; <?php echo $navLoc->getText("adminMaterialTypes");?><br>
<?php } else { ?>
 <a href="../admin/materials_list.php" class="alt1"><?php echo $navLoc->getText("adminMaterialTypes");?></a><br>
<?php } ?>

<?php if ($nav == "collections") { ?>
 &raquo; <?php echo $navLoc->getText("adminCollections");?><br>
<?php } else { ?>
 <a href="../admin/collections_list.php" class="alt1"><?php echo $navLoc->getText("adminCollections");?></a><br>
<?php } ?>

<?php if ($nav == "themes") { ?>
 &raquo; <?php echo $navLoc->getText("adminThemes");?><br>
<?php } else { ?>
 <a href="../admin/theme_list.php" class="alt1"><?php echo $navLoc->getText("adminThemes");?></a><br>
<?php } ?>

<!--
< ?php if ($nav == "translation") { ?>
 &raquo; < ?php echo $navLoc->getText("adminTranslation");?><br>
< ?php } else { ?>
 <a href="../admin/translation_list.php" class="alt1">< ?php echo $navLoc->getText("adminTranslation");?></a><br>
< ?php } ?>
-->

<a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".$helpPage; ?>')"><?php echo $navLoc->getText("help");?></a>

