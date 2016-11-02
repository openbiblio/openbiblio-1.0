<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__,"../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));

	## assure all session values are current
	## session is started in ../shared/common.php
	setSessionFmSettings();  ## found in ../shared/common.php
	
	$temp_return_page = "";
	if (isset($_GET["RET"])){
		$_SESSION["returnPage"] = $_GET["RET"];
	}

	$sites_table = new Sites;
    //echo "in loginForm: about to collect site data<br />\n";
	$sites = $sites_table->getSelect();
    //echo "in loginForm: ";print_r($sites);echo "<br />\n";

	// If the current_site is set, default to this site, otherwise use the cookie and finally the site default
	if(isset($_REQUEST['selectSite'])){
		$siteId = $_REQUEST['selectSite'];
	} elseif(isset($_COOKIE['OpenBiblioSiteID'])) {
		$site = new Sites;
		$exists_in_db = $site->maybeGetOne($_COOKIE['OpenBiblioSiteID']);
		if ($exists_in_db['siteid'] != $_COOKIE['OpenBiblioSiteID']) {
			$_COOKIE['OpenBiblioSiteID'] = 1;
		}
		$siteId = $_COOKIE['OpenBiblioSiteID'];
	} else {
		$siteId = Settings::get('multi_site_func');
		if(!($siteId > 0)){
			$siteId = 1;
		}
		$_REQUEST['selectSite'] = $siteId;
	}
	$tab = "circ";
	$nav = "";
	$focus_form_name = "loginform";
	$focus_form_field = "username";

	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3 class="title"><?php echo T("Staff Login"); ?></h3>
<?php // print_r($_SESSION); //debugging only ?>

<form name="loginform" method="post" action="../shared/login.php">
<fieldset>
<table>
	<tbody>
	<tr>
		<td><label for="username"><?php echo T("Username"); ?>:</label></td>
		<td valign="top">
			<input id="username" name="username" type="text" size="15" required aria-required="true" autofocus />
		</td>
	</tr>
	<tr>
		<td><label for="password"><?php echo T("Password"); ?>:</label></td>
		<td valign="top" class="noborder">
			<input id="pwd" name="pwd" type="password" size="15" required aria-required="true" />
		</td>
	</tr>
	<?php if(($_SESSION['multi_site_func'] > 0) || ($_SESSION['site_login'] == 'Y')){ ?>
	<tr>
		<td><label for="selectSite"><?php echo T("Library Site"); ?>:</label></td>
		<td>
			<?php echo inputfield('select', 'selectSite', $siteId, NULL, $sites) ?>	
		</td>
	</tr>
	<?php } ?>
	</tbody>
	
	<tfoot>
	<tr>
		<td colspan="2" align="center">
			<input type="submit" id="login" value="<?php echo T("Login"); ?>" />
		</td>
	</tr>
	<tfoot>
	
</table>
</fieldset>
</form>

<script language="JavaScript" >
	"use strict"
/* TODO - fl get this working
console.log('js activated');
	$("#showPwd").on('change',null, function () {
console.log("'hide pwd' clicked");
		if ($("#showPwd:checked").length > 0) {
        	$(".pwd").attr('type', 'text');
console.log("'pwd type' now text");
		} else {
        	$(".pwd").attr('type', 'password');
console.log("'pwd type' now password");
		}
	});
*/
</script>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
?>	
