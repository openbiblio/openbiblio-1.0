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
  $navloc = new Localize(OBIB_LOCALE,"navbars");
 
?>
<input type="button" onClick="parent.location='../shared/logout.php'" value="<?php echo $navloc->getText("Logout"); ?>" class="navbutton"><br />
<br />

<?php if ($nav == "searchform") { ?>
 &raquo; <?php echo $navloc->getText("memberSearch"); ?><br>
<?php } else { ?>
 <a href="../circ/index.php" class="alt1"><?php echo $navloc->getText("memberSearch"); ?></a><br>
<?php } ?>

<?php if ($nav == "search") { ?>
 &nbsp; &raquo; <?php echo $navloc->getText("catalogResults"); ?><br>
<?php } ?>

<?php if ($nav == "view") { ?>
 &nbsp; &raquo; <?php echo $navloc->getText("memberInfo"); ?><br>
 &nbsp; &nbsp; <a href="../circ/mbr_edit_form.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("editInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("catalogDelete"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_account.php?mbrid=<?php echo $mbrid;?>&reset=Y" class="alt1"><?php echo $navloc->getText("account"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_history.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("checkoutHistory"); ?></a><br>
<?php } ?>

<?php if ($nav == "edit") { ?>
 &nbsp; <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("memberInfo"); ?></a><br>
 &nbsp; &nbsp; &raquo; <?php echo $navloc->getText("editInfo"); ?><br>
 &nbsp; &nbsp; <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("catalogDelete"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_account.php?mbrid=<?php echo $mbrid;?>&reset=Y" class="alt1"><?php echo $navloc->getText("account"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_history.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("checkoutHistory"); ?></a><br>
<?php } ?>

<?php if ($nav == "delete") { ?>
 &nbsp; <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("memberInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_edit_form.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("editInfo"); ?></a><br>
 &nbsp; &nbsp; &raquo; <?php echo $navloc->getText("catalogDelete"); ?><br>
 &nbsp; &nbsp; <a href="../circ/mbr_account.php?mbrid=<?php echo $mbrid;?>&reset=Y" class="alt1"><?php echo $navloc->getText("account"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_history.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("checkoutHistory"); ?></a><br>
<?php } ?>

<?php if ($nav == "hist") { ?>
 &nbsp; <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("memberInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_edit_form.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("editInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("catalogDelete"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_account.php?mbrid=<?php echo $mbrid;?>&reset=Y" class="alt1"><?php echo $navloc->getText("account"); ?></a><br>
 &nbsp; &nbsp; &raquo; <?php echo $navloc->getText("checkoutHistory"); ?><br>
<?php } ?>

<?php if ($nav == "account") { ?>
 &nbsp; <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("memberInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_edit_form.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("editInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("catalogDelete"); ?></a><br>
 &nbsp; &nbsp; &raquo; <?php echo $navloc->getText("account"); ?><br>
 &nbsp; &nbsp; <a href="../circ/mbr_history.php?mbrid=<?php echo $mbrid;?>" class="alt1"><?php echo $navloc->getText("checkoutHistory"); ?></a><br>
<?php } ?>

<?php if ($nav == "new") { ?>
 &raquo; <?php echo $navloc->getText("newMember"); ?><br>
<?php } else { ?>
 <a href="../circ/mbr_new_form.php?reset=Y" class="alt1"><?php echo $navloc->getText("newMember"); ?></a><br>
<?php } ?>

<?php if ($nav == "checkin") { ?>
 &raquo; <?php echo $navloc->getText("checkIn"); ?><br>
<?php } else { ?>
 <a href="../circ/checkin_form.php?reset=Y" class="alt1"><?php echo $navloc->getText("checkIn"); ?></a><br>
<?php } ?>

<a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".$helpPage; ?>')"><?php echo $navloc->getText("help"); ?></a>
