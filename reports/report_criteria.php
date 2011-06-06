<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "reports";
	$nav = "reportcriteria";
	$focus_form_name = "reportcriteriaform";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));

	if (isset($_SESSION['postVars']['type'])) {
		$type = $_SESSION['postVars']['type'];
	} elseif (isset($_REQUEST['type'])) {
		$type = $_REQUEST['type'];
	} else {
		header('Location: ../reports/index.php');
		exit(0);
	}

	$rpt = Report::create($type);
	if (!$rpt) {
		header('Location: ../reports/index.php');
		exit(0);
	}

	Nav::node('reports/reportcriteria',T("Report Criteria"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	#****************************************************************************
	#*  getting form vars
	#****************************************************************************
	require(REL(__FILE__, "../shared/get_form_vars.php"));

	echo '<h3>'.T($rpt->title()).'</h3>';

	if ($_REQUEST['msg']) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}
?>

<form name="reportcriteriaform" method="get" action="../reports/run_report.php">
<fieldset>
<input type="hidden" name="type" value="<?php echo H($rpt->type()) ?>" />

<?php
	Params::printForm($rpt->paramDefs());
?>

<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
</fieldset>
</form>
