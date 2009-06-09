<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "new";
	$focus_form_name = "custommarceditform";
	$focus_form_field = "tag";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));

	$postVars = array();
	$pageErrors = array();
	if (isset($_GET["material_field_id"])) {
		$material_field_id =$_GET["material_field_id"];
		$mf = new MaterialFields;
		$postVars = $mf->getOne($material_field_id);
	} else if (isset($_SESSION['postVars'])) {
		$postVars = $_SESSION['postVars'];
		if (isset($_SESSION['pageErrors'])) {
			$pageErrors = $_SESSION['pageErrors'];
		}
		$material_field_id = $postVars['material_field_id'];
	}
	$material_cd = $postVars['material_cd'];
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

	$returnPg = "../admin/material_fields_edit_form.php?material_cd=".U($material_cd)."&material_field_id=".U($material_field_id);
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

<form name="custommarceditform" action="material_fields_edit.php" method="post">
<input type="hidden" name="material_cd" value="<?php echo H($material_cd); ?>" />
<input type="hidden" name="material_field_id" value="<?php echo H($material_field_id); ?>" />
<input type="hidden" name="posted" value="posted" />
<?php include ("../admin/material_fields_form_fields.php"); ?>
</form>

<?php

	Page::footer();
