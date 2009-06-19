<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Make sure error messages don't persist. */
unset($_SESSION['pageErrors']);
unset($_SESSION['postVars']);
?>

</div>  <!-- div #content -->

<!--div id="footer">
<a href="http://obiblio.sourceforge.net/">
<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" /></a><br/>
Powered by OpenBiblio version <?php echo H(OBIB_CODE_VERSION);?><br/>
OpenBiblio is free software, copyright by its authors.<br/>
Get <a href="../COPYRIGHT.html">more information</a>.
</div-->

</body>
</html>
