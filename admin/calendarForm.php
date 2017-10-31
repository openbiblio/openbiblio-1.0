<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "calendarForm";
	$focus_form_name = "";
	$focus_form_field = "";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Calendar Manager'));

	//print_r($_SESSION); // for debugging
?>

<!-- ------------------------------------------------------------------------ -->
	<div id="listDiv">
		<fieldset>
		<legend id=listLegend>Select a Calendar</legend>
			<ul id="calList">
				<li>Filled by server</li>
			</ul>
		</fieldset>
		<input type="submit" id="calAddNewBtn" value="<?php echo T("Add New"); ?>" />
	</div>

<!-- ------------------------------------------------------------------------ -->
	<div id="editDiv">
		<h3><?php echo T("Edit Calendar"); ?></h3>

		<p class="note"><?php echo T("calendarEditFormMsg");?></p>
		<form role="form" id="editForm" name="editForm">
			<fieldset>
				<input type="hidden" id="calMode" name="mode" value="" />
				<input type="hidden" id="calName" name="oldName" value="" />
				<input type="hidden" id="calCd" name="calendar" value="" />

				<label for="name"><?php echo T("Name"); ?>:</label>
				<?php echo inputfield('text', 'name', $calname, array('size'=>'32', 'required'=>'required')); ?>
				<input type="submit" class="calSaveBtn" value="<?php echo T("Save Changes"); ?>" />
			</fieldset>

			<fieldset id="calArea">
			</fieldset>

			<ul class="btnRow">
				<li><input type="button" class="calGoBkBtn" value="<?php echo T("Go Back"); ?>" /></li>
				<li><input type="button" class="calDeltBtn" value="<?php echo T("Delete"); ?>" /></li>
			</ul>
		</form>
	</div>

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));
	include "../admin/calendarJs.php";
?>

</body>
</html>
