<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Make sure error messages don't persist. */
unset($_SESSION['pageErrors']);
unset($_SESSION['postVars']);
?>
<!-- **************************************************************************************
     * Footer
     **************************************************************************************-->
<div id="footer">
  <?php if (Settings::get('library_url') != "") { ?>
    <a href="<?php echo H(Settings::get('library_url'));?>">Library Home</a> |
  <?php }
  if (Settings::get('opac_url') != "") { ?>
    <a href="<?php echo H(Settings::get('opac_url'));?>">OPAC</a>
  <?php } ?>
  <br /><br />
    <a href="http://obiblio.sourceforge.net/"><img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0"></a>
  <br /><br />
  Powered by OpenBiblio version <?php echo H(OBIB_CODE_VERSION);?><br />
  OpenBiblio is free software, copyright by its authors.<br />
  Get <a href="../COPYRIGHT.html">more information</a>.
</div>
    </td>
  </tr>
</table>
</body>
</html>
