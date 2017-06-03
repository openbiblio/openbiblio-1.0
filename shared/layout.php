<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Optional outputs for Report sub-system
 * @author Mich Stetson
 */

	require_once("../shared/common.php");

	require_once(REL(__FILE__, '../classes/Report.php'));
	require_once(REL(__FILE__, '../classes/Params.php'));

	if (isset($_REQUEST['tab'])) {
		$tab = $_REQUEST['tab'];
	} else {
		$tab = 'reports';
	}
	if ($tab != 'opac') {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}

	####-------------------------------------------------------------------####
	# Must ask for parameters
	$nav = "layoutparams";
	$focus_form_name = "layoutparamform";
	$focus_form_field = "lay_skip";

//	if ($tab == 'opac') {
//		Page::header_opac(array('nav'=>$nav, 'title'=>''));
//	} else {
//		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
//	}

	require(REL(__FILE__, "../shared/get_form_vars.php"));

	if (isset($_REQUEST['msg'])) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}
	####-------------------------------------------------------------------####

	assert('preg_match("/^[-_A-Za-z0-9]+\$/", $_REQUEST["name"])');
	$filename = '../layouts/'.$_REQUEST["name"].'.php';
	if (!is_readable($filename)) {
		$filename = '../layouts/default/'.$_REQUEST["name"].'.php';
	}
	assert('is_readable($filename)');
	require_once($filename);
	## get reference to class contained in '$filename'
	$classname = 'Layout_'.$_REQUEST["name"];
	assert('class_exists($classname)');

//	$rpt = Report::load($_REQUEST['rpt']);
	$rpt = Report::load($_REQUEST['rpt'],1,9999);
	assert('$rpt != NULL');

	// Rendering a large layout can take a while.
	set_time_limit(90);

	$l = new $classname;
	if (method_exists($l, 'paramDefs')) {
		$defs = $l->paramDefs();
	} else {
		$defs = array();
	}
	if (empty($defs) or isset($_REQUEST['filled'])) {
		$params = new Params;
		$errs = $params->loadCgi_el($defs, 'lay_');
		if (empty($errs)) {
			if (method_exists($l, 'init')) {
				$l->init($params);
			}
			$l->render($rpt);
//			exit();
		} else {
			list($msg, $fielderrs) = FieldError::listExtract($errs);
			if ($msg) {
				$_REQUEST['msg'] = $msg;
			}
			$_SESSION['postVars'] = mkPostVars();
			$_SESSION['pageErrors'] = $fielderrs;
		}
	}
	####-------------------------------------------------------------------####

?>
<h3><?php echo T("Circulation"); ?></h3>

<form role="form" name="layoutparamform" method="get" action="../shared/layout.php">
<fieldset>
<legend><?php echo T("Packing List"); ?></legend>
<input type="hidden" name="name" value="<?php echo H($_REQUEST["name"]) ?>" />
<input type="hidden" name="rpt" value="<?php echo H($_REQUEST["rpt"]) ?>" />
<input type="hidden" name="tab" value="<?php echo H($tab) ?>" />
<input type="hidden" name="filled" value="<?php echo H('1') ?>" />

<?php
	Params::printForm($defs, 'lay_');
?>

<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
</fieldset>
</form>

