<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "tranUtil";
  $focus_form_name = "utilForm";
  $focus_form_field = "collSet";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h3 id="pageHdr" class="title"><?php echo T("transUtilities"); ?></h3>

	<section id="entry">
		<fieldset id="cmnArea">
			<p><label><?php echo T("Current Locale"); ?>: </label><span id="crntLoc"></span></p>
			<label for="locSet"><?php echo T("Working Locale"); ?>: </label>
			<select id="locSet" name="locSet">
			</select>
		</fieldset> 
		
		<fieldset id="dupArea">
		<legend><?php echo T("Check4Dups"); ?></legend>
		  <input type="button" id="dupChkBtn" value="<?php echo T("Scan"); ?>" />
		</fieldset> 
		
		<fieldset id="orfnArea">
		<legend><?php echo T("Check4Unused"); ?></legend>
		  <input type="button" id="orfnChkBtn" value="<?php echo T("Scan"); ?>" />
		</fieldset> 
		
		<fieldset id="absntArea">
		<legend><?php echo T("Check4Needed"); ?></legend>
		  <input type="button" id="absntChkBtn" value="<?php echo T("Scan"); ?>" />
		</fieldset> 
		
		<fieldset id="maybeArea">
		<legend><?php echo T("Check4Maybe"); ?></legend>
			<!--p class="error">Under Construction</p-->
		  <input type="button" id="maybeChkBtn" value="<?php echo T("Scan"); ?>" />
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../plugin_transUtils/transUtilJs.php'));
?>	
</body>
</html>
