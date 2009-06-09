<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "calendars/del";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Calendars.php"));

	if (!isset($_REQUEST["calendar"])){
		header("Location: ../admin/calendars_list.php");
		exit();
	}

	$calendar = $_REQUEST['calendar'];

	$calendars = new Calendars;
	$cal = $calendars->getOne($_REQUEST["calendar"]);

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delcalendarform" method="post" action="../admin/calendar_del.php">
<input type="hidden" name="calendar" value="<?php echo H($_REQUEST["calendar"]) ?>" />
<input type="hidden" name="name" value="<?php echo H($cal['description']) ?>" />
<?php echo T('calendarDelConfirmMsg', array('desc'=>H($cal['description']))); ?><br /><br />
			<input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
			<a class="small_button" href="../admin/calendars_list.php"><?php echo T("Cancel"); ?></a>
</form>
</center>
<?php

	Page::footer();
