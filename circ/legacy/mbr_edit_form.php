<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "circulation";
	$restrictToMbrAuth = TRUE;
	$nav = "mbr/edit";
	$focus_form_name = "editMbrform";
	$focus_form_field = "barcodeNmbr";
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../model/Members.php"));

	if (isset($_GET["mbrid"])){
		$members = new Members;
		$mbr = $members->getOne($_GET["mbrid"]);
		$_SESSION["postVars"] = $mbr;
		$custom = $members->getCustomFields($_GET["mbrid"]);
		while ($row = $custom->next() ) {
			$_SESSION["postVars"]['custom_'.$row["code"]] = $row["data"];
		}
		$postVars = $_SESSION["postVars"];
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}
	$mbrid = $postVars[mbrid]; # For nav menu
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	$headerWording = T("Edit");

	$cancelLocation = "../circ/mbr_view.php?mbrid=".$postVars["mbrid"]."&reset=Y";
?>
<h3>			<?php echo $headerWording;?> <?php echo T("Member"); ?></h3>

<form name="editMbrform" method="post" action="../circ/mbr_edit.php">
<input type="hidden" name="mbrid" value="<?php echo $postVars["mbrid"];?>" />

<?php

	include(REL(__FILE__, "../circ/mbr_fields.php"));
	 ;
