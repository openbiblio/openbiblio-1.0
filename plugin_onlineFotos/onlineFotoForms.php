<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * THIS IS AN OpenBiblio PLUG-IN  
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "onlineFotos";
  $focus_form_name = "workForm";
  $focus_form_field = "listChkBtn";

  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
	<h1 id="pageHdr" class="title"><?php echo T("onlineFotos"); ?></h1>
	<h3 id="pageHdr" class="title"><?php echo T("sourceSelection"); ?></h3>

	<section id="entry">
		<fieldset id="srchArea">
			<legend><?php echo T("openLibrary"); ?></legend>
            <label for="fotoEnable"><?php echo T("enable"); ?><input type="checkbox" CHECKED value="Y" /></label>
            <br />
			<label for="fotoURL" >URL:<input type="url" value="http://covers.openlibrary.org"/></label>
            <br /><br />
		    <input type="button" id="submitBtn" value="<?php echo T("Submit"); ?>" />
		</fieldset>
	</section>
	
<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../plugin_onlineFotos/onlineFotoJs.php"));
?>	
	
</body>
</html>	
