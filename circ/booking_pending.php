<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$restrictInDemo = true;
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../classes/Date.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/Table.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));

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
	$(document).ready(function () {
		$('#all').bind('click',null,function () {
		  if ($('#all:checked').val() == 'all') {
				$('checkbox.rowCkBox').attr('checked','CHECKED');
			}
		});
	});
</script>

<form name="selection" id="selection" action="../circ/booking_checkout.php" method="post">
	<input type="hidden" name="tab" value="<?php echo HURL($tab)?>" />
	<input type="hidden" name="name" value="bibid" />
	<fieldset>
	<legend><?php echo T("Pending Bookings"); ?></legend>
		<input type="submit" value="<?php echo T("Check Out"); ?>" /></a>

<?php
	$disp = new ReportDisplay($rpt);
	$t = new TableDisplay;
	$t->columns = $disp->columns();
	//array_unshift($t->columns, $t->mkCol('<b>'.T("All").'</b><br /><input type="checkbox" id="all"  name="all" value="all" onclick="setCheckboxes()" />'));
	$guts = '<label for="all">'.T("All").'</label><br />'
				 .'<input type="checkbox" id="all" name="all" value="all" />';
	array_unshift($t->columns, $t->mkCol($guts));

	echo $t->begin();
	$selected = array();
	while ($r = $rpt->next()) {
		$available = array();
		foreach ($r['copies'] as $c) {
			if (!in_array($c['barcode_nmbr'], $selected)) {
				$available[] = $c['barcode_nmbr'];
			}
		}
		$checkbox = '<input type="checkbox" id="id[]" name="id[]" '
							 .'value="'.H($r['bookingid']).'" '
							 .'class="rowCkBox" ';
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
?>
	</fieldset>
</form>
	
