<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "callNoUtil";
  //$focus_form_field = "collSet";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>


	<h1 id="pageHdr" class="title"><?php echo T("Call Number Utilities"); ?></h1>

	<section id="entry">
		<fieldset id="orfnArea">
		<legend><?php echo T("Make call numbers searchable"); ?></legend>
			<p class="note">
				This module checks for records that are missing field 099, which is
				what OpenBiblio uses to search for call numbers.
			</p>
		<select name="mode" id="callNoMode">
			<option value="search" selected>Find records without call numbers</option>
			<option value="dry-run">Dry run of call number add</option>
			<option value="add">Add call numbers to records</option>
		</select><br />
		<span id="call_number_schemata">
		<input type="checkbox" name="050">Library of Congress call number (050)</input><br />
		<input type="checkbox" name="090">Library of Congress call number (090)</input><br />
		<input type="checkbox" name="082">Dewey Decimal number (082)</input><br />
		<input type="checkbox" name="080">Universal Decimal number (080)</input><br />
		<input type="checkbox" name="055">Canadian (LAC) call number (055)</input><br />
		<input type="checkbox" name="060">National Library of Medicine call number (060)</input><br />
		<input type="checkbox" name="070">National Agricultural Library call number (070)</input><br />
		</span>
		<input type="button" id="callNoChkBtn" value="<?php echo T("Scan"); ?>" />
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../plugin_callNoUtils/callNoJs.php'));
?>	
</body>
</html>
