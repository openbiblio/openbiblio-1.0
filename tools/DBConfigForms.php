<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  $cache = NULL;
  require_once("../shared/common.php");

  $tab = "tools";
  $nav = "chgColl";
  $focus_form_name = "chgForm";
  $focus_form_field = "collSet";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
	<style>
		.rowflex-container { display:flex; flex-flow: row; }
		table { border:2px solid blue; margin:0 0.5em 0 0.5em; }
	</style>

	<h3 id="pageHdr" class="title"><?php echo T("DbServerInformation"); ?></h3>

	<section id="info">
		<fieldset id="srvrVars">
		<legend>Curent Database Server Settings</legend>
			<h5> MySQL version <span id="version"></span></h5>
			<div class="rowflex-container">
			<table  id="srvrCharSets" border="1">
				<caption>Character Sets</caption>
				<thead><tr><th>Name</th><th>Value</th></tr></thead>
				<tbody class="striped"></tbody>
			</table>
			<table  id="srvrCollations" border="1">
				<caption>Collation Settings</caption>
				<thead><tr><th>Name</th><th>Value</th></tr></thead>
				<tbody class="striped"></tbody>
			</table>
			</div>
			<table  id="srvrEngines" border="1">
				<caption>Engines Available</caption>
				<thead><tr><th>Name</th><th>Support</th><th>Transactions</th></tr></thead>
				<tbody class="striped"></tbody>
			</table>
			<table id="srvrMiscVar" border="1">
				<caption>Misc. Variables</caption
				<thead><tr><th>Name</th><th>Value</th></tr></thead>
				<tbody class="striped"></tbody>
			</table>
		</fieldset>
	</section>

	<section id="entry">
		<fieldset>
		<legend>Change DB Collation HERE</legend>
			<form role="form" id="chgForm" name ="chgColl">
				<label for="collSet"><?php echo T("Collation"); ?>: </label>
				<select id="collSet" name="collSet">
				</select>
				<br />
				<label for="db"><?php echo T("Database"); ?>: </label>
		   		<input type="text" size="30" name="db" value="<?php echo OBIB_DATABASE; ?>" />
		   		<br />
		   		<input type="button" id="action" name="action" value="Change Database" />
		   </form>
		</fieldset>   
	</section>
	
	<section id="rsltsArea">
		<fieldset id="chgRslts">
		<legend><?php echo T("Changing collation in all %db% tables"); ?></legend>
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../tools/DBConfigJs.php'));
?>	
</body>
</html>
