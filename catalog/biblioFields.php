<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");

	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

		function getlabel($f) {
			global $LOC;
			$label = "";
			if ($f['label'] != "") {
				$label = $f['label'];
			} elseif ($f['subfield'] != "") {
				$idx = sprintf("%03d$%s", $f['tag'], $f['subfield']);
				$label = $LOC->getMarc($idx);
			} else {
				$label = $LOC->getMarc($f['tag']);
			}
			return $label;
		}
		function mkinput($fid, $sfid, $data, $f) {
			return array('fieldid' => $fid,
				'subfieldid' => $sfid,
				'data' => $data,
				'tag' => $f['tag'],
				'subfield' => $f['subfield_cd'],
				'label' => getlabel($f),
				'required' => $f['required'],
				'form_type' => $f['form_type'],
				'repeat' => $f['repeatable']);
		}
		function mkFldSet($n, $i, $marcInputFld, $mode) {
		  if ($mode == 'onlnCol') {
				echo "	<td valign=\"top\" class=\"filterable\"> \n";
				$namePrefix = "onln_$n";
		    echo "<input type=\"button\" value=\"<--\" id=\"$namePrefix"."_btn\" class=\"accptBtn\" /> \n";
			}
			else if ($mode == 'editCol') {
				echo "	<td valign=\"top\" > \n";
				$namePrefix = 'fields['.H($n).']';
				echo inputfield('hidden', $namePrefix."[tag]",         H($i['tag']))." \n";
				echo inputfield('hidden', $namePrefix."[subfield_cd]", H($i['subfield']))." \n";
				echo inputfield('hidden', $namePrefix."[fieldid]",     H($i['fieldid']),
												array('id'=>$marcInputFld.'_fieldid'))." \n";
				echo inputfield('hidden', $namePrefix."[subfieldid]",  H($i['subfieldid']),
												array('id'=>$marcInputFld.'_subfieldid'))." \n";
			}

			$attrs = array("id"=>"$marcInputFld");
			$attrStr = "marcBiblioFld";
			if ( $i['required'] && ($mode != 'onlncol') ) {
				// 'required' does not apply to online data fields
				$attrs['required'] = 'required';
			}
			if ($i['repeat'])
			  $attrStr .= " rptd";
			else
			  $attrStr .= " only1";
		  if ($mode == 'onlnCol')
		    $attrStr .= " online";
			else
			  $attrStr .= " offline";
			$attrs["class"] = $attrStr;

			if ($i['form_type'] == 'text') {
			  $attrs["size"] = "50"; $attrs["maxLength"] = "75"; 
				echo inputfield('text', $namePrefix."[data]", H($i['data']),$attrs)." \n";
			} else {
				// IE seems to make the font-size of a textarea overly small under
				// certain circumstances.  We force it to a sane value, even
				// though I have some misgivings about it.  This will make
				// the font smaller for some people.
				$attrs["style"] = "font-size:10pt; font-weight: normal;";
				$attrs["rows"] = "7"; $attrs["cols"] = "38";
				echo inputfield('textarea', $namePrefix."[data]", H($i['data']),$attrs, H($i['data']))." \n";
			}
			echo "</td> \n";
		}
		
		### ============== main code body starts here ==============
		# fetch a complete set of all material types
		$matTypes = new MediaTypes;
		# determine which is to be 'selected'
		if (!empty($_GET['matlCd'])) {
		  $material_cd_value = $_GET['matlCd'];
		} elseif (!empty($_GET['material_cd'])) {
		  $material_cd_value = $_GET['material_cd'];
		} elseif (isset($biblio['material_cd'])) {
			$material_cd_value = $biblio['material_cd'];
		} else {
			$material_cd_value = $matTypes->getDefault();
		}
 				
		// get field specs for this material type in 'display postition' order
		$mf = new MaterialFields;
		$fields = $mf->getMatches(array('material_cd'=>$material_cd_value), 'position');

		## anything to process for current media type (material_cd) ?
		if ($fields->count() == 0) {
			echo "<tr><td colspan=\"2\" >.T('No fields to fill in.').</td></tr>\n";
		}

		## build an array of fields to be displayed on user form
		$inputs = array();
		while (($f=$fields->next())) {
		  #  make multiples of those so flagged
			for ($n=0; $n<=$f['repeatable']; $n++) {
				array_push($inputs, mkinput(NULL, NULL, NULL, $f));
			}
		}

		## now build html for those input fields
		foreach ($inputs as $n => $i) {
			$marcInputFld = H($i['tag']).H($i['subfield']);
			echo "<tr> \n";
			echo "	<td valign=\"top\"> \n";
			//if ($i['required']) {
			//	echo '	<sup>*</sup>';
			//}
			echo "		<label for=\"$marcInputFld\">".H($i['label'].":")."</label>";
			echo "	</td> \n";

			mkFldSet($n, $i, $marcInputFld, 'editCol');	// normal local edit column
			mkFldSet($n, $i, $marcInputFld, 'onlnCol');  // update on-line column

		echo "</tr> \n";
		}
?>
