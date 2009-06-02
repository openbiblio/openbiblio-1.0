<?php
/**********************************************************************************
 *   Copyright(C) 2002, 2003 David Stevens
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
<!-- **************************************************************************************
     * Footer
     **************************************************************************************-->
<br><br><br>
</font>
<font face="Arial, Helvetica, sans-serif" size="1" color="<?php echo OBIB_PRIMARY_FONT_COLOR;?>">
<center>
  <?php if (OBIB_LIBRARY_URL != "") { ?>
    <a href="<?php echo OBIB_LIBRARY_URL;?>">Library Home</a> |
  <?php }
  if (OBIB_OPAC_URL != "") { ?>
    <a href="<?php echo OBIB_OPAC_URL;?>">OPAC</a> |
  <?php } ?>
  <a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".$helpPage; ?>')">Help</a>
  <br><br>
    <a href="http://obiblio.sourceforge.net/"><img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0"></a>
  <br><br>
  Powered by OpenBiblio version <?php echo OBIB_VERSION;?><br>
  Copyright &copy; 2002, 2003 Dave Stevens<br>
  under the
  <a href="../shared/copying.html">GNU General Public License</a>
</center>
<br>
</font>
    </td>
  </tr>
</table>
</body>
</html>