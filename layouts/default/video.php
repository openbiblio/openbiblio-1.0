<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../classes/Lay.php'));

class Layout_video {
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
			$lay->container('Lines', array('margin-top'=>'0.5in', 'margin-bottom'=>'0.5in', 'margin-left'=>'0.125in', 'margin-right'=>'0.125in'));
				$lay->container('Columns');
					list(, $skip) = $this->p->getFirst('skip');
					for ($i = 0; $i < $skip; $i++) {
						$lay->container('Column', array(
							'height'=>'1in', 'width'=>'2.625in',
						));
						$lay->close();
					}
					while ($row = $rpt->each()) {
						$lay->container('Column', array(
							'height'=>'1in', 'width'=>'2.625in',
							'y-align'=>'center',
						));
							$lay->container('TextLine', array('x-align'=>'center'));
								$lay->pushFont('Times-Roman', 36);
									$lay->text(strtoupper($row['barcode_nmbr']));
								$lay->popFont();
							$lay->close();
						$lay->close();
						$lay->container('Column', array(
							'height'=>'1in', 'width'=>'2.625in',
							'margin-top'=>'0.125in', 'margin-bottom'=>'0.125in',
							'margin-left'=>'0.125in', 'margin-right'=>'0.125in',
						));
							$lay->pushFont('Times-Roman', 14);
								$lay->container('TextLines');
									$lay->text($row['title']);
								$lay->close();
								$lay->container('Column', array('y-align'=>'bottom'));
									$lay->container('TextLine', array('x-align'=>'right'));
										$lay->text('Length: 118 min');
									$lay->close();
								$lay->close();
							$lay->popFont();
						$lay->close();
					}
				$lay->close();
			$lay->close();
		$lay->close();
	}
}
