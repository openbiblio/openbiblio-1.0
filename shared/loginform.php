<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__,"../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));	

	## assure all session values are current
	setSessionFmSettings(); // part of ../shared/common.php
	
	// Need to be initialised to make the decission to show the site dropdown list - LJ
//	if(!isset($_SESSION['show_copy_site']))
//		$_SESSION['show_copy_site'] = Settings::get('show_copy_site');
	
	$temp_return_page = "";
	if (isset($_GET["RET"])){
		$_SESSION["returnPage"] = $_GET["RET"];
	}

	$sites_table = new Sites;		
	$sites = $sites_table->getSelect();	

	// If the current_site is set, default to this site, otherwise default
	if(isset($_REQUEST['selectSite'])){
		$siteId = $_REQUEST['selectSite'];
	} else {
		$siteId = Settings::get('library_name');
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
<h1><span id="searchHdr" class="title"><?php echo T("Staff Login"); ?></span></h1>
<?php //print_r($_SESSION); //debugging only ?>

<form name="loginform" method="post" action="../shared/login.php">
<fieldset>
<table class="primary">

	<tbody>
	<tr>
		<td valign="top" class="noborder">
			<label for="username"><?php echo T("Username:"); ?></label>
		</td>
		<td valign="top" class="noborder">
			<?php echo inputfield('text','username',$postVars["username"],$attrs); ?>
		</td>
	</tr>
	<tr>
		<td valign="top" class="noborder">
			<label for="password"><?php echo T("Password:"); ?></label>
		</td>
		<td valign="top" class="noborder">
			<?php echo inputfield('password','pwd',$postVars["pwd"],$attrs); ?>
		</td>
	</tr>
	<?php if(($_SESSION['show_copy_site'] == 'Y') || ($_SESSION['site_login'] == 'Y')){ ?>
	<tr>
		<td>
			<label for="selectSite"><?php echo T("Library Site"); ?>:</label>
		</td>
		<td>
			<?php 
				echo inputfield('select', 'selectSite', $siteId, NULL, $sites); 	
			?>	
		</td>
	</tr>
	<?php } ?>
	</tbody>
	
	<tfoot>
	<tr>
		<td colspan="2" align="center" class="noborder">
			<input type="submit" value="<?php echo T("Login"); ?>" class="button" />
		</td>
	</tr>
	<tfoot>
	
</table>
</fieldset>
</form>

<?php

	Page::footer();
