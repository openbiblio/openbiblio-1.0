<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$restrictToMbrAuth = TRUE;
	$nav = "mbr/delete";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Members.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/Holds.php"));


	$mbrid = $_GET["mbrid"];

	#****************************************************************************
	#*  Getting member name
	#****************************************************************************
	$members = new Members;
	$mbr = $members->getOne($mbrid);
	$mbrName = $mbr['first_name']." ".$mbr['last_name'];

	$copies = new Copies;
	$checkouts = $copies->getMemberCheckouts($mbrid);
	$checkoutCount = $checkouts->count();

	#****************************************************************************
	#*  Getting hold request count
	#****************************************************************************
	$holds = new Holds();
	$all_holds = $holds->getMatches(array('mbrid'=>$mbrid));
	$holdCount = $all_holds->count();

	#**************************************************************************
	#*  Show confirm page
	#**************************************************************************
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	if (($checkoutCount > 0) or ($holdCount > 0)) {
?>
<center>
	<?php echo T('mbrDelConfirmWarn', array('name'=>$mbrName, 'checkoutCount'=>$checkoutCount, 'holdCount'=>$holdCount)); ?>
	<br /><br />
	<a href="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>&reset=Y"><?php echo T("Return to member information"); ?></a>
</center>

<?php
	} else {
?>
<center>
<form name="delbiblioform" method="post" action="../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>&reset=Y">
<?php echo T('mbrDelConfirmMsg', array('name'=>$mbrName)); ?>
<br /><br />
			<input type="button" onclick="parent.location='../circ/mbr_del.php?mbrid=<?php echo $mbrid;?>&name=<?php echo urlencode($mbrName);?>'" value="<?php echo T("Delete"); ?>" class="button" />
			<input type="submit" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>
<?php
	}
	Page::footer();
