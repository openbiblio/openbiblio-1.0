<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "bookings/delete";
	require_once(REL(__FILE__, "../shared/logincheck.php"));


	$bookingid = $_GET["bookingid"];

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
	<form method="post" action="../circ/booking_del.php">
		<?php echo T("Really delete this booking?"); ?>
		<br /><br />
		<input type="hidden" name="bookingid" value="<?php echo H($bookingid) ?>" />
			<input type="submit" class="button" value="<?php echo T("Yes"); ?>" />
			<a href="../circ/booking_view.php?bookingid=<?php echo H($bookingid) ?>" class="small_button"><?php echo T("No"); ?></a>
</form>

<?php

	 ;
