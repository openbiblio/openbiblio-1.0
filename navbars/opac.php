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


<?php if ($nav == "home") { ?>
 &raquo; <?php echo $navLoc->getText("catalogSearch1"); ?><br>
<?php } else { ?>
 <a href="../opac/index.php" class="alt1"><?php echo $navLoc->getText("catalogSearch2"); ?></a><br>
<?php } ?>

<?php if ($nav == "search") { ?>
 &raquo; <?php echo $navLoc->getText("catalogResults"); ?><br>
<?php } ?>

<?php if ($nav == "view") { ?>
 &raquo; <?php echo $navLoc->getText("catalogBibInfo"); ?><br>
<?php } ?>

<a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".$helpPage; ?>')"><?php echo $navLoc->getText("Help"); ?></a>
