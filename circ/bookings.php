<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "bookings";
	$focus_form_name = "booking_pending";
	$focus_form_field = "rpt_out_before";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	require_once(REL(__FILE__, "../classes/Date.php"));

	if (isset($_REQUEST['msg'])) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}
?>

<h3><?php echo T("Manage Bookings"); ?></h3>
<form name="booking_pending" method="get" action="../circ/booking_pending.php">
<fieldset>
<legend><?php echo T("Pending Bookings"); ?></legend>
<table class="primary">
	<tbody class="unStriped">
	<tr>
		<td nowrap="true" class="primary">
			<label for="rpt_out_before"><?php echo T("For Date"); ?>:</label>
			<?php echo inputfield('text', 'rpt_out_before', 'today', array('size'=>10)); ?>
			<input type="submit" value="<?php echo T("Search"); ?>" class="button" />
		</td>
	</tr>
	</tbody>
</table>
</fieldset>
</form>

<?php

	 ;
