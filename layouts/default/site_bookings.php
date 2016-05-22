<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_site_bookings {
	function render($rpt) {
		$rpt = $rpt->variant(array('order_by'=>'site_out_title'));
		$lay = new Lay;
			$lay->container('Columns', array(
				'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
				'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
			));
				$oldsite = NULL;
				while ($row = $rpt->each()) {
					if ($row['siteid'] != $oldsite) {
						if ($oldsite !== NULL) {
							$lay->close();
							$lay->element('Spacer', array('height'=>9));
						}
						$oldsite = $row['siteid'];
						$lay->container('Paragraph');
							$lay->container('Column');
								$lay->pushFont('Times', 'B', 14);
									$lay->container('TextLine');
										$lay->text($row['site_name']);
									$lay->close();
								$lay->popFont();
								$lay->pushFont('Times-Italic', 12);
									$lay->container('Line', array('margin-left'=>'0.5in'));
										$lay->container('TextLine', array('width'=>'1in', 'underline'=>1));
											$lay->text('Out Date');
										$lay->close();
										$lay->container('TextLine', array('width'=>'3in', 'underline'=>1));
											$lay->text('Title');
										$lay->close();
										$lay->container('TextLine', array('width'=>'1in', 'underline'=>1));
											$lay->text('Barcode');
										$lay->close();
										$lay->container('TextLine', array('width'=>'2in', 'underline'=>1));
											$lay->text('Member');
										$lay->close();
									$lay->close();
								$lay->popFont();
							$lay->close();
						}
						$lay->container('Line', array('margin-left'=>'0.5in'));
							$lay->container('TextLine', array('width'=>'1in'));
								$lay->text($row['outd']);
							$lay->close();
							$lay->container('TextLine', array('width'=>'3in'));
								if (strlen($row['title']) > 38) {
									$row['title'] = substr($row['title'], 0, 36)."...";
								}
								$lay->text($row['title']);
							$lay->close();
							$lay->container('TextLine', array('width'=>'1in'));
								$lay->text($row['item_num']);
							$lay->close();
							$lay->container('TextLine', array('width'=>'2in'));
								$lay->text($row['last_name'].', '.$row['first_name']);
							$lay->close();
						$lay->close();
					}
				$lay->close(); # Paragraph in the if ($row['siteid'] != $oldsite) above
			$lay->close();
		$lay->close();
	}
}
