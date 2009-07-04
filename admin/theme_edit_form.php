<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "themes";
	$headerWording="Edit";
	$focus_form_name = "editthemeform";
	$focus_form_field = "themeName";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	#****************************************************************************
	#*  Checking for query string flag to read data from database.
	#****************************************************************************
	if (isset($_GET["themeid"])){
		include_once(REL(__FILE__, "../model/Themes.php"));
		$themes = new Themes;
		$theme = $themes->getOne($_GET["themeid"]);

		$postVars["themeid"] = $theme['themeid'];
		$postVars["themeName"] = $theme['theme_name'];

		$postVars["titleBg"] = $theme['title_bg'];
		$postVars["titleFontFace"] = $theme['title_font_face'];
		$postVars["titleFontSize"] = $theme['title_font_size'];
		if ($theme['title_font_bold'] == 'Y') {
			$postVars["titleFontBold"] = "CHECKED";
		} else {
			$postVars["titleFontBold"] = "";
		}
		$postVars["titleFontColor"] = $theme['title_font_color'];
		$postVars["titleAlign"] = $theme['title_align'];

		$postVars["primaryBg"] = $theme['primary_bg'];
		$postVars["primaryFontFace"] = $theme['primary_font_face'];
		$postVars["primaryFontSize"] = $theme['primary_font_size'];
		$postVars["primaryFontColor"] = $theme['primary_font_color'];
		$postVars["primaryLinkColor"] = $theme['primary_link_color'];
		$postVars["primaryErrorColor"] = $theme['primary_error_color'];

		$postVars["alt1Bg"] = $theme['alt1_bg'];
		$postVars["alt1FontFace"] = $theme['alt1_font_face'];
		$postVars["alt1FontSize"] = $theme['alt1_font_size'];
		$postVars["alt1FontColor"] = $theme['alt1_font_color'];
		$postVars["alt1LinkColor"] = $theme['alt1_link_color'];

		$postVars["alt2Bg"] = $theme['alt2_bg'];
		$postVars["alt2FontFace"] = $theme['alt2_font_face'];
		$postVars["alt2FontSize"] = $theme['alt2_font_size'];
		$postVars["alt2FontColor"] = $theme['alt2_font_color'];
		$postVars["alt2LinkColor"] = $theme['alt2_link_color'];
		if ($theme['alt2_font_bold'] == 'Y') {
			$postVars["alt2FontBold"] = "CHECKED";
		} else {
			$postVars["alt2FontBold"] = "";
		}

		$postVars["borderColor"] = $theme['border_color'];
		$postVars["borderWidth"] = $theme['border_width'];
		$postVars["tablePadding"] = $theme['table_padding'];
		$_SESSION['postVars'] = $postVars;
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}


?>

<script type="text/javascript">
<!--
function previewTheme() {
	var SecondaryWin;
	SecondaryWin = window.open('',"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
	document.editthemeform.action = "../admin/theme_preview.php";
	document.editthemeform.target = 'secondary';
	document.editthemeform.submit();
}

function editTheme() {
	document.editthemeform.action = "../admin/theme_edit.php";
	document.editthemeform.target = '';
	document.editthemeform.submit();
}

-->
</script>

<h3><?php echo T("Themes"); ?></h3>

<a href="javascript:previewTheme()"><?php echo T("Preview Theme Changes"); ?></a><br /><br />

<form name="editthemeform" method="post" action="../admin/theme_edit.php">
<fieldset>
<legend><?php echo $headerWording;?> <?php echo T("Theme"); ?></legend>
<input type="hidden" name="themeid" value="<?php echo $postVars["themeid"];?>" />

<?php

	include(REL(__FILE__, "../admin/theme_fields.php"));
	
?>
</fieldset>
<?php
	Page::footer();
