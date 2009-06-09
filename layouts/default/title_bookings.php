<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_title_bookings {
	function render($rpt) {
		$rpt = $rpt->variant(array('order_by'=>'title'));
		$lay = new Lay;
			$lay->container('Columns', array(
				'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
				'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
			));
				$oldbib = NULL;
				$total = 0;
				$lay->container('Column');
					while ($row = $rpt->each()) {
						if ($row['bibid'] != $oldbib) {
							if ($oldbib !== NULL) {
									$lay->container('Line', array('margin-left'=>'0.5in'));
										$lay->container('TextLine', array('width'=>'1in'));
											$lay->text($total);
										$lay->close();
										$lay->pushFont('Times-Bold', 12);
											$lay->container('TextLine');
												$lay->text('TOTAL');
											$lay->close();
										$lay->popFont();
									$lay->close();
								$lay->close();
								$lay->element('Spacer', array('height'=>9));
								$lay->container('Column');
								$total = 0;
							}
							$oldbib = $row['bibid'];
							$lay->pushFont('Times-Bold', 14);
								$lay->container('TextLine');
									$lay->text($row['title']);
								$lay->close();
							$lay->popFont();
							$lay->pushFont('Times-Bold', 10);
								$lay->container('TextLine');
									$lay->text($row['item_num']);
								$lay->close();
							$lay->popFont();
							$lay->pushFont('Times-Italic', 12);
								$lay->container('Line', array('margin-left'=>'0.5in'));
									$lay->container('TextLine', array('width'=>'1in', 'underline'=>1));
										$lay->text('Usage');
									$lay->close();
									$lay->container('TextLine', array('underline'=>1));
										$lay->text('Site');
									$lay->close();
								$lay->close();
							$lay->popFont();
						}
						$lay->container('Line', array('margin-left'=>'0.5in'));
							$lay->container('TextLine', array('width'=>'1in'));
								$total += $row['usage_count'];
								$lay->text($row['usage_count']);
							$lay->close();
							$lay->container('TextLine');
								$lay->text($row['site_name']);
							$lay->close();
						$lay->close();
					}
				$lay->close();
			$lay->close();
		$lay->close();
	}
}
