<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	require_once(REL(__FILE__, "../classes/UsmarcTagDm.php"));
	require_once(REL(__FILE__, "../classes/UsmarcTagDmQuery.php"));
	require_once(REL(__FILE__, "../classes/UsmarcSubfieldDm.php"));
	require_once(REL(__FILE__, "../classes/UsmarcSubfieldDmQuery.php"));
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../functions/errorFuncs.php"));
	require_once(REL(__FILE__, "../catalog/inputFuncs.php"));

	#****************************************************************************
	#*  Loading up an array ($marcArray) with the USMarc tag descriptions.
	#****************************************************************************

	$marcTagDmQ = new UsmarcTagDmQuery();
	$marcTagDmQ->connect();
	if ($marcTagDmQ->errorOccurred()) {
		$marcTagDmQ->close();
		displayErrorPage($marcTagDmQ);
	}
	$marcTagDmQ->execSelect();
	if ($marcTagDmQ->errorOccurred()) {
		$marcTagDmQ->close();
		displayErrorPage($marcTagDmQ);
	}
	$marcTags = $marcTagDmQ->fetchRows();
	$marcTagDmQ->close();

	$marcSubfldDmQ = new UsmarcSubfieldDmQuery();
	$marcSubfldDmQ->connect();
	if ($marcSubfldDmQ->errorOccurred()) {
		$marcSubfldDmQ->close();
		displayErrorPage($marcSubfldDmQ);
	}
	$marcSubfldDmQ->execSelect();
	if ($marcSubfldDmQ->errorOccurred()) {
		$marcSubfldDmQ->close();
		displayErrorPage($marcSubfldDmQ);
	}
	$marcSubflds = $marcSubfldDmQ->fetchRows();
	$marcSubfldDmQ->close();

?>
<p class="note">
<?php echo T("Fields marked are required"); ?>
</p>

<table class="primary" width="100%">
	<tr>
		<th colspan="2" valign="top" nowrap="yes" align="left">
			<?php echo T("Item"); ?>
		</th>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Type of Material:"); ?>
		</td>
		<td valign="top" class="primary">
<?php
	$mattypes = new MaterialTypes;

	if (isset($biblio['material_cd'])) {
		$material_cd_value = $biblio['material_cd'];
	} elseif (isset($_GET['material_cd'])) {
	  $material_cd_value = $_GET['material_cd'];
	} else {
		$material_cd_value = $mattypes->getDefault();
	}
	echo inputfield('select', "materialCd", $material_cd_value, array('onchange'=>"matCdReload()"), $mattypes->getSelect());
			?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Collection:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php
				$collections = new Collections;
				if (isset($biblio['collection_cd'])) {
					$value = $biblio['collection_cd'];
				} else {
					$value = $collections->getDefault();
				}
				echo inputfield('select', "collectionCd", $value, NULL, $collections->getSelect());
			?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary" valign="top">
			<?php echo T("Show in OPAC:"); ?>
		</td>
		<td valign="top" class="primary">
			<input type="checkbox" name="opacFlg" value="CHECKED"
				<?php if (isset($biblio) and $biblio['opac_flg'] == 'Y') echo H('checked="checked"'); ?> />
		</td>
	</tr>

	<tr>
		<td colspan="2" nowrap="true" class="primary">
			<b><?php echo T("USMarc Fields:"); ?></b>
		</td>
	</tr>
<?php
	function getlabel($f) {
		global $marcSubflds, $marcTags;
		$label = "";
		if ($f['label'] != "") {
			$label = $f['label'];
		} elseif ($f['subfield'] != "") {
			$idx = sprintf("%03d%s", $f['tag'], $f['subfield']);
			$label = $marcSubflds[$idx]->getDescription();
		} else {
			$label = $marcTags[$f['tag']]->getDescription();
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
			'form_type' => $f['form_type']);
	}

	$mf = new MaterialFields;
	$fields = $mf->getMatches(array('material_cd'=>$material_cd_value), 'position');
	if ($fields->count() == 0) {
?>
	<tr>
		<td colspan="2" class="primary"><?php echo T('No fields to fill in.'); ?></td>
	</tr>
<?php
	}
	$inputs = array();
	while (($f=$fields->next()) !== NULL) {
		$tags = array();
		if (isset($biblio)) {
			$tags = $biblio['marc']->getFields($f['tag']);
			if ($f['auto_repeat'] != Tag && count($tags) > 0) {
				$tags = array($tags[0]);
			}
		}
		foreach ($tags as $t) {
			$subfs = array();
			if ($f['subfield_cd'] != "") {
				$subfs = $t->getSubfields($f['subfield_cd']);
				if ($f['auto_repeat'] != Subfield && count($subfs) > 0) {
					$subfs = array($subfs[0]);
				}
			}
			foreach ($subfs as $sf) {
				array_push($inputs,
					mkinput($t->fieldid,
						$sf->subfieldid,
						$sf->data, $f));
			}
			if (count($subfs) == 0 || $f['auto_repeat'] == 'Subfield') {
				array_push($inputs, mkinput($t->fieldid, NULL, NULL, $f));
			}
		}
		if (count($tags) == 0 || $f['auto_repeat'] == 'Tag') {
			array_push($inputs, mkinput(NULL, NULL, NULL, $f));
		}
	}

	foreach ($inputs as $n => $i) {
?>
	<tr>
		<td class="primary" valign="top" style="width: 30%">
<?php
		if ($i['required'] == 'Y') {
			echo '<sup>*</sup>';
		}
		echo H($i['label'].":");
?>
		</td>
		<td valign="top" class="primary">
			<input type="hidden" name="fields[<?php echo H($n); ?>][tag]"
						 value="<?php echo H($i['tag']); ?>" />
			<input type="hidden" name="fields[<?php echo H($n); ?>][subfield_cd]"
						 value="<?php echo H($i['subfield']); ?>" />
			<input type="hidden" name="fields[<?php echo H($n); ?>][fieldid]"
						 value="<?php echo H($i['fieldid']); ?>" />
			<input type="hidden" name="fields[<?php echo H($n); ?>][subfieldid]"
						 value="<?php echo H($i['subfieldid']); ?>" />
<?php
		if ($i['form_type'] == 'text') {
?>
			<input style="width: 100%" type="text" name="fields[<?php echo H($n); ?>][data]"
				value="<?php echo H($i['data']); ?>" />
<?php
		} else {
			// IE seems to make the font-size of a textarea overly small under
			// certain circumstances.  We force it to a sane value, even
			// though I have some misgivings about it.  This will make
			// the font smaller for some people.
?>
			<textarea style="width: 100%; font-size: 10pt; font-weight: normal" rows="7" name="fields[<?php echo H($n); ?>][data]"><?php echo H($i['data']); ?></textarea>
<?php
		}
?>
		</td>
	</tr>
<?php
	}
?>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='<?php echo H($cancelLocation);?>'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>

</table>
