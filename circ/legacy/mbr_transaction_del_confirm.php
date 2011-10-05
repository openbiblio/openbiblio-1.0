<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "account";
	$restrictInDemo = true;
	require_once(REL(__FILE__, "../shared/logincheck.php"));


	#****************************************************************************
	#*  Retrieving get var
	#****************************************************************************
	$mbrid = $_GET["mbrid"];
	$transid = $_GET["transid"];

	#**************************************************************************
	#*  Show confirm page
	#**************************************************************************
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<center>
<form name="delbiblioform" method="post" action="../circ/mbr_account.php?mbrid=<?php echo $mbrid;?>">
<?php echo T("Really delete transaction?"); ?>
<br /><br />
			<input type="button" onlick="parent.location='../circ/mbr_transaction_del.php?mbrid=<?php echo $mbrid;?>&transid=<?php echo $transid;?>'" value="<?php echo T("Delete"); ?>" class="button" />
			<input type="submit" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>
