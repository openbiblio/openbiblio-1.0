<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_pull_list {
	function render($rpt) {
//		$rpt = $rpt->variant(array('order_by'=>'item_num'));
		$rpt = $rpt->getVariant(array('order_by'=>'item_num'));

		$lay = new Lay;
			$lay->container('Columns', array(
				'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
				'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
			));
				$lay->pushFont('Times', 'B', 16);
					$lay->container('TextLine', array('x-align'=>'center'));
						$lay->text('Instructional Media Center');
					$lay->close();
				$lay->popFont();
				$lay->pushFont('Times', 'B', 12);
					$lay->container('TextLine', array('x-align'=>'center'));
						$lay->text('Pull List - '.date('m/d/y'));
					$lay->close();
				$lay->popFont();
				$lay->element('Spacer', array('height'=>9));
				$lay->pushFont('Times', 'I', 14);
					$lay->container('Line');
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text('Item');
						$lay->close();
						$lay->container('TextLine', array('width'=>'4.5in'));
							$lay->text('Title');
						$lay->close();
						$lay->container('TextLine', array('width'=>'2in'));
							$lay->text('Members');
						$lay->close();
					$lay->close();
				$lay->popFont();
				# FIXME - The justification works around a layout bug that would make
				# one single-spaced line appear at the bottom of each page.
				$lay->container('Columns', array('y-spacing'=>14, 'y-align'=>'justify'));
					$lay->pushFont('Times-Roman', 14);
						while ($row = $rpt->each()) {
							$lay->container('Line');
								$lay->container('TextLine', array('width'=>'1in'));
									$lay->text($row['item_num']);
								$lay->close();
								$lay->container('TextLine', array('width'=>'4.5in'));
									$lay->text(substr($row['title'], 0, 35));
								$lay->close();
								$lay->container('TextLine', array('width'=>'2in'));
									$lay->text($row['members'][0]['last_name'].', '.$row['members'][0]['first_name']);
								$lay->close();
							$lay->close();
						}
					$lay->popFont();
				$lay->close();
			$lay->close();
		$lay->close();
	}
}
