<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "user";
  $nav = "doiSearch";
  $focus_form_name = "doiForm";
  $focus_form_field = "doiCd";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h1 id="pageHdr" class="title"><?php echo T("doiSearch"); ?></h1>

	<section id="entry">
		<fieldset id="srchArea">
		<legend><?php echo T("Search4DocumentInfo"); ?></legend>
			<p class="note">
			This modeule is intended to search the servers<br >
			of the GLOBAL HANDLE REGISTRY for documents.
			</p>
			<form id="doiForm">
			<label for="doiCd"><?php echo T("EnterDOI2Resolve"); ?></label><br />
			<input type="text" id="doiCd" name="doiCd" required \><br />
		  <input type="submit" id="srchBtn" value="<?php echo T("Search"); ?>" />
			</form>
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'doiSearchJs.php'));
?>	

</body>
</html>
