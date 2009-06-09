<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_checkout_history {
	function render($rpt) {
		$lay = new Lay;
			$lay->container('Columns', array(
				'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
				'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
			));
				$lay->pushFont('Times-Bold', 16);
					$lay->container('TextLine', array('x-align'=>'center'));
						$lay->text('Checkout History - '.date('m/d/y'));
					$lay->close();
				$lay->popFont();
				$lay->element('Spacer', array('height'=>9));
				$lay->pushFont('Times-Italic', 12);
					$lay->container('Line');
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text('Booked Date');
						$lay->close();
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text('Due Date');
						$lay->close();
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text('Barcode');
						$lay->close();
						$lay->container('TextLine', array('width'=>'4in'));
							$lay->text('Title');
						$lay->close();
					$lay->close();
				$lay->popFont();
				while ($row = $rpt->each()) {
					$lay->container('Line');
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text(date('m/d/y', strtotime($row['book_dt'])));
						$lay->close();
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text(date('m/d/y', strtotime($row['due_dt'])));
						$lay->close();
						$lay->container('TextLine', array('width'=>'1in'));
							$lay->text($row['item_num']);
						$lay->close();
						$lay->container('TextLine', array('width'=>'4in'));
							$lay->text($row['title']);
						$lay->close();
					$lay->close();
				}
			$lay->close();
		$lay->close();
	}
}
