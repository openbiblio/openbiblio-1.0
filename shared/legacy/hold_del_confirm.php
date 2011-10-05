<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	#****************************************************************************
	#*  Checking for get vars.
	#****************************************************************************
	$bibid = $_GET["bibid"];
	$holdid = $_GET["holdid"];
	if (isset($_GET["mbrid"])) {
		$mbrid = $_GET["mbrid"];
		$tab = "circulation";
		$nav = "view";
		$returnUrl = "../circ/mbr_view.php?mbrid=".$mbrid;
	} else {
		$mbrid = "";
		$tab = "catalog";
		$nav = "holds";
		$returnUrl = "../catalog/biblio_hold_list.php?bibid=".$bibid;
	}

	$restrictInDemo = TRUE;
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	#**************************************************************************
	#*  Show confirm page
	#**************************************************************************
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delbiblioform" method="post" action="<?php echo $returnUrl;?>">
<?php echo T("holdDelConfirmMsg"); ?>
<br /><br />
			<input type="button" onclick="parent.location='../shared/hold_del.php?bibid=<?php echo $bibid;?>&holdid=<?php echo $holdid;?>&mbrid=<?php echo $mbrid;?>'" value="<?php echo T("Delete"); ?>" class="button" />
			<input type="submit" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>

<?php

	 ;
