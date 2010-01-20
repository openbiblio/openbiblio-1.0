<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../functions/inputFuncs.php"));	
require_once(REL(__FILE__, "../model/Sites.php"));

$sites_table = new Sites;		
$sites = $sites_table->getSelect();

if(sizeof($sites) == 1) $_SESSION['current_site'] = Settings::get('library_name');
	
if(isset($_REQUEST['selectSite'])){
	$_SESSION['current_site'] =  $_REQUEST['selectSite'];
	header("Location: opac/index.php");
}

if(!empty($_SESSION['current_site'])) header("Location: ../catalog/biblio_search.php?tab=OPAC");
	

session_cache_limiter(null);

$tab = "opac";
$nav = "home";
$focus_form_name = "catalog_search";
$focus_form_field = "searchText";

Page::header_opac(array('nav'=>$nav, 'title'=>''));

?>

			<h1><? echo T('Welcome to the libary');?></h1>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="phrasesearch">
					<fieldset>
					<legend><?php T('Please select the library') ?></legend>
					<table class="primary">
						<tbody>
							<tr>
							<td class="primary" nowrap="true">
								<?php echo T('Please select the library:'); ?>
							</td><td>
								<?php echo inputfield('select', 'selectSite', Settings::get('library_name'), NULL, $sites); 	?>								
								<input class="button" name="action" type="submit" value="<?echo T('Select site')?>"/>
							</td></tr>							
						</tbody>
					</table>
				</fieldset>
			</form>			
<?
Page::footer();
?>
