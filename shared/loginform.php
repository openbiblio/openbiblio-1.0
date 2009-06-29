<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__,"../functions/inputFuncs.php"));

	$temp_return_page = "";
	if (isset($_GET["RET"])){
		$_SESSION["returnPage"] = $_GET["RET"];
	}

	$tab = "circ";
	$nav = "";
	$focus_form_name = "loginform";
	$focus_form_field = "username";

	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	# for later use by inputfields()
	$attrs = array(size=>"20", maxlength=>"20");

?>

<br />
<center>
<form name="loginform" method="post" action="../shared/login.php">
<table class="primary">
	<tr>
		<th><?php echo T("Staff Login"); ?></th>
	</tr>
	<tr>
		<td valign="top" class="primary" align="left">
<table class="primary">
	<tr>
		<td valign="top" class="noborder">
			<?php echo T("Username:"); ?>
		</td>
		<td valign="top" class="noborder">
			<?php
				echo inputfield('text','username',$postVars["username"],$attrs);
			?>
		</td>
	</tr>
	<tr>
		<td valign="top" class="noborder">
			<?php echo T("Password:"); ?>
		</td>
		<td valign="top" class="noborder">
			<?php
				echo inputfield('password','pwd',$postVars["pwd"],$attrs);
			?>
		</td>
	</tr>

	<tr>
		<td colspan="2" align="center" class="noborder">
			<input type="submit" value="<?php echo T("Login"); ?>" class="button" />
		</td>
	</tr>
</table>
		</td>
	</tr>
</table>

</form>
</center>

<?php

	Page::footer();
