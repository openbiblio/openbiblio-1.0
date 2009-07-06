<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once(REL(__FILE__, "../model/States.php"));
	require_once(REL(__FILE__, "../model/MemberTypes.php"));
	require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));
	$sites_table = new Sites;
	$sites = $sites_table->getSelect();

	$_SESSION[postVars] = $postVars;
	$_SESSION[pageErrors] = $pageErrors;

	if (empty($sites)) {
			echo '<strong>'.T('mbrFldsMustAddSite', array('link'=>'<a href="../admin/sites_list.php">', 'end'=>'</a>')).'</strong>';
			return;
	}
	$states = new States;
	$mbrtypes = new MemberTypes;
	$customFields = new MemberCustomFields;
	$fields = array(
		'site' => inputfield('select', 'siteid', NULL, NULL, $sites),
		'cardNumber' => inputfield("text","barcode_nmbr",NULL,$attr=array("size"=>20,"max"=>20),$pageErrors),
		'lastName' => inputfield("text","last_name",NULL,$attr=array("class"=>"required","size"=>20,"max"=>20),$pageErrors),
		'firstName' => inputfield("text","first_name",NULL,$attr=array("class"=>"required","size"=>20,"max"=>20),$pageErrors),
		'address1' => inputfield("text","address1",NULL,$attr=array("size"=>40,"max"=>128),$pageErrors),
		'address2' => inputfield("text","address2",NULL,$attr=array("size"=>40,"max"=>128),$pageErrors),
		'city' => inputfield("text","city",NULL,$attr=array("size"=>30,"max"=>50),$pageErrors),
		'state' => inputfield("select","state",NULL, NULL, $states->getSelect()),
		'zip' => inputfield("text","zip",NULL,$attr=array("size"=>5,"max"=>5),$pageErrors),
		"mbrFldsZipExt" => inputfield("text","zip_ext",NULL,$attr=array("size"=>4,"max"=>4),$pageErrors),
		"homePhone" => inputfield("text","home_phone",NULL,$attr=array("class"=>"required","size"=>15,"max"=>15),$pageErrors),
		"workPhone" => inputfield("text","work_phone",NULL,$attr=array("size"=>15,"max"=>15),$pageErrors),
		"emailAddress" => inputfield("text","email",NULL,$attr=array("size"=>40,"max"=>128),$pageErrors),
		"password" => inputfield("password","password",NULL,$attr=array("class"=>"required","size"=>10),$pageErrors),
		"passwordConfirm" => inputfield("password","confirm-pw",NULL,$attr=array("class"=>"required","size"=>10),$pageErrors),
		"classification" => inputfield("select", "classification", NULL, NULL, $mbrtypes->getSelect()),
	);
	
/*
	FIXME -- table does not exist; legacy code perhaps? -- fred
	foreach ($customFields->getSelect() as $name=>$title) {
		$fields[$title.':'] = inputfield('text', 'custom_'.$name, NULL,NULL,$pageErrors);
	}
*/
?>
<p class="note">
<?php echo T("Fields marked are required"); ?>
</p>

<fieldset>
<table class="primary">
	<thead>
	<!--tr>
		<th colspan="2" valign="top" nowrap="yes" align="left">
			<?php //echo $headerWording;?> <?php echo T("Member"); ?>
		</td>
	</tr-->
	</thead>
	<tbody class="striped">
<?php
	foreach ($fields as $title => $html) {
	  if (($title == 'cardNumber') && ($_SESSION['mbrBarcode_flg']=='N')) continue;
?>
	<tr>
		<td nowrap="true" class="primary" valign="top">
			<?php echo T($title); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo $html; ?>
		</td>
	</tr>
<?php
	}
?>
	</tbody>
	<tfoot>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='<?php echo $cancelLocation;?>'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
</table>
</fieldset>