<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../classes/Links.php'));

global $_marcdisplay_functions;
$_marcdisplay_functions = array(
	'100$a' => array('MarcFuncs', 'author_link'),
	'260$b' => array('MarcFuncs', 'publisher_link'),
	'440$a' => array('MarcFuncs', 'series_link'),
	'650$a' => array('MarcFuncs', 'subject_link'),
	'856$u' => array('MarcFuncs', 'url'),
);

class MarcDisplay {
	function MarcDisplay($field, $biblio) {
		$this->field = $field;
		$this->spec = $this->field['tag'].'$'.$this->field['subfield_cd'];
		$this->biblio = $biblio;
	}
	function title() {
		if (strlen($this->field['label'])) {
			return T($this->field['label']);
		} else {
			return $LOC->getMarc($this->spec);
		}
	}
	function htmlValues() {
		global $_marcdisplay_functions;
		$vals = $this->biblio['marc']->getValues($this->spec);
		if(empty($vals)) {
			return '';
		}
		if (isset($_marcdisplay_functions[$this->spec])) {
			$cb = $_marcdisplay_functions[$this->spec];
		} else {
			$cb = array('MarcFuncs', 'nl2br');
		}
		$l = array();
		foreach ($vals as $v) {
			$l[] = call_user_func($cb, $this->biblio, $v);
		}
		return implode('<br/>', $l);
	}
}

class MarcFuncs {
	function nl2br($biblio, $val) {
		return nl2br(H($val));
	}
	function author_link($biblio, $val) {
		return Links::mkLink('author', $val, H($val));
	}
	function publisher_link($biblio, $val) {
		return Links::mkLink('publisher', $val, H($val));
	}
	function series_link($biblio, $val) {
		return Links::mkLink('series', $val, H($val));
	}
	function subject_link($biblio, $val) {
		return Links::mkLink('subject', $val, H($val));
	}
	function url($biblio, $val) {
		return '<a href="'.H($val).'">'.H($val).'</a>';
	}
}
