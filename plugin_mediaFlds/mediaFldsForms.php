<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "mediaFlds";
  //$focus_form_name = "utilForm";
  //$focus_form_field = "collSet";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h1 id="pageHdr" class="title"><?php echo T("mediaFldsInOut"); ?></h1>

	<section id="entry">
		<fieldset id="actionArea">
		<legend><?php echo T("layoutExport"); ?></legend>
			<label for="material_cd" ><?php echo T("ChooseMedia"); ?>:</label>
				<select id="material_cd" ></select>
		  <input type="button" id="exportBtn" value="<?php echo T("Export"); ?>" />
		</fieldset>
		<fieldset id="actionArea">
		<legend><?php echo T("layoutInport"); ?></legend>
			<label for="newMedia"><?php echo T("NewMediaName"); ?>:</label>
				<input type="text" id="newMedia" name="newMedia" />
		  <input type="button" id="inportBtn" value="<?php echo T("Inport"); ?>" />
		</fieldset>
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../plugin_mediaFlds/mediaFldsJs.php'));
  require_once(REL(__FILE__,'../shared/listSrvr.php'));
?>
</body>
</html>
