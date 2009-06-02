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
?>
<a href="../shared/logout.php"><img src="../images/logout.gif" width="64" height="20" border="0"></a>
<br><br>

<?php if ($nav == "summary") { ?>
 &raquo; Admin Summary<br>
<?php } else { ?>
 <a href="../admin/index.php" class="alt1">Admin Summary</a><br>
<?php } ?>

<?php if ($nav == "staff") { ?>
 &raquo; Staff Admin<br>
<?php } else { ?>
 <a href="../admin/staff_list.php" class="alt1">Staff Admin</a><br>
<?php } ?>

<?php if ($nav == "settings") { ?>
 &raquo; Library Settings<br>
<?php } else { ?>
 <a href="../admin/settings_edit_form.php?reset=Y" class="alt1">Library Settings</a><br>
<?php } ?>

<?php if ($nav == "materials") { ?>
 &raquo; Material Types<br>
<?php } else { ?>
 <a href="../admin/materials_list.php" class="alt1">Material Types</a><br>
<?php } ?>

<?php if ($nav == "collections") { ?>
 &raquo; Collections<br>
<?php } else { ?>
 <a href="../admin/collections_list.php" class="alt1">Collections</a><br>
<?php } ?>

<?php if ($nav == "themes") { ?>
 &raquo; Themes<br>
<?php } else { ?>
 <a href="../admin/theme_list.php" class="alt1">Themes</a><br>
<?php } ?>

<a href="javascript:popSecondary('../doc/index.php?tab=<?php echo $tab;?>@nav=<?php echo $nav;?>')">Help</a>

