<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "collections";

	require_once(REL(__FILE__, "../model/Collections.php"));
//	require_once(REL(__FILE__, "../functions/formatFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<script>
cl = {
	init: function () {
		//$('form').bind('submit',null,function(){
		//	alert('you hit a submit button');
		//	return false;
		//});
		//$('<sup>*</sup>').prependTo('#newmbrform table tr:has(input.required) td:first-child');

		$('table.striped tbody tr:even').addClass('altBG');
	}
};
$(document).ready(cl.init);

</script>
<h3><?php echo T("Collections"); ?></h3>

<?php
	if ($_GET["msg"]) {
		echo '<p class="error">'.H($_GET["msg"])."</p><!--br /><br /-->";
	}
	$collections = new Collections;
	$cols = $collections->getAllWithStats();

	function _type_format($type, $data) {
		# FIXME - i18n
		$str = $type.': ';
		switch ($type) {
		case 'Circulated':
			$str .= sprintf("%d ".T("days").", $%.2f/".T("day"), $data['days_due_back'], $data['daily_late_fee']);
			break;
		case 'Distributed':
			$str .= T("Restock at ").$data['restock_threshold'];
			break;
		default:
			$str .= '???';
		}
		return $str;
	}
?>

<a href="../admin/collections_new_form.php?reset=Y"><?php echo T("Add New Collection"); ?></a>
<br />
<fieldset>
<table class="primary striped">
<thead>
	<tr>
		<th colspan="2" valign="top">
			<sup>*</sup><?php echo T("Function"); ?>
		</th>
		<th valign="top">
			<?php echo T("Description"); ?>
		</th>
		<th valign="top">
			<?php echo T("Type"); ?>
		</th>
		<th valign="top">
			<?php echo T("Item<br />Count"); ?>
		</th>
	</tr>
<thead>
<tbody>
	<?php
		while ($col = $cols->next()) {
	?>
	<tr>
		<td valign="top" class="primary">
			<a href="../admin/collections_edit_form.php?code=<?php echo HURL($col['code']);?>" class="<?php echo H($row_class);?>"><?php echo T("edit"); ?></a>
		</td>
		<td valign="top" class="primary">
			<?php if ($col['count'] == 0) { ?>
				<a href="../admin/collections_del_confirm.php?code=<?php echo HURL($col['code']); ?>&amp;desc=<?php echo HURL($col['description']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("del"); ?></a>
			<?php } else { echo T("del"); } ?>
		</td>
		<td valign="top" class="primary">
			<?php echo H($col['description']); ?>
		</td>
		<td valign="top" align="center" class="primary">
			<?php echo H(_type_format($col['type'], $collections->getTypeData($col))); ?>
		</td>
		<td valign="top" align="center"  class="primary">
			<?php echo H($col['count']); ?>
		</td>
	</tr>
	<?php
		}
	?>
</tbody>
</table>
</fieldset>

<p class="note">
	<sup>*</sup>
	<?php echo T("Note:");?><br /><?php echo T('collectionsListNoteMsg'); ?>
</p>

<?php

	Page::footer();
