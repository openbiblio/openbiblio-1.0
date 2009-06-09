<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	session_cache_limiter(null);

	$tab = "admin";
	$nav = "new";
	$helpPage = "customMARC";

	if (isset($_GET["msg"])) {
		$msg = '<p class="error">'.H($_GET["msg"]).'</p><br /><br />';
	} else {
		$msg = "";
	}
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	require_once(REL(__FILE__, "../functions/errorFuncs.php"));

	if (!isset($_GET["material_cd"]) || $_GET["material_cd"] == "") {
		Fatal::internalError('material_cd not set');
	}
	$material_cd= $_GET["material_cd"];

	//    Played with printselect function
	$postvars["material_cd"]=$material_cd;
	$value=$_GET["material_cd"];
	$fieldname="material_cd";
	$domainTable="material_type_dm";

	$mt = new MaterialTypes();
	$material_type= $mt->get_name($material_cd);

	echo $msg;
?>
<br />
<a href="material_fields_add_form.php?material_cd=<?php echo HURL($material_cd);?>&amp;reset=Y"><?php echo T("materialFieldsViewAddField");?></a> (<?php echo $material_type; ?>)<br /><br />
<?php
	$mf = new MaterialFields;
	$rows = $mf->getMatches(array('material_cd'=>$material_cd));

	if ($rows->count() == 0) {
		echo T("No fields found!");
	} else {
?>

<table class="primary">
<tr>
<th colspan="2" valign="top"><sup>*</sup><?php echo T("Function"); ?></th>
<th><?php echo T("Tag"); ?></th>
<th><?php echo T("Subfield"); ?></th>
<th><?php echo T("Label"); ?></th>
<th><?php echo T("Position"); ?></th>
<th><?php echo T("Required?"); ?></th>
<th><?php echo T("Repeatable?"); ?></th>
</tr>
<?php
		while ($row = $rows->next()) {
?>
<tr>
<td valign="top" class="primary">
<a href="material_fields_edit_form.php?material_field_id=<?php echo HURL($row["material_field_id"])?>&amp;material_cd=<?php echo HURL($material_cd) ?>&amp;reset=Y">
<?php echo T("edit"); ?></a>
</td>
<td valign="top" class="primary">
<a href="material_fields_delete.php?material_field_id=<?php echo HURL($row["material_field_id"])?>&amp;material_cd=<?php echo HURL($material_cd) ?>">
<?php echo T("del"); ?></a>
</td>

<td class="primary"><?php echo H($row["tag"])?></td>
<td class="primary" align="center"><?php echo H($row["subfield_cd"])?></td>
<td class="primary"><?php echo H($row['label']); ?></td>
<td class="primary" align="center"><?php echo H($row["position"])?></td>
<td class="primary" align="center"><?php
if ($row["required"]=='1') {
	echo "T";
} else {
	echo "F";
}
?></td>
<td class="primary" align="center"><?php
if ($row["repeatable"]=='1') {
	echo "T";
} else {
	echo "F";
}
?></td>
</tr>
<?php
		}
		echo "</table>";
	}
	Page::footer();
