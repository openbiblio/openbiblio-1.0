<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_overdue {
	function render($rpt) {
		$rpt = $rpt->variant(array('order_by'=>'site_member'));

		$lay = new Lay;
			$lay->container('Columns', array(
				'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
				'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
			));
				$oldmbr = NULL;
				while ($row = $rpt->each()) {
					if ($row['mbrid'] != $oldmbr) {
						if ($oldmbr !== NULL) {
							$lay->close();
							$lay->container('Columns', array(
								'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
								'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
							));
						}
						$oldmbr = $row['mbrid'];
						$lay->pushFont('Times', 'B', 16);
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->text('Overdue Notice - '.date('m/d/y'));
							$lay->close();
						$lay->popFont();
						$lay->element('Spacer', array('height'=>9));
						$lay->pushFont('Times', 'B', 12);
							$lay->container('TextLine');
								$lay->text($row['last_name'].', '.$row['first_name']);
							$lay->close();
							$lay->container('TextLine');
								$lay->text($row['site_name']);
							$lay->close();
						$lay->popFont();
						$lay->element('Spacer', array('height'=>9));
						$lay->pushFont('Times', 'I', 12);
							$lay->container('Line');
								$lay->container('TextLine', array('width'=>'1in'));
									$lay->text('Barcode');
								$lay->close();
								$lay->container('TextLine', array('width'=>'5in'));
									$lay->text('Title');
								$lay->close();
								$lay->container('TextLine', array('width'=>'1in'));
									$lay->text('Due Date');
								$lay->close();
							$lay->close();
						$lay->popFont();
					}
					$lay->container('Line');
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text($row['barcode_nmbr']);
						$lay->close();
						$lay->container('TextLine', array('width'=>'5in'));
							$lay->text($row['title']);
						$lay->close();
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text(date('m/d/y', strtotime($row['due_dt'])));
						$lay->close();
					$lay->close();
				}
			$lay->close();
		$lay->close();
	}
}
