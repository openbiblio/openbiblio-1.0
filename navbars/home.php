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

<?php if (!isset($sess_userid) or (isset($sess_userid) and $sess_userid == "")) { ?>
  <a href="../shared/loginform.php?RET=<?php echo $PHP_SELF;?>"><img src="../images/login.gif" width="64" height="20" border="0"></a>
<?php } else { ?>
  <a href="../shared/logout.php"><img src="../images/logout.gif" width="64" height="20" border="0"></a>
<?php } ?>
<br><br>

<?php if ($nav == "home") { ?>
 &raquo; Home<br>
<?php } else { ?>
 <a href="../home/index.php" class="alt1">Home</a><br>
<?php } ?>

<?php if ($nav == "license") { ?>
 &raquo; License<br>
<?php } else { ?>
 <a href="../home/license.php" class="alt1">License</a><br>
<?php } ?>

<a href="javascript:popSecondary('../doc/index.php?tab=<?php echo $tab;?>@nav=<?php echo $nav;?>')">Help</a>
