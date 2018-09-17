<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  $cache = NULL;
  require_once("../shared/common.php");

  $tab = "tools";
  $nav = "mediaFlds";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
	<h1 id="pageHdr" class="title"><?php echo T("mediaFldsInOut"); ?></h1>

	<section id="entry">
		<fieldset id="actionArea">
		<legend><?php echo T("layoutExport"); ?></legend>
			<p class="note">
				 1. Select Existing Layout.<br />
				 2. Highlight entire output below.<br />
				 3. Copy highlighted text to favorite text editor.<br />
				 4. Save using a meaningful name.<br />
			</p>
			<hr />
			<label for="exportMedia" ><?php echo T("ChooseMedia"); ?>:</label>
				<select id="exportMedia" ></select>
		  <input type="button" id="exportBtn" value="<?php echo T("FetchLayout"); ?>" />
		</fieldset>

		<br />

		<fieldset id="actionArea">
		<legend><?php echo T("layoutimport"); ?></legend>
			<p class="note"><?php echo T("importInstructions"); ?><p>
			<hr />
			<label for="newMedia"><?php echo T("NewLayoutFile"); ?>:</label>
				<input type="file" id="newLayout" name="newLayout" /><br />
			<label for="importMedia" ><?php echo T("UseForMedia"); ?>:</label>
				<select id="importMedia" ></select>
		  <input type="button" id="importBtn" value="<?php echo T("importLayout"); ?>" />
		</fieldset>
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../plugin_mediaFlds/mediaFldsJs.php'));
  require_once(REL(__FILE__,'../shared/listSrvr.php'));
?>
</body>
</html>
