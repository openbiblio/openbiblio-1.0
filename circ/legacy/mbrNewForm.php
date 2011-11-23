<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/MemberTypes.php"));

session_cache_limiter(null);

$tab = "circulation";
$restrictToMbrAuth = TRUE;
$nav = "new";
$focus_form_name = "newmbrForm";
$focus_form_field = "last_name";

require_once(REL(__FILE__, "../functions/inputFuncs.php"));
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../shared/get_form_vars.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
$headerWording = T("Add New");
?>

<!-- ------------------------------------------------------------------------ -->
<div id="newDiv">
	<h3 id="searchHdr"><?php echo $headerWording;?> <?php echo T("Member"); ?></h3>
	<form id="newmbrForm" name="newmbrForm" >

	<?php
		## default entries for operator convenience
		$sit = new Sites;
		$lib = $sit->getOne($_SESSION['current_site']);
		$mbr[siteid] = $lib[siteid];
		$mbr[city] = $lib[city];
		$mbr[state] = $lib[state];
		$mbr[zip] = $lib[zip];
	
		$mbrtypes = new MemberTypes;
		$mbr[classification] = $mbrtypes->getDefault();
	
		include(REL(__FILE__, "../circ/mbrFields.php"));
	?>
	
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<!-- ------------------------------------------------------------------------ -->

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));

	include_once(REL(__FILE__,'./mbrNewJs.php'));
?>	
