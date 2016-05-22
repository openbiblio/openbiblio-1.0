<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));
require_once(REL(__FILE__, '../../model/Biblios.php'));
require_once(REL(__FILE__, '../../model/MediaTypes.php'));

class Layout_catalog {
	var $p;
	function paramDefs() {
		return array(
			array('string', 'title', array('title'=>'Title', 'default'=>'')),
		);
	}
	function init($params) {
		$this->p = $params;
	}
	function render($rpt) {
		$mattypes = new MediaTypes;
		$materialTypeDm = $mattypes->getSelect();
		$biblios = new Biblios();
		$lay = new Lay;

		$lay->container('Columns', array(
			'margin-top'=>'0.5in',
			'margin-bottom'=>'0.5in',
			'margin-left'=>'0.5in',
			'margin-right'=>'0.5in',
		));
		list( , $title) = $this->p->getFirst('title');
		if ($title) {
			$lay->pushFont('Times', 'B', 16);
				$lay->container('TextLine', array(
					'margin-bottom'=>'0.125in',
					'underline'=>true,
					'x-align'=>'center',
				));
					$lay->text($title);
				$lay->close();
			$lay->popFont();
		}
		$lay->container('Lines', array(
			'x-spacing'=>14,
			'x-align'=>'strict-justify',
		));
		$lay->container('Columns', array(
			'y-spacing'=>14,
			'y-align'=>'justify',
			'width'=>'47%'
		));
		while ($row = $rpt->each()) {
			$biblio = $biblios->getOne($row['bibid']);
			$rec = array();
			$rec['title'] = $biblio['marc']->getValue('245$a')
							 . ' ' . $biblio['marc']->getValue('245$b');
			$rec['type'] = $materialTypeDm[$biblio['material_cd']];
			$rec['callno'] = $biblio['marc']->getValue('099$a');
			$rec['length'] = $biblio['marc']->getValue('300$a');
			$rec['publisher'] = $biblio['marc']->getValue('260$b');
			$rec['year'] = $biblio['marc']->getValue('260$c');
			$rec['audience'] = $biblio['marc']->getValue('521$a');
			$rec['series'] = $biblio['marc']->getValue('440$a');
			$rec['summary'] = $biblio['marc']->getValue('520$a');
			$rec['contents'] = $biblio['marc']->getValue('505$a');
			$rec['subjects'] = implode('; ',
													array_merge($biblio['marc']->getValues('650$a'),
																			$biblio['marc']->getValues('651$a')));

			$lay->container('Columns');
				$lay->container('Column');
					$lay->pushFont('Times', 'B', 14);
						$lay->container('TextLines');
							$lay->text($rec['title']);
						$lay->close();
					$lay->popFont();
					$lay->pushFont('Times', 'B', 10);
						foreach (array(array('type', 'callno', 'audience'), array('', 'length', 'year')) as $a) {
							$lay->container('Line', array('x-align'=>'strict-justify'));
								foreach ($a as $f) {
									$lay->container('TextLine', array('width'=>72, 'x-align'=>'center'));
										$lay->text($rec[$f]);
									$lay->close();
								}
							$lay->close();
						}
					$lay->popFont();
				$lay->close();
				$whole_lines = array(
					array('publisher', 'Publisher'),
					array('series', 'Series'),
					array('summary', 'Summary'),
					array('contents', 'Contents'),
					array('subjects', 'Subjects'),
				);
				$lay->pushFont('Times', '', 10);
					foreach ($whole_lines as $l) {
						if (!$rec[$l[0]]) {
							continue;
						}
						$lay->container('Columns', array('margin-left'=>'1em', 'indent'=>'-1em'));
							$first = 1;
							foreach (explode("\n", $rec[$l[0]]) as $line) {
								$lay->container('TextLines', array('x-align'=>'justify'));
									if ($first) {
										$lay->pushFont('Times', 'B', 10);
											$lay->text($l[1].':');
										$lay->popFont();
										$first = 0;
									}
									$lay->text($line);
								$lay->close();
							}
						$lay->close();
					}
				$lay->popFont();
			$lay->close();
		}
		$lay->close();
		$lay->close();
		$lay->close();
		$lay->close();
		return true;
	}
}
