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

	$fields = array(
		'Site' => inputfield('select', 'siteid', $mbr[siteid], NULL, $sites),
		'Card Number' => inputfield("text","barcode_nmbr",$mbr[barcode_nmbr],$attr=array("class"=>"required","size"=>20,"max"=>20),$pageErrors),
		'Last Name' => inputfield("text","last_name",$mbr[last_name],$attr=array("class"=>"required","size"=>20,"max"=>20),$pageErrors),
		'First Name' => inputfield("text","first_name",$mbr[first_name],$attr=array("class"=>"required","size"=>20,"max"=>20),$pageErrors),
		'Address Line 1' => inputfield("text","address1",$mbr[address1],$attr=array("size"=>40,"max"=>128),$pageErrors),
		'Address Line 2' => inputfield("text","address2",$mbr[address2],$attr=array("size"=>40,"max"=>128),$pageErrors),
		'City' => inputfield("text","city",$mbr[city],$attr=array("size"=>30,"max"=>50),$pageErrors),
		'State' => inputfield("select","state",$mbr[state], NULL, $states->getSelect()),
		'Zip Code' => inputfield("text","zip",$mbr[zip],$attr=array("size"=>5,"max"=>5),$pageErrors),
		"Zip Code ext" => inputfield("text","zip_ext",$mbr[zip_ext],$attr=array("size"=>4,"max"=>4),$pageErrors),
		"Home Phone" => inputfield("text","home_phone",$mbr[home_phone],$attr=array("class"=>"required","size"=>15,"max"=>15),$pageErrors),
		"Work Phone" => inputfield("text","work_phone",$mbr[work_phone],$attr=array("size"=>15,"max"=>15),$pageErrors),
		"Email Address" => inputfield("text","email",$mbr[email],$attr=array("size"=>40,"max"=>128),$pageErrors),
		//"password" => inputfield("password","password",$mbr[password],$attr=array("class"=>"required","size"=>10),$pageErrors),
		//"passwordConfirm" => inputfield("password","confirm-pw",$mbr[passwordConfirm],$attr=array("class"=>"required","size"=>10),$pageErrors),
		"Classification" => inputfield("select", "classification",$mbr[classification], NULL, $mbrtypes->getSelect()),
	);

	## add custom fields to array to be displayed
	$customFields = new MemberCustomFields;
	foreach ($customFields->getSelect() as $name=>$title) {
		$fields[$title] = inputfield('text', 'custom_'.$name, NULL,NULL,$pageErrors);
	}
?>

<p class="note"> <?php echo T("Fields marked are required"); ?></p>

<fieldset>
<table>
	<tbody>
<?php
	foreach ($fields as $title => $html) {
	  if (($title == 'Card Number') && (($_SESSION[mbrBarcode_flg]=='N') || ($_SESSION[mbr_autoBarcode_flg]=='Y'))){
			echo "<tr>\n";
			echo "	<td colspan=\"2\">";inputfield('hidden',"barcode_nmbr",'000000');echo "	</td>\n";
			echo "</tr>\n";
		}
		else {
			echo "<tr>\n";
			echo "	<td><label>";echo T($title); echo "</label></td>\n";
			echo "	<td valign=\"top\" >";echo $html; echo "</td>\n";
			echo "</tr>\n";
		}
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
