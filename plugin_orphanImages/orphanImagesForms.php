<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * THIS IS AN OpenBiblio PLUG-IN  
 */
 
  $cache = NULL;
  require_once("../shared/common.php");

  $tab = "tools";
  $nav = "oldFiles";
  $focus_form_name = "workForm";
  $focus_form_field = "orfnChkBtn";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h1 id="pageHdr" class="title"><?php echo T('OrphanImageFinder'); ?></h1>
	<section>
		<fieldset id="orfnFiles">
		<legend><?php echo T("CheckForOrphanImages"); ?></legend>
		  <input type="button" id="orfnChkBtn" value="<?php echo T("Scan"); ?>" />
		  <label for="detl">Details</label>
		  	<select id="detl">
		  		<option selected value="No">No</option>
		  		<option value="Yes" >Yes</option>
		  	</select>
		  <label for="verb">Verbose</label>
		  	<select id="verb">
		  		<option selected value="No">No</option>
		  		<option value="Yes" >Yes</option>
		  	</select>
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts"> </fieldset>
	</section>
	
<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../plugin_orphanImages/orphanImagesJs.php"));
?>	
	
</body>
</html>	
