<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "new";
	$focus_form_name = "custommarcform";
	$focus_form_field = "tag";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	//require_once(REL(__FILE__, "../catalog/marcFuncs.php"));

	$postVars = array();
	$pageErrors = array();
	if (isset($_GET["material_cd"])) {
		$material_cd = $_GET["material_cd"];
		$postVars["material_cd"] = $material_cd;
		$postVars["tag"] = "";
		$postVars["subfieldCd"] = "";
		$postVars["descr"] = "";
		$postVars["required"] = "";
	} else if (isset($_SESSION['postVars'])) {
		$postVars = $_SESSION['postVars'];
		if (isset($_SESSION['pageErrors'])) {
			$pageErrors = $_SESSION['pageErrors'];
		}
		$material_cd = $postVars['material_cd'];
	}
	if (!isset($material_cd) || $material_cd == "") {
		Fatal::internalError(T("No material code set"));
	}
	if (isset($_GET["tag"])) {
		$postVars["tag"] = $_GET["tag"];
	}
	if (isset($_GET["subfld"])) {
		$postVars["subfield_cd"] = $_GET["subfld"];
	}
	if (isset($_GET["label"])) {
		$postVars["label"] = $_GET["label"];
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	$returnPg = "../admin/material_fields_add_form.php?material_cd=".U($material_cd);
	$fieldid = "";
	$cancelLocation ="../admin/material_fields_view.php?material_cd=$material_cd";
	if (isset($_GET["msg"])) {
		$msg = '<p class="error">'.H($_GET["msg"]).'</p><br /><br />';
	} else {
		$msg = "";
	}

	#****************************************************************************
	#*  Start of body
	#****************************************************************************
echo $msg;
?>

<form name="custommarcform" action="material_fields_add.php" method="post">
<input type="hidden" name="material_cd" value="<?php echo H($material_cd); ?>" />
<input type="hidden" name="posted" value="posted" />
<?php include ("../admin/material_fields_form_fields.php"); ?>
</form>

<?php

	Page::footer();
