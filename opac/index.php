<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * This module looks to see if a library preference is saved in a COOKIE. 
 * If so, then the user is taken directlly to a search screen for that library.
 * If, not the user is asked to select a library and a new cookie is saved. 
 * 
 *FIXME:
 * There needs to be a way for a user to switch libraries.
 *         
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../functions/inputFuncs.php"));	
require_once(REL(__FILE__, "../model/Sites.php"));

$sites_table = new Sites;		
$sites = $sites_table->getSelect();

// Adjusted, so that if 'library_name' contains a string, the site is put by default on 1.
if(empty($_SESSION['current_site'])){ 
 	// Check for cookie, otherwise take default
	if(isset($_COOKIE['OpenBiblioSiteID'])) {
		$siteId = $_COOKIE['OpenBiblioSiteID'];
	} else {
		if($_SESSION['multi_site_func'] > 0){
			$_SESSION['current_site'] = $_SESSION['multi_site_func'];
		} else {
			$_SESSION['current_site'] = 1;
		}
		setcookie("OpenBiblioSiteID", $_SESSION['current_site'], time()+60*60*24*365);
	}			
}
	
if(isset($_REQUEST['selectSite'])){
	$_SESSION['current_site'] =  $_REQUEST['selectSite'];
	header("Location: ../opac/index.php");
}

if(!empty($_SESSION['current_site'])) {
	header("Location: ../catalog/srchForms.php?tab=OPAC");
}	

session_cache_limiter(null);

$tab = "opac";
$nav = "home";
$focus_form_name = "catalog_search";
$focus_form_field = "searchText";

//Page::header_opac(array('nav'=>$nav, 'title'=>''));
Page::header(array('nav'=>$nav, 'title'=>''));

?>
			<h1><?php echo T("Welcome to the Library");?></h1>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="phrasesearch">
					<fieldset>
					<legend><?php T("Please select the library") ?></legend>
					<table class="primary">
						<tbody>
							<tr>
							<td class="primary" nowrap="true">
								<?php echo T("Please select the library"); ?>
							</td><td>
								<?php echo inputfield('select', 'selectSite', Settings::get('library_name'), NULL, $sites); 	?>								
								<input class="button" name="action" type="submit" value="<?php echo T("Select site")?>"/>
							</td></tr>							
						</tbody>
					</table>
				</fieldset>
			</form>			
<?
 ;
?>
