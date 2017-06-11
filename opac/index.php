<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * This module looks to see if a library preference is saved in a COOKIE.
 * If so, then the user is taken directlly to a search screen for that library.
 * If, not the user is asked to select a library and a new cookie is saved.
 *
 * Re-write of original PHP based version.
 * @author Fred LaPlante, June 2017
 */

require_once("../shared/common.php");

$currentSite = $_SESSION['current_site'];
$opecSiteMode = Settings::get('opec_site_mode');
if ($opecSiteMode == 'N') {
	//Force user to use current site
	header("Location: ../catalog/srchForms.php?tab=OPAC");
} // else Now dispay site chooser form

$tab = "opac";
$nav = "index";
$focus_form_name = "chooserFrm";
$focus_form_field = "libraryName";

Page::header(array('nav'=>$nav, 'title'=>''));

?>
	<h3 id="opacHdr"><?php echo T("Welcome to the Library");?></h3>
	<p class="note"><?php echo T("Library has multiple facilities, chose one");?></p>
	<form role="form" id="chooserForm" name="chooserForm" method="post" >
		<fieldset>
			<label for="libraryName"><?php echo T("Library Site"); ?></label>
			<select id="libraryName" name="siteid" ></select>

			<hr>
			<input type="hidden" id="mode" name="mode" />
			<button id="theBtn">Select site</button>
		</fieldset>
	</form>

<?php
  	require_once(REL(__FILE__,'../shared/footer.php'));

	require_once(REL(__FILE__, "../opac/indexJs.php"));
?>
</body>
</html>
<?
