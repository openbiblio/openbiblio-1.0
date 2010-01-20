<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "biblio/edit";
	$helpPage = "biblioEdit";
	$focus_form_name = "editbiblioform";
	$focus_form_field = "materialCd";
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));

	if (isset($_GET["bibid"])){
		#****************************************************************************
		#*  Retrieving get var
		#****************************************************************************
		$bibid = $_GET["bibid"];
		$postVars[bibid] = $bibid;
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
	$headerWording = T("Edit");

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
			echo '<a href="../catalog/biblio_edit_form.php?bibid='.HURL($p['bibid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($p['.seqno']).'" accesskey="p">&laquo;'.T("Prev").'</a>';
		}
		echo '</td><td align="center">';
		echo T("Record %item% of %items%", array('item'=>H($_REQUEST['seqno']+1), 'items'=>H($rpt->count())));
		echo '</td><td align="right">';
		if ($n) {
			echo '<a href="../catalog/biblio_edit_form.php?bibid='.HURL($n['bibid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($n['.seqno']).' accesskey="n">'.T("Next").'&raquo;</a>';
		}
		echo '</td></tr></table>';
	}
?>
	<script type="text/javascript">
		<!--
		bef = {
			init: function () {
			  $('#materialCd').bind('change',null,bef.matCdReload);
				$('#biblioFldTbl tbody#marcBody tr:not(.hidden):even').addClass('altBG');
			},
			matCdReload: function (){
				//alert("test");
				//document.newbiblioform.posted.value='media_change';
				$('#editBiblioForm').submit();
			}
		}
		$(document).ready(bef.init);
		//-->
	</script>
	
	<h1><span id="searchHdr" class="title"><?php echo T('Edit Item'); ?></span></h1>
	<form id="editBiblioForm" name="editbiblioform" method="post" action="../catalog/biblio_edit.php">
	<input type="hidden" name="bibid" value="<?php echo H($postVars["bibid"]);?>" />

<?php

	include(REL(__FILE__, "../catalog/biblio_fields.php"));
	Page::footer();
