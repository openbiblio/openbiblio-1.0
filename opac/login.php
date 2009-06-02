<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/Members.php"));

$msg = '';
if (isset($_POST['id']) and isset($_POST['password'])) {
  $members = new Members;
  $mbrid = $members->loginMbrid($_POST['id'], $_POST['password']);
  if ($mbrid) {
    $_SESSION['authMbrid'] = $mbrid;
    header("Location: ../opac/my_account.php");
    exit();
  } else {
    $msg = T("Invalid ID or password");
  }
}

Page::header_opac(array('nav'=>$nav, 'title'=>''));

if ($msg) {
  echo '<p class="error">'.H($msg).'</p>';
}

?>
<form action="../opac/login.php" method="post">
<table>
<tr>
<td align="right"><b><?php echo T("Email or ID Number:"); ?></b></td>
<td><input type="text" name="id" value="<?php if (isset($_POST[id])) echo H($_POST[id]) ?>" /></td>
</tr>
<tr>
<td align="right"><b><?php echo T("Password:"); ?></b></td>
<td><input type="password" name="password" /></td>
</tr>
<tr><td></td><td><input class="button" type="submit" value="<?php echo T("Login"); ?>" /></td></tr>
</table>
</form>
<?php
Page::footer();
