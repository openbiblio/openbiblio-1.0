<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	session_cache_limiter(null);

	$tab = "working";
	$nav = "calendarForm";
	$focus_form_name = "";
	$focus_form_field = "";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Calendar Manager'));

	//print_r($_SESSION); // for debugging
?>

	<p id="errSpace" class="error"></p>

<!-- ------------------------------------------------------------------------ -->
	<div id="listDiv">
		<fieldset>
		<legend id=listLegend>Select a Calendar</legend>
			<ul id="calList">
				<li>Filled by server</li>
			</ul>
		</fieldset>
	</div>

<!-- ------------------------------------------------------------------------ -->
	<div id="editDiv">
		<h3><?php echo T("Edit Calendar"); ?></h3>

		<p class="note"><?php echo T("calendarEditFormMsg");?></p>
		<form name="editForm" id="editForm">
			<fieldset>
				<input type="hidden" name="calendar" value="" />

				<table class="biblio_view">
				<tr>
					<th class="name" valign="bottom"><?php echo T("Name:"); ?></th>
					<td class="value" valign="bottom"><?php echo inputfield('text', 'name', $calname, array('size'=>'32', 'required'=>'required')); ?></td>
					<td class="value" valign="bottom"><input type="submit" id="calSave" value="<?php echo T("Save Changes"); ?>" class="button" /></td>
				</tr>
				</table>
			</fieldset>

			<fieldset id="calArea">
			</fieldset>
			<div style="padding-top: 4px; text-align: right">
				<input type="submit" value="<?php echo T("Save Changes"); ?>" class="button" />
			</div>
		</form>
	</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	include "./calendarJs.php";
?>

</body>
</html>
