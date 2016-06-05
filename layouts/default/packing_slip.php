<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_packing_slip {
	var $p;
	function paramDefs() {
		return array(
			array('string', 'skip', array('title'=>'Skip Labels', 'default'=>'0')),
		);
	}
	function init($params) {
		$this->p = $params;
	}
	function render($rpt) {
		$lay = new Lay;
			$lay->container('Lines');
				$lay->container('Columns', array('width'=>'50%'));
					list(, $skip) = $this->p->getFirst('skip');
					for ($i = 0; $i < $skip; $i++) {
						$lay->container('Column', array('height'=>'2.75in',));
						$lay->close();
					}
					while ($row = $rpt->each()) {
						for ($i=0; $i<$row['pieces']; $i++) {
							$lay->container('Column', array('margin-left'=>'0.25in',
								'margin-right'=>'0.25in', 'margin-top'=>'0.25in',
								'margin-bottom'=>'0.25in', 'height'=>'2.75in',
							));
								$lay->pushFont('Times', 'B', 10);
									if ($row['pieces'] > 1) {
										$lay->container('Line');
											$lay->container('TextLine', array('x-align'=>'left'));
												$lay->text('Media Center');
											$lay->close();
											$lay->container('TextLine', array('x-align'=>'right'));
												$lay->text(($i+1).' of '.$row['pieces']);
											$lay->close();
										$lay->close();
									} else {
										$lay->container('TextLine', array('x-align'=>'center'));
											$lay->text('Media Center');
										$lay->close();
									}
								$lay->popFont();
								$lay->element('Spacer', array('height'=>9));
								$lay->pushFont('Times', '', 12);
									$lay->container('TextLine');
										$lay->text('To: '.strtoupper($row['members'][0]['site_name']));
									$lay->close();
									$lay->container('Line', array('y-align'=>'top', 'x-spacing'=>6));
										$lay->container('TextLine');
											$lay->text('Teacher Name:');
										$lay->close();
										$lay->container('Column');
											foreach (array_slice($row['members'], 0, 4) as $m) {
												$lay->container('TextLine');
													$lay->text($m['first_name'].' '.$m['last_name'].' ('.$m['barcode_nmbr'].')');
												$lay->close();
											}
										$lay->close();
									$lay->close();
									$lay->element('Spacer', array('height'=>9));
									$lay->container('TextLine');
										$title = substr($row['title'], 0, 27);	# Try to keep title from overflowing the slip
										$lay->text('Item: ('.$row['item_num'].') '.$title);
									$lay->close();
									$lay->element('Spacer', array('height'=>9));
									$lay->container('TextLine');
										$lay->text('Note:');
									$lay->close();
									$lay->container('Column', array('y-align'=>'bottom'));
										$lay->container('Line');
											$lay->container('TextLine');
												$lay->text('Deliver On: '.$row['book_dt']);
											$lay->close();
											$lay->container('TextLine', array('x-align'=>'right'));
												$lay->text('Return By: ');
												$lay->pushFont('Times', 'B', 12);
													$lay->text($row['due_dt']);
												$lay->popFont();
											$lay->close();
										$lay->close();
									$lay->close();
								$lay->popFont();
							$lay->close();
						}
					}
				$lay->close();
			$lay->close();
		$lay->close();
	}
}
