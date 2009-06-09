<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "cataloging";
	$nav = "biblio/viewmarc";
	$helpPage = "biblioMarcView";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));

	if (isset($_REQUEST["bibid"])){
		$bibid = $_REQUEST["bibid"];
		$postVars['bibid'] = $bibid;
		$_SESSION["postVars"] = $postVars;
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
		$bibid = $postVars["bibid"];
	}
	#****************************************************************************
	#*  Search database
	#****************************************************************************
	$biblios = new Biblios();
	$biblio = $biblios->getOne($bibid);


?>
<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo H(Settings::get('html_lang_attr')); ?>" lang="<?php echo H(Settings::get('html_lang_attr')); ?>">
<head>
<title></title>
</head>
<body>
<table class="primary" width="100%">
	<tr>
		<td colspan="2" nowrap="true" class="primary">
			<b><?php echo T("MARC Record:"); ?> <?php echo $biblio['marc']->getValue('245$a')?></b>
		</td>
	</tr>
	<tr>
	 <td colspan="2">
	<a href="#" onclick="APA();return false;">APA</a>&nbsp;|&nbsp;<a href="#" onclick="MLA();return false;">MLA</a>
	<br />

	<div id="replaceme"><br />&nbsp;<br /></div>


	<script type="text/javascript">
	var title = "<?php echo $biblio['marc']->getValue('245$a'); ?>";
	var author = "<?php echo $biblio['marc']->getValue('100$a'); ?>";
	var pubName = "<?php echo $biblio['marc']->getValue('260$b'); ?>";
	var pubDate = "<?php echo $biblio['marc']->getValue('260$c'); ?>";
	var pubCity = "<?php echo $biblio['marc']->getValue('260$a'); ?>";

	function APA() {
		var msg = author + ". (" + pubDate + "). " + "<i>" + title + "</i>, " + pubCity +": " + pubName;
		document.getElementById("replaceme").innerHTML = msg;

	}

	function MLA() {
		var msg = author + ". " + "<u>" + title + "</u>. " + pubCity +": " + pubName + ", " + pubDate;
		document.getElementById("replaceme").innerHTML = msg;

	}
	</script>
</td></tr>
</table>

</body>
</html>
