<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "cataloging";
	$nav = "biblio/editmarc";
	$helpPage = "biblioMarcEdit";
	$focus_form_name = "editmarcform";
	$focus_form_field = "materialCd";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));

	if (isset($_REQUEST["bibid"])){
		$bibid = $_REQUEST["bibid"];
		$postVars['bibid'] = $bibid;
		$_SESSION["postVars"] = $postVars;
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
		$bibid = $postVars["bibid"];
	}
	#****************************************************************************
	#*  Search database
	#****************************************************************************
	$biblios = new Biblios();
	$biblio = $biblios->getOne($bibid);

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	$cancelLocation = "../shared/biblio_view.php?bibid=".urlencode($bibid);

	if (isset($_REQUEST["msg"])) {
		echo '<p class="error">'.H($_REQUEST["msg"]).'</p>';
	}
	if (isset($_REQUEST['rpt'])) {
		$rpt = Report::load($_REQUEST['rpt']);
	} else {
		$rpt = NULL;
	}
	if ($rpt and isset($_REQUEST['seqno'])) {
		$p = $rpt->row($_REQUEST['seqno']-1);
		$n = $rpt->row($_REQUEST['seqno']+1);
		echo '<table style="margin-bottom: 10px" width="60%" align="center"><tr><td align="left">';
		if ($p) {
			echo '<a href="../catalog/biblio_marc_edit_form.php?bibid='.HURL($p['bibid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($p['.seqno']).'" accesskey="p">&laquo;'.T("Prev").'</a>';
		}
		echo '</td><td align="center">';
		echo T("Record %item% of %items%", array('item'=>H($_REQUEST['seqno']+1), 'items'=>H($rpt->count())));
		echo '</td><td align="right">';
		if ($n) {
			echo '<a href="../catalog/biblio_marc_edit_form.php?bibid='.HURL($n['bibid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($n['.seqno']).'" accesskey="n">'.T("Next").'&raquo;</a>';
		}
		echo '</td></tr></table>';
	}
?>
	<a href="biblio_view_full.php?bibid=<?php echo $bibid;?>"><?php echo T("Detailed View"); ?></a>&nbsp;|&nbsp;
	<a href="biblio_view_marc.php?bibid=<?php echo $bibid;?>"><?php echo T("MARC View"); ?></a>&nbsp;|&nbsp;
	<a href="biblio_cite.php?bibid=<?php echo $bibid;?>" target="_citation"><?php echo T("Citation"); ?></a>

	<fieldset>
	<legend><?php echo T("Item"); ?></legend>
	<table class="primary" width="100%">
	<tbody id="nonMarcBody">
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Type of Material:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php
				$mattypes = new MaterialTypes;
				echo $mattypes->get_name($biblio['material_cd']);
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
				echo $collections->get_name($biblio['collection_cd']);
			?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary" valign="top">
			<?php echo T("Show in OPAC:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php if ($biblio['opac_flg']=='Y'){
				echo 'Yes';
			} else {
				echo 'No';
			}; ?>
		</td>
	</tr>
	<tr>
		<td colspan="2" nowrap="true" class="primary">
			<b><?php echo T("MARC Record:"); ?></b>
		</td>
	</tr>
	<tbody id="marcBody">
	<tr>
		<td colspan="2" nowrap="true" class="primary">
			<?php echo nl2br($biblio['marc']->getMnem()); ?>
		</td>
	</tr>
	<tbody>
	</table>
	</fieldset>
	
<?php

	Page::footer();
