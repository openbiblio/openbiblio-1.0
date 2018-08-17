<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	## set up default values for new member
	require_once(REL(__FILE__, "../model/Sites.php"));
	$sites_table = new Sites;
	$lib = $sites_table->getOne($_SESSION['current_site']);
	if (empty($lib	)) {
		$text = 'href="../admin/sites_list.php"';
		echo '<strong>'.T("mbrFldsMustAddSite", array('link'=>'<a '.$text.' >', 'end'=>'</a>')).'</strong>';
		return;
	}

	$mbr['siteid'] = $lib['siteid'];
	$mbr['city'] = $lib['city'];
	$mbr['state'] = $lib['state'];
	$mbr['zip'] = $lib['zip'];

	require_once(REL(__FILE__, "../model/MemberTypes.php"));
	$mbrtypes = new MemberTypes;
	$mbr['classification'] = $mbrtypes->getDefault();

	$_SESSION['postVars'] = $postVars;
	$_SESSION['pageErrors'] = $pageErrors;
?>

	<tr>
		<td><label for="siteid"><?php echo T("Site");?></label></td>
		<td valign="top">
			<?php echo inputfield('hidden', 'crntSite', $mbr['siteid']);?>
			<?php echo inputfield('select', 'siteid', $mbr['siteid']);?>
		</td>
	</tr>
	<tr>
		<td><label for="barcode_nmbr"><?php echo T("Card Number");?></label></td>
		<td valign="top">
			<?php echo inputfield("text","barcode_nmbr",$mbr['barcode_nmbr'],$attr=array("required"=>"required","size"=>20,"max"=>20),$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td><label for="last_name"><?php echo T("LastName");?></label></td>
		<td valign="top">
      <?php echo inputfield("text","last_name",$mbr['last_name'],$attr=array("required"=>"required","size"=>20,"max"=>20),$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td><label for="first_name"><?php echo T("FirstName");?></label></td>
		<td valign="top">
			<?php echo inputfield("text","first_name",$mbr['first_name'],$attr=array("required"=>"required","size"=>20,"max"=>20),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="address1"><?php echo T("AddressLine1");?></label></td>
		<td valign="top">
			<?php echo inputfield("text","address1",$mbr['address1'],$attr=array("size"=>40,"max"=>128),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="address2"><?php echo T("AddressLine2");?></label></td>
		<td valign="top">
			<?php echo inputfield("text","address2",$mbr['address1'],$attr=array("size"=>40,"max"=>128),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="city"><?php echo T("City");?></label></td>
		<td valign="top">
			<?php echo inputfield("text","city",$mbr['city'],$attr=array("size"=>30,"max"=>50),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="state"><?php echo T("State");?></label></td>
		<td valign="top">
			<?php echo inputfield("select","state",$mbr['state']);?>
		</td>
	</tr>
	<tr>
		<td><label for="zip"><?php echo T("PostalCode");?></label></td>
		<td valign="top">
			<?php echo inputfield("zip","zip",$mbr['zip'],$attr=array("size"=>10,"max"=>10),$pageErrors);?>
		</td>
	</tr>
	<!--tr>
		<td><label for="zip_ext"><?php echo T("ZipCodeExt");?></label></td>
		<td valign="top">
			<?php echo inputfield("text","zip_ext",$mbr['zip_ext'],$attr=array("size"=>10,"max"=>10),$pageErrors);?>
		</td>
	</tr-->
	<tr>
		<td><label for="home_phone"><?php echo T("HomePhone");?></label></td>
		<td valign="top">
			<?php echo inputfield("tel","home_phone",$mbr['home_phone'],$attr=array("required"=>"required","size"=>15,"max"=>15),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="work_phone"><?php echo T("WorkPhone");?></label></td>
		<td valign="top">
			<?php echo inputfield("tel","work_phone",$mbr['work_phone'],$attr=array("size"=>15,"max"=>15),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="email"><?php echo T("EmailAddress");?></label></td>
		<td valign="top">
			<?php echo inputfield("email","email",$mbr['email'],$attr=array("size"=>40,"max"=>128),$pageErrors);?>
		</td>
	</tr>
	<tr>
		<td><label for="classification"><?php echo T("MemberType");?></label></td>
		<td valign="top">
			<?php echo inputfield("select", "classification",$mbr['classification']);?>
		</td>
	</tr>
	<tr>
		<td><label for="last_legal_name"><?php echo T("Legal last name").' ('.T("if different from above").')';?></label></td>
		<td valign="top">
      <?php echo inputfield("text","last_legal_name",$mbr['last_legal_name'],$attr=array("size"=>20,"max"=>20),$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td><label for="first_legal_name"><?php echo T("Legal first name").' ('.T("if different from above").')';?></label></td>
		<td valign="top">
			<?php echo inputfield("text","first_legal_name",$mbr['first_legal_name'],$attr=array("size"=>20,"max"=>20),$pageErrors);?>
		</td>
	</tr>


<?php
	## add custom fields
	require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
	require_once(REL(__FILE__, "../model/MemberCustomFields_DM.php"));
	$customFields = new MemberCustomFields_DM;
	foreach ($customFields->getSelect() as $name=>$title) {
		echo "<tr>\n";
		echo "	<td><label>";echo T($title); echo "</label></td>\n";
		echo "	<td valign=\"top\" >";
		echo inputfield('text', 'custom_'.$name, '', NULL,$pageErrors);
    echo "	</td>\n";
		echo "</tr>\n";
	}
?>
