<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "search";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Links.php"));

	#****************************************************************************
	#*  Checking for post vars.  Go back to form if none found.
	#****************************************************************************
	if (empty($_REQUEST)) {
		header("Location: ../circ/index.php");
		exit();
	}

	function getRpt() {
		if ($_REQUEST['type'] == 'previous') {
			$rpt = Report::load('MemberSearch');

			if ($rpt && $_REQUEST['rpt_order_by']) {
				$rpt = $rpt->variant(array('order_by'=>$_REQUEST['rpt_order_by']));
			}
			return $rpt;
		}

		$rpt = Report::create('member_search', 'MemberSearch');
		if (!$rpt) {
			return false;
		}
		$rpt->initCgi();
		return $rpt;
	}

	$rpt = getRpt();
	assert($rpt);

	if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
		$currentPageNmbr = $_REQUEST["page"];
	} else {
		$currentPageNmbr = $rpt->curPage();
	}

	#**************************************************************************
	#*  Show member view screen if only one result from query
	#**************************************************************************
	if ($rpt->count() == 1) {
		$mbr = $rpt->row(0);
		header("Location: ../circ/mbr_view.php?mbrid=".$mbr['mbrid']."&reset=Y");
		exit();
	}

	Nav::node('circulation/search/member_list', T("Print List"), '../shared/layout.php?name=member_list&rpt=MemberSearch');
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h3><?php echo T("Search Results"); ?></h3>

<?php

	#**************************************************************************
	#*  Show search results
	#**************************************************************************
	# Display no results message if no results returned from search.
	## FIXME - needs a 'goBack' ability so search criteria can be modified and re-submitted
	if ($rpt->count() == 0) {
		echo T("No results found.");
		Page::footer();
		exit();
	}

	$page_url = new LinkUrl("../circ/mbr_search.php", 'page', array('type'=>'previous'));
	$disp = new ReportDisplay($rpt);
	echo '<div class="results_count">';
	echo T("%count% results found.", array('count'=>$rpt->count()));
	echo '</div>';
	echo $disp->pages($page_url, $currentPageNmbr);

?>
<script type="text/javascript">
// based on a function from PhpMyAdmin
function setCheckboxes()
{
	var checked = document.forms['selection'].elements['all'].checked;
	var elts = document.forms['selection'].elements['id[]'];
	if (typeof(elts.length) != 'undefined') {
		for (var i = 0; i < elts.length; i++) {
			elts[i].checked = checked;
		}
	} else {
		elts.checked = checked;
	}
	return true;
}
</script>
<form name="selection" id="selection" action="../shared/dispatch.php" method="post">
<fieldset>
<input type="hidden" name="tab" value="<?php echo HURL($tab)?>" />
<table>
<?php
if ($_SESSION['currentBookingid']) {
	echo '<tr>';
	echo '	<td class="resultshead buttons">';
	echo '		<input type="hidden" name="bookingid" value="'.H($_SESSION['currentBookingid']).'" />';
	echo '		<input type="submit" name="action_booking_mbr_add" value="'.T("Add To Booking").'" />';
	echo '	</td>';
	echo '</tr>';
}
?>
</table>
<?php

$sort_url = new LinkUrl("../circ/mbr_search.php", 'rpt_order_by', array('type'=>'previous'));
$t = new TableDisplay;
$t->columns = $disp->columns($sort_url);
array_unshift($t->columns, $t->mkCol('<b>'.T("All").'</b><br /><input type="checkbox" name="all" value="all" onclick="setCheckboxes()" />'));
echo $t->begin();
$page = $rpt->pageIter($currentPageNmbr);
while ($r = $page->next()) {
	$dr = $disp->row($r);
	array_unshift($dr, '<input type="checkbox" name="id[]" value="'.H($r['mbrid']).'" />');
	echo $t->rowArray($dr);
}
echo $t->end();

?>

</fieldset>
</form>

<?php
echo $disp->pages($page_url, $currentPageNmbr);
Page::footer();
