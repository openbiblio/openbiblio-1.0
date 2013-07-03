<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "bookings/cart";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Links.php"));

	#****************************************************************************
	#*  Retrieving post vars and scrubbing the data
	#****************************************************************************
	if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
		$currentPageNmbr = $_REQUEST["page"];
	} else {
		$currentPageNmbr = 1;
	}

	$rpt = Report::create(booking_cart, BookingCart);
	$rpt->initCgi();
	$total_items = $rpt->count();

	Nav::node('circulation/bookings/cart/pull_list', T("Pull List"), '../shared/layout.php?name=pull_list&rpt=BookingCart');
	Nav::node('circulation/bookings/cart/packing_slip', T("Packing Slips"), '../shared/layout.php?name=packing_slip&rpt=BookingCart');

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	# Display no results message if no results returned from search.
	if ($total_items == 0) {
		echo '<p>'.T("Booking cart is empty").'</p>';
		exit();
	}

	$p = array('type'=>'previous');
	if (isset($_REQUEST['rpt_order_by'])) {
		$p['rpt_order_by'] = $_REQUEST['rpt_order_by'];
	}
	$page_url = new LinkUrl("../circ/booking_cart.php", 'page', $p);
	$sort_url = new LinkUrl("../circ/booking_cart.php", 'rpt_order_by', $p);
	$disp = new ReportDisplay($rpt);
	echo '<div class="results_count">';
	echo T("%count% bookings in cart.", array('count'=>$rpt->count()));
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
<input type="hidden" name="name" value="bookingid" />
<table class="resultshead">
	<tr>
			<th><?php echo T("Booking Cart"); ?></th>
		<td class="resultshead">
<table class="buttons">
<tr>
<td><input type="submit" value="<?php echo T("Remove from Cart"); ?>" /></td>
</tr>
</table>
</td>
</tr>
</table>
<?php

$t = new TableDisplay;
$t->columns = $disp->columns($sort_url);
array_unshift($t->columns, $t->mkCol('<b>'.T("All").'</b><br /><input type="checkbox" name="all" value="all" onclick="setCheckboxes()" />'));
echo $t->begin();
$page = $rpt->pageIter($currentPageNmbr);
while ($r = $page->fetch_assoc()) {
	$dr = $disp->row($r);
	array_unshift($dr, '<input type="checkbox" name="id[]" value="'.H($r['bookingid']).'" />');
	echo $t->rowArray($dr);
}
echo $t->end();

echo '</form>';

echo $disp->pages($page_url, $currentPageNmbr);
 ;
