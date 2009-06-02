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
 &raquo; Circ Summary<br>
<?php } else { ?>
 <a href="../circ/index.php" class="alt1">Circulation Summary</a><br>
<?php } ?>

<?php if ($nav == "searchform") { ?>
 &raquo; Member Search<br>
<?php } else { ?>
 <a href="../circ/mbr_search_form.php" class="alt1">Member Search</a><br>
<?php } ?>

<?php if ($nav == "search") { ?>
 &nbsp; &raquo; Search Results<br>
<?php } ?>

<?php if ($nav == "view") { ?>
 &nbsp; &raquo; Member Info<br>
 &nbsp; &nbsp; <a href="../circ/mbr_edit_form.php?mbrid=<?php echo $mbrid;?>" class="alt1">Edit Info</a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo $mbrid;?>&name=<?php echo $mbrName;?>" class="alt1">Delete</a><br>
 &nbsp; &nbsp; Pay Late Fee<br>
<?php } ?>

<?php if ($nav == "edit") { ?>
 &nbsp; <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>" class="alt1">Member Info</a><br>
 &nbsp; &nbsp; &raquo; Edit Info<br>
 &nbsp; &nbsp; <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo $mbrid;?>&name=<?php echo $mbrName;?>" class="alt1">Delete</a><br>
 &nbsp; &nbsp; Pay Late Fee<br>
<?php } ?>

<?php if ($nav == "delete") { ?>
 &nbsp; <a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>" class="alt1">Member Info</a><br>
 &nbsp; &nbsp; <a href="../circ/mbr_edit_form.php?mbrid=<?php echo $mbrid;?>" class="alt1">Edit Info</a><br>
 &nbsp; &nbsp; &raquo; Delete<br>
 &nbsp; &nbsp; Pay Late Fee<br>
<?php } ?>

<?php if ($nav == "new") { ?>
 &raquo; New Member<br>
<?php } else { ?>
 <a href="../circ/mbr_new_form.php?reset=Y" class="alt1">New Member</a><br>
<?php } ?>

<?php if ($nav == "checkin") { ?>
 &raquo; Check In<br>
<?php } else { ?>
 <a href="../circ/checkin_form.php?reset=Y" class="alt1">Check In</a><br>
<?php } ?>

Reports<br>
<a href="javascript:popSecondary('../doc/index.php?tab=<?php echo $tab;?>@nav=<?php echo $nav;?>')">Help</a>
