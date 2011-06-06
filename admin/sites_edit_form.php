<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$focus_form_name = "editsiteform";
	$focus_form_field = "name";

	require_once(REL(__FILE__, "../model/Calendars.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	if (isset($_REQUEST["siteid"])) {
		$siteid = $_REQUEST["siteid"];
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
		$siteid = $postVars['siteid'];
	}
	$site = array('siteid'=>$siteid, 'calendar'=>'');
	if (is_numeric($siteid)){
		include_once(REL(__FILE__, "../model/Sites.php"));
		$sites = new Sites;
		$site = $sites->getOne($_REQUEST["siteid"]);
		$nav = "sites/edit";
	} else {
		$nav = "sites/new";
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Edit Site"); ?></h3>

<form name="editsiteform" method="post" action="../admin/sites_edit.php">
<input type="hidden" name="siteid" value="<?php echo H($site["siteid"]) ?>" />
<fieldset>
<table class="primary">
	<tbody class="striped">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Calendar:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php
				$calendars = new Calendars;
				echo inputfield('select', 'calendar', $site['calendar'], NULL, $calendars->getSelect());
			?>
		</td>
	</tr>
<?php
	$fields = array(
		'name' => T("Name"),
		'code' => T("Code"),
		'address1' => T("Address Line 1"),
		'address2' => T("Address Line 2"),
		'city' => T("City"),
		'state' => T("State"),
		'zip' =>T("Zip Code"),
		'phone' => T("Phone"),
		'fax' => T("Fax"),
		'email' => T("Email"),
	);
	foreach ($fields as $n => $title) {
?>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo H($title) ?>:
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text', $n, $site[$n]); ?>
		</td>
	</tr>
<?php
	}
?>
	<tr>
		<td nowrap="true" class="primary" valign="top">
			<?php echo T('sitesEditFormDelNote'); ?>
		</td>
		<td valign="top" class="primary">
			<?php
				echo inputfield('textarea', 'delivery_note', $site['delivery_note']);
			?>
		</td>
	</tr>
	</tbody>
	
	<tfoot>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<a class="small_button" href="../admin/sites_list.php"><?php echo T("Cancel"); ?></a>
		</td>
	</tr>
	</tfoot>
	
</table>
</fieldset>
</form>

<?php

	 ;
