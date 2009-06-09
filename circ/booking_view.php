<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "bookings/view";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Bookings.php"));
	require_once(REL(__FILE__, "../model/Members.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/Cart.php"));
	require_once(REL(__FILE__, "../classes/Buttons.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));


	#****************************************************************************
	#*  Checking for get vars.  Go back to form if none found.
	#****************************************************************************
	if (!$_REQUEST['bookingid']) {
		header("Location: ../circ/bookings.php");
		exit();
	}

	#****************************************************************************
	#*  Retrieving get var
	#****************************************************************************
	$bookingid = $_REQUEST['bookingid'];
	if ($_REQUEST["msg"]) {
		$msg = '<p class="error">'.H($_REQUEST["msg"]).'</p><br /><br />';
	} else {
		$msg = "";
	}

	$bookings = new Bookings;
	$booking = $bookings->getOne($bookingid);

	$_SESSION['currentBookingid'] = $bookingid;

	$biblios = new Biblios();
	$biblio = $biblios->getOne($booking['bibid']);
	assert($biblio != NULL);

	$members = new Members;
	$mbrs = array();
	foreach ($booking['mbrids'] as $mbrid) {
		$mbr = $members->getOne($mbrid);
		$mbrs[] = $mbr;
	}

	if (isset($_REQUEST['rpt'])) {
		$rpt = Report::load($_REQUEST['rpt']);
	} else {
		$rpt = NULL;
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	echo $msg;

	if (isset($_SESSION['postVars']['confirm_book_dt'])) {
		echo '<p class="error">'.T("To ignore book again").'</p>';
	}

	if ($rpt and isset($_REQUEST['seqno']) and $rpt->count() > 1) {
		$p = $rpt->row($_REQUEST['seqno']-1);
		$n = $rpt->row($_REQUEST['seqno']+1);
		echo '<table style="margin-bottom: 10px" width="60%" align="center"><tr><td align="left">';
		if ($p) {
			echo '<a href="../circ/booking_view.php?bookingid='.HURL($p['bookingid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($p['.seqno']).'" accesskey="p">&laquo;'.T("Prev").'</a>';
		}
		echo '</td><td align="center">';
		echo T("Booking %item% out of %items% in sequence", array('item'=>H($_REQUEST['seqno']+1), 'items'=>H($rpt->count())));
		echo '</td><td align="right">';
		if ($n) {
			echo '<a href="../circ/booking_view.php?bookingid='.HURL($n['bookingid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($n['.seqno']).'" accesskey="n">'.T("Next").'&raquo;</a>';
		}
		echo '</td></tr></table>';
	}
?>
<table class="resultshead">
	<tr>
		<th><?php echo T("Booking Information"); ?></th>
		<td class="resultshead">
<?php
	$buttons = array(
		array(T("Delete"), '../circ/booking_del.php?bookingid='.U($bookingid),
					T("Really delete booking of %item%?", array('item'=>H($biblio['marc']->getValue('099$a'))))),
	);
	$cart = getCart($bookingid);
	$params = 'name=bookingid&id[]='.U($bookingid).'&tab='.U($tab);
	if ($cart->contains($bookingid)) {
		$buttons[] = array(T("Remove from Cart"), '../shared/cart_del.php?'.$params);
	} else {
		$buttons[] = array(T("Add To Cart"), '../shared/cart_add.php?'.$params);
	}
	echo Buttons::display($buttons);
?>
		</td>
	</tr>
</table>
<form method="post" action="../circ/mbr_search.php">
<input type="hidden" name="rpt_terms[0][type]" value="keyword" />
<input type="hidden" name="rpt_terms[0][exact]" value="0" />
<table class="biblio_view">
	<tr>
		<td class="name"><?php echo T("Item:"); ?></td>
		<td class="value"><a href="../shared/biblio_view.php?bibid=<?php echo H($booking[bibid]) ?>&amp;tab=cataloging">
			(<?php echo H($biblio['marc']->getValue('099$a')) ?>)
			<?php echo H($biblio['marc']->getValue('245$a')) ?>
			<?php echo H($biblio['marc']->getValue('245$b')) ?>
		</a></td>
	</tr>
	<tr>
		<td class="name"><?php echo T("Members:"); ?></td>
		<td class="value">
			<table>
<?php
	foreach ($mbrs as $member) {
		echo '<tr><td><a href="../circ/mbr_view.php?mbrid='.HURL($member['mbrid']).'">';
		echo H($member['first_name'].' '.$member['last_name'].' ');
		echo '('.H($member['barcode_nmbr']).')</a>';
		echo '</td><td><a href="../circ/booking_edit.php?bookingid='.HURL($bookingid).'&amp;id[]='.HURL($member['mbrid']).'&amp;action_booking_mbr_del=1" class="small_button">'.T("Remove").'</a></td></tr>';
	}
?>
				<tr><td><input type="text" size="15" name="rpt_terms[0][text]" /></td><td><input type="submit" class="button" value="<?php echo T("Add Member..."); ?>" /></td></tr>
			</table>
		</td>
	</tr>
</table>
</form>
<form method="post" action="../circ/booking_edit.php">
<input type="hidden" name="bookingid" value="<?php echo H($bookingid); ?>" />
<?php
	if (isset($_SESSION['postVars']['confirm_book_dt'])) {
		$booking['book_dt'] = $_SESSION['postVars']['confirm_book_dt'];
		echo '<input type="hidden" name="confirm_book_dt" value="'
				 . H($_SESSION['postVars']['confirm_book_dt']).'" />';
	}
	if (isset($_SESSION['postVars']['confirm_due_dt'])) {
		$booking['due_dt'] = $_SESSION['postVars']['confirm_due_dt'];
		echo '<input type="hidden" name="confirm_due_dt" value="'
				 . H($_SESSION['postVars']['confirm_due_dt']).'" />';
	}
?>
<table class="biblio_view">
	<tr>
		<td class="name"><?php echo T("Out Date:"); ?></td>
		<td class="value"><input size="10" type="text" name="book_dt" value="<?php echo H($booking['book_dt']) ?>" /></td>
	</tr>
	<tr>
		<td class="name"><?php echo T("Due Date:"); ?></td>
		<td class="value"><input size="10" type="text" name="due_dt" value="<?php echo H($booking['due_dt']) ?>" /></td>
	</tr>
	<tr><td></td><td><input class="button" type="submit" value="<?php echo T("Change Dates"); ?>" /></td></tr>
</table>

<?php

	Page::footer();
