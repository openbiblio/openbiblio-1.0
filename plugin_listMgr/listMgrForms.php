<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * THIS IS AN OpenBiblio PLUG-IN  
 */
 
  $cache = NULL;
  require_once("../shared/common.php");

  $tab = "tools";
  $nav = "listMgr";
  $focus_form_name = "workForm";
  $focus_form_field = "listChkBtn";

  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h1 id="pageHdr" class="title"><?php echo T("pullDownListMgr"); ?></h1>
	<section>
		<fieldset id="entrySect">
		<legend><?php echo T("pullDownListMgr"); ?></legend>
		  <input type="button" id="showListsBtn" value="<?php echo T("ShowMe"); ?>" />
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="pullDowns">
			<legend><?php echo T("PullDowns"); ?></legend>
			<label for="calendar_cd" >Calendars:<select id="calendar_cd" ></select></label>
			<label for="collection_cd" >Collections:<select id="collection_cd" ></select></label>
			<br />
			<label for="media_cd" >Media:<select id="media_cd" ></select></label>
			<label for="mbrTyp_cd" >Member Types:<select id="mbrTyp_cd" ></select></label>
			<br />
			<label for="state_cd" >States:<select id="state_cd" ></select></label>
			<label for="site_cd" >Sites:<select id="site_cd" ></select></label>
			<br />
			<!--label for="inptType_cd" >Inputs:<select id="inptTyp_cd" ></select></label-->
			<label for="validation_cd" >Validations:<select id="validation_cd" ></select></label>
			<label for="calc_cd" >Due date calculators:<select id="calc_cd" ></select></label>
		</fieldset>
	</section>
	
<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../plugin_listMgr/listMgrJs.php"));
?>	
	
</body>
</html>	
