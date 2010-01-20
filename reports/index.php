<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "reports";
$nav = "reportlist";

include(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../classes/Report.php"));
define("REPORT_DEFS_DIR","../reports/defs");

#****************************************************************************
#*  Read report definitions
#****************************************************************************
$reports = array();
$errors = array();

if ($handle = opendir(REPORT_DEFS_DIR)) {
	while (($file = readdir($handle)) !== false) {
		if (preg_match('/^([^._][^.]*)\\.(rpt|php)$/', $file, $m)) {
			list($rpt, $err) = Report::create_e($m[1]);
			if (!$err) {
				if (!isset($reports[$rpt->category()])) {
					$reports[$rpt->category()] = array();
				}
				$reports[$rpt->category()][$rpt->type()] = T($rpt->title());
			} else {
				$errors[] = $err;
			}
		}
	}
	closedir($handle);
}

ksort($reports);
foreach (array_keys($reports) as $k) {
	asort($reports[$k]);
}

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3><?php echo T("Report List");?></h3>
<fieldset>
<legend><?php echo T("reportsDesc");?></legend>
<ul>

<?php
foreach (array_keys($reports) as $category) {
	echo '<li class="report_category"><strong>'.T($category).'</strong><ul>';
	foreach ($reports[$category] as $type => $title) {
		echo '<li><a href="../reports/report_criteria.php?type='.HURL($type).'">'.H($title).'</a></li>';
	}
	echo '</ul></li>';
}
if ($errors) {
	echo '<li class="report_category"><strong>'.T("Report Errors").'</strong><ul>';
	foreach ($errors as $e) {
		echo '<li>'.H($e->toStr()).'</li>';
	}
	echo '</ul></li>';
}
?>
</ul>
</fieldset>

<?php
Page::footer();
