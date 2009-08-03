<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	$tab = "cataloging";
//	if (isset($_REQUEST["tab"])) {
	if (!empty($_REQUEST["tab"])) { // tab was SET to '' by menu functions
		$tab = $_REQUEST["tab"];
	}
	$_REQUEST['tab'] = $tab;

	$nav = "cart";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Links.php"));
	require_once(REL(__FILE__, "../functions/info_boxes.php"));

	$rpt = Report::create('biblio_cart', 'BiblioCart');
	if (!$rpt) {
		Fatal::internalError(T("Unexpected error creating report"));
	}
	$rpt->initCgi();

	if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
		$currentPageNmbr = $_REQUEST["page"];
	} else {
		$currentPageNmbr = $rpt->curPage();
	}
	$total_items = $rpt->count();

	if ($tab == "opac") {
		Nav::node('cart/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioCart&tab=opac');
		Page::header_opac(array('nav'=>$nav, 'title'=>''));
	} else {
		Nav::node('cataloging/cart/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioCart');
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	}
	if (isset($_REQUEST["msg"]) && !empty($_REQUEST["msg"])) {
		echo '<p class="error">'.H($_REQUEST["msg"]).'</p><br /><br />';
	}
	# Display no results message if no results returned from search.
	if ($total_items == 0) {
	  echo "<h3>Request Cart</h3>";
		echo "<p class=\"error\">".T("Cart is empty")."</p>";
		Page::footer();
		exit();
	}
	currentMbrBox();
	$p = array('type'=>'previous');
	if (isset($_REQUEST['rpt_order_by'])) {
		$p['rpt_order_by'] = $_REQUEST['rpt_order_by'];
	}
	$page_url = new LinkUrl("../shared/req_cart.php", 'page', $p);
	$sort_url = new LinkUrl("../shared/req_cart.php", 'rpt_order_by', $p);
	$disp = new ReportDisplay($rpt);
	echo '<div class="results_count">';
	echo T("%count% items in cart.", array('count'=>$rpt->count()));
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
<form name="selection" id="selection" action="../shared/cart_del.php" method="post">
<input type="hidden" name="tab" value="<?php echo HURL($tab)?>" />
<input type="hidden" name="name" value="bibid" />
<table class="resultshead">
	<tr>
			<th><?php echo T("Request Cart"); ?></th>
		<td class="resultshead">
<table class="buttons">
<tr>
<?php
	if ($tab == "opac") {
?>
<td><a href="../shared/request.php"><?php echo T("Submit Request"); ?></a></td>
<?php } ?>
<td><input type="submit" value="<?php echo T("Remove from Cart"); ?>" /></a></td>
</tr>
</table>
</td>
	</tr>
</table>
<?php

$t = new TableDisplay;
$t->columns = $disp->columns($sort_url);
array_unshift($t->columns, $t->mkCol('<b>'.T("All").'</b><br /><input type="checkbox" name="all" value="all" onclick="setCheckboxes();" />'));
echo $t->begin();
$page = $rpt->pageIter($currentPageNmbr);
while ($r = $page->next()) {
	$dr = $disp->row($r);
	array_unshift($dr, '<input type="checkbox" name="id[]" value="'.H($r['bibid']).'" />');
	echo $t->rowArray($dr);
}
echo $t->end();

echo '</form>';

echo $disp->pages($page_url, $currentPageNmbr);
Page::footer();
