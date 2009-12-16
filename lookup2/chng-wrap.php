<?php
##### wrapper for biblio_change.php to allow it to stand alone if need be

	require_once("../shared/common.php");
	
#### this block is from biblio_change.php ####
require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../classes/Marc.php"));

#****************************************************************************
#*  Checking for post vars.  Go back to search if none found.
#****************************************************************************
if (count($_POST) == 0) {
	if ($nav == "newconfirm") {
		header("Location: ../catalog/biblio_new_form.php");
	} else {
		header("Location: ../catalog/index.php");
	}
	exit();
}
###############################################

	$tab = "cataloging";
	$nav = "newconfirm";
	
	require_once(REL(__FILE__,'../catalog/biblio_change.php'));
	
#### changed to eliminate an editing loop. Now goes directly to the new copy entry form - Fred
//header("Location: ../catalog/biblio_edit_form.php?bibid=".$bibid."&msg=".U($msg));
header("Location: ../catalog/biblio_copy_new_form.php?resey=Y&bibid=".$bibid."&msg=".U($msg));
exit();
?>
