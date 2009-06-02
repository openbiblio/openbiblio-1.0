<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
 if (ereg('[^a-zA-Z0-9_]', $tab)) {
   Fatal::internalError("Possible security violation: bad tab name");
   exit(); # just in case
 }
 
  include("../shared/header_top.php");
;?>


<!-- **************************************************************************************
     * Left nav
     **************************************************************************************-->
<table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo H(OBIB_ALT1_BG);?>">
    <td colspan="6"><img src="../images/shim.gif" width="1" height="15" border="0"></td>
  </tr>
  <tr>
    <td bgcolor="<?php echo H(OBIB_ALT1_BG);?>"><img src="../images/shim.gif" width="10" height="1" border="0"></td>
    <td bgcolor="<?php echo H(OBIB_ALT1_BG);?>"><img src="../images/shim.gif" width="140" height="1" border="0"></td>
    <td bgcolor="<?php echo H(OBIB_BORDER_COLOR);?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo H(OBIB_BORDER_COLOR);?>"><img src="../images/shim.gif" width="10" height="1" border="0"></td>
    <td bgcolor="<?php echo H(OBIB_BORDER_COLOR);?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo H(OBIB_BORDER_COLOR);?>"><img src="../images/shim.gif" width="10" height="1" border="0"></td>
  </tr>
  <tr>
    <td bgcolor="<?php echo H(OBIB_ALT1_BG);?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td valign="top" bgcolor="<?php echo H(OBIB_ALT1_BG);?>">
      <font  class="alt1">
      <?php include("../navbars/".$tab.".php");?>
      </font>
    <br><br><br><br>
    </td>
    <td bgcolor="<?php echo H(OBIB_BORDER_COLOR);?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo H(OBIB_PRIMARY_BG);?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td height="100%" width="100%" valign="top">
      <font class="primary">
      <br>
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->
