<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
//require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/ReportDisplays.php"));
//require_once(REL(__FILE__, "../classes/Report.php"));
//require_once(REL(__FILE__, "../classes/TableDisplay.php"));
//require_once(REL(__FILE__, "../classes/ReportDisplay.php"));

class ReportDisplaysUI {
	static function display($page) {
		$rptdisplays = new ReportDisplays;
		$displays = $rptdisplays->getMatches(array('page'=>$page), 'position');
		while ($disp = $displays->next()) {
			$rpt = Report::create($disp['report']);
			$rpt->init($disp['params']);
			$rpt->count();	# Make sure we have an iter.  FIXME - This is an ugly hack
			$iter = $rpt->iter;
			if ($disp['max_rows'] > 0)
				$iter = new SliceIter(0, $disp['max_rows'], $iter);
			$t = new TableDisplay;
			$rdisp = new ReportDisplay($rpt);
			$t->title = $disp['title'];
			$t->columns = $rdisp->columns();
			echo $t->begin();
			while ($r = $iter->fetch_assoc()) {
				echo $t->rowArray($rdisp->row($r));
			}
			echo $t->end();
		}
	}
}
