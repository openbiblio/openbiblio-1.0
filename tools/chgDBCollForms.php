<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "chgColl";
  $focus_form_name = "chgForm";
  $focus_form_field = "collSet";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h3 id="pageHdr" class="title"><?php echo T("ChangeDBCollation"); ?></h3>

	<section id="entry">
		<fieldset>
			<form id="chgForm" name ="chgColl">
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
	
	<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../tools/chgDBCollJs.php'));
?>	
</body>
</html>
