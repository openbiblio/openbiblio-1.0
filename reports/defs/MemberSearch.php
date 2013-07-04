<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../../classes/Query.php"));
require_once(REL(__FILE__, "../../classes/Search.php"));

class MemberSearch_rpt {
	var $searchTypes;

	function MemberSearch_rpt() {
		$this->searchTypes = array(
			'keyword' => Search::type('Keyword', 'ms', array('m.first_name', 'm.last_name', 's.name', 'm.barcode_nmbr')),
			'barcode' => Search::type('Barcode', 'ms', array('m.barcode_nmbr'), 'phrase'),
			'name' => Search::type('Name', 'ms', array('m.last_name', 'm.first_name')),
			'site' => Search::type('Site', 'ms', array('s.name')),
		);
	}
	function title() { return "Member Search"; }
	function category() { return "Member Reports"; }
	function layouts() { return array(array('title'=>'Print List', 'name'=>'member_list')); }
	function paramDefs() {
		$p = array(
			array('order_by', 'order_by', array(), array(
				array('name', array('title'=>'Name', 'expr'=>"concat(m.last_name, ', ', m.first_name)")),
				array('site_name', array('title'=>'Site', 'expr'=>'s.name')),
				array('barcode_nmbr', array('title'=>'Number')),
			)),
		);
		return array_merge(Search::getParamDefs($this->searchTypes), $p);
	}
	function columns() {
		return array(
			array('name'=>'mbrid', 'hidden'=>true, 'checkbox'=>true),
			array('name'=>'last_name', 'hidden'=>true),
			array('name'=>'first_name', 'hidden'=>true),
			array('name'=>'school_grade', 'hidden'=>true),
			array('name'=>'siteid', 'hidden'=>true),
			array('name'=>'barcode_nmbr', 'title'=>'Number', 'align'=>'center', 'sort'=>'barcode_nmbr'),
			array('name'=>'name', 'title'=>'Name', 'func'=>'member_link', 'sort'=>'name'),
			array('name'=>'site_name', 'title'=>'Site', 'sort'=>'site_name'),
		);
	}
	function select($params) {
		$q = new Query();
		$sql = 'select m.mbrid, m.barcode_nmbr, m.last_name, '
					 . "m.first_name, concat(m.last_name, ', ', m.first_name) as name, "
					 . 'm.school_grade, m.siteid, s.name as site_name '
					 . 'from member m, site s where s.siteid=m.siteid ';
		foreach (Search::getTerms($this->searchTypes, $params->getList('terms')) as $t) {
			list($typename, $text, $exact) = $t;
			if (!array_key_exists($typename, $this->searchTypes)) {
				continue;
			}
			$type = $this->searchTypes[$typename];
			assert('$type["within"] == "ms"');
			if ($type['method'] == numeric) {
				$verb='%N';
			} else {
				$verb='%Q';
			}
			if ($exact) {
				$operator = '=';
			} else {
				$operator = $type['operator'];
			}
			if ($operator == 'like') {
				$text = '%'.$text.'%';
			}
			$ors = array();
			foreach ($type['fields'] as $col) {
				$ors[] = $q->mkSQL("%C %! $verb", $col, $operator, $text);
			}
			$sql .= 'and ('.implode(' or ', $ors).') ';
		}

		list( , $order_by, $raw) = $params->getFirst('order_by');
		if ($order_by) {
			$sql .= 'order by '.$order_by.' ';
		}
		
		return $q->select($sql);
	}
}
