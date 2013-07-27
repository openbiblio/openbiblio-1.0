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
	require_once(REL(__FILE__, "../classes/Params.php"));

	if (isset($_SESSION['postVars']['type'])) {
		$type = $_SESSION['postVars']['type'];
	} elseif (isset($_REQUEST['type'])) {
		$type = $_REQUEST['type'];
	} else {
		header('Location: ../reports/index.php');
		exit(0);
	}

	### Create a new Report Object ###
	$rpt = Report::create($type);
	if (!$rpt) {
		header('Location: ../reports/index.php');
		exit(0);
	}

	### construct web page with criteria form ###
	Nav::node('reports/reportcriteria',T($rpt->title()));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	echo '<h3>'.T($rpt->title()).'</h3>';

	if ($_REQUEST['msg']) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}

	require(REL(__FILE__, "../shared/get_form_vars.php"));
?>

<fieldset>
	<legend>Report Criteria</legend>
	<form name="reportcriteriaform" method="get" action="../reports/run_report.php">
		<input type="hidden" name="title" value="<?php echo H($rpt->title());?>" />
		<input type="hidden" name="type" value="<?php echo H($rpt->type()) ?>" />

<?php
	Params::printForm($rpt->getParamDefs());
?>

		<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
	</form>
</fieldset>

