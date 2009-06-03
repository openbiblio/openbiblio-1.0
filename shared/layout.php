<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
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

  assert('ereg("^[-_A-Za-z0-9]+\$", $_REQUEST["name"])');
  $filename = '../layouts/'.$_REQUEST["name"].'.php';
  if (!is_readable($filename)) {
    $filename = '../layouts/default/'.$_REQUEST["name"].'.php';
  }
  assert('is_readable($filename)');
  $classname = 'Layout_'.$_REQUEST["name"];

  require_once($filename);

  assert('class_exists($classname)');

  $rpt = Report::load($_REQUEST['rpt']);
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
      exit();
    } else {
      list($msg, $fielderrs) = FieldError::listExtract($errs);
      if ($msg) {
        $_REQUEST['msg'] = $msg;
      }
      $_SESSION['postVars'] = mkPostVars();
      $_SESSION['pageErrors'] = $fielderrs;
    }
  }

  # Must ask for parameters
  $nav = "layoutparams";
  $focus_form_name = "layoutparamform";

  if ($tab == 'opac') {
    Page::header_opac(array('nav'=>$nav, 'title'=>''));
  } else {
    Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
  }

  require(REL(__FILE__, "../shared/get_form_vars.php"));

  if (isset($_REQUEST['msg'])) {
    echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
  }
?>

<form name="layoutparamform" method="get" action="../shared/layout.php">
<input type="hidden" name="name" value="<?php echo H($_REQUEST["name"]) ?>" />
<input type="hidden" name="rpt" value="<?php echo H($_REQUEST["rpt"]) ?>" />
<input type="hidden" name="tab" value="<?php echo H($tab) ?>" />
<input type="hidden" name="filled" value="<?php echo H('1') ?>" />

<?php
  Params::printForm($defs, 'lay_');
?>

<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
</form>

<?php

  Page::footer();