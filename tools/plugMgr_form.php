<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "plugMgr";
  $focus_form_name = "workForm";
  $focus_form_field = "list";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	require_once(REL(__FILE__, "plugMgr_js.php"));
?>
<!--h1><span id="pageHdr" class="title"><?php //echo T('PLugin Manager'); ?></span></h1-->
<h1 id="pageHdr" class="title"><?php echo T('Plugin Manager'); ?></h1>

<div id="listDiv">
	<form id="listForm" name="listForm" class="form">
		<label for="pluginOK"><?php echo T('Plugins Allowed?'); ?></label>
  	<?php echo inputfield('checkbox', 'pluginOK', 'Y', NULL, $_SESSION['allow_plugins_flg']); ?>

		<fieldset id="pluginSet">
		  <h5><?php echo T('Select Plugins'); ?></h5>
   		<ul id="pluginList" ></ul>
		</fieldset>

		<!--input type="hidden" id="editMode" name="editMode" value="">
    <input type="hidden" id="material_field_id" name="material_field_id" value="">
		<input type="button" id="editUpdtBtn" value="<?php //echo T("Update"); ?>" class="button" />
		<input type="button" id="editCnclBtn" value="<?php //echo T("goBack"); ?>" class="button" />
		<input type="button" id="editDeltBtn" value="<?php //echo T("Delete"); ?>" class="button" /-->
	</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php include(REL(__FILE__,"../shared/footer.php")); ?>
