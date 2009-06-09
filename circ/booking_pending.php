<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$restrictInDemo = true;
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../classes/Date.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/Table.php"));

	$tab = "circulation";
	$nav = "bookings/pending";


	if (count($_REQUEST) == 0) {
		header("Location: ../circ/index.php");
		exit();
	}

	$rpt = Report::create('pending_bookings', 'PendingBookings');
	assert($rpt);
	$errs = $rpt->initCgi_el();
	if (!empty($errs)) {
		FieldError::backToForm('../circ/bookings.php', $errs);
	}

	Nav::node('circulation/bookings/pending/pull_list', T("Pull List"), '../shared/layout.php?name=pull_list&rpt=PendingBookings');

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<script type="text/javascript">
// based on a function from PhpMyAdmin
function setCheckboxes()
{
	var checked = document.forms[selection].elements[all].checked;
	var elts = document.forms[selection].elements[id[]];
	if (typeof(elts.length) != undefined) {
		for (var i = 0; i < elts.length; i++) {
			elts[i].checked = checked;
		}
	} else {
		elts.checked = checked;
	}
	return true;
}
</script>
<form name="selection" id="selection" action="../circ/booking_checkout.php" method="post">
<input type="hidden" name="tab" value="<?php echo HURL($tab)?>" />
<input type="hidden" name="name" value="bibid" />
<table class="resultshead">
	<tr>
			<th><?php echo T("Pending Bookings"); ?></th>
		<td class="resultshead">
<table class="buttons">
<tr>
<td><input type="submit" value="<?php echo T("Check Out"); ?>" /></a></td>
</tr>
</table>
</td>
</tr>
</table>
<?php
	$disp = new ReportDisplay($rpt);
	$t = new TableDisplay;
	$t->columns = $disp->columns();
	array_unshift($t->columns, $t->mkCol('<b>'.T("All").'</b><br /><input type="checkbox" name="all" value="all" onclick="setCheckboxes()" />'));
	echo $t->begin();
	$selected = array();
	while ($r = $rpt->next()) {
		$available = array();
		foreach ($r['copies'] as $c) {
			if (!in_array($c['barcode_nmbr'], $selected)) {
				$available[] = $c['barcode_nmbr'];
			}
		}
		$checkbox = '<input type="checkbox" name="id[]" value="'.H($r['bookingid']).'" ';
		if (empty($available)) {
			$r['selected'] = '';
			$checkbox .= '/>';
		} else {
			$r['selected'] = $available[array_rand($available)];
			$selected[] = $r['selected'];
			$checkbox .= 'checked="checked" />';
		}
		$dr = $disp->row($r);
		array_unshift($dr,  $checkbox);
		echo $t->rowArray($dr);
	}
	echo $t->end();
	echo '</form>';

	require_once(REL(__FILE__, "../shared/footer.php"));
