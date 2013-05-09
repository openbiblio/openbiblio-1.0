<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../../classes/Report.php"));
require_once(REL(__FILE__, "../../classes/Query.php"));
require_once(REL(__FILE__, "../../classes/Search.php"));
require_once(REL(__FILE__, "../../classes/BiblioRows.php"));

class BiblioSearch_rpt extends BiblioRows {
	var $params = NULL;
	var $searchTypes;
	var $q;
	var $countSQL = NULL;
	var $sliceSQL = NULL;

	function BiblioSearch_rpt() {
		$this->searchTypes = array(
			'keyword' => Search::type('Keyword', 'MARC', array()),
			'callno' => Search::type('Call No.', 'MARC', array('099$a')),
			'author' => Search::type('Author', 'MARC', array('100$a', '245$c', '700$a', '110$a')),
			'title' => Search::type('Title', 'MARC', array('240$a', '245$a', '245$b')),
			'subject' => Search::type('Subject', 'MARC', array('650$a', '651$a', '505$a', '502$a')),
			'publisher' => Search::type('Publisher', 'MARC', array('260$b')),
			'address' => Search::type('Address', 'MARC', array('260$a')),
			'date' => Search::type('Date', 'MARC', array('240$f', '130$f', '260$c')),
			//'series' => Search::type('Series', 'MARC', array('440$a', '490$a')),
			//'pub_date_from' => Search::type('Published After', 'MARC', array('260$c'), 'numeric', '>='),
			//'pub_date_to' => Search::type('Published Before', 'MARC', array('260$c'), 'numeric', '<='),
			'audience_level' => Search::type('Grade Level', 'MARC', array('521$a'), 'phrase'),
			'media_type' => Search::type('Media Type', 'material_type_dm', array('description'), 'string', 'like'),
			'collection' => Search::type('Collection', 'collection_dm', array('description'), 'string', 'like'),
			'barcode' => Search::type('Barcode', 'biblio_copy', array('barcode_nmbr'), 'phrase', 'like', 'start'),
		);
	}
	function title() { return "Item Search"; }
	function category() { return "Cataloging"; }
	function layouts() { return array(array('title'=>'MARC Export', 'name'=>'marc')); }
	function paramDefs() {
		$p = array(
			array('order_by', 'order_by', array(), array(
				array('callno', array('title'=>'Call No.')),
				array('title', array('title'=>'Title')),
				array('author', array('title'=>'Author')),
				array('date', array('title'=>'Date')),
				array('length', array('title'=>'Length')),
			)),
		);
		return array_merge(Search::getParamDefs($this->searchTypes), $p);
	}
	function select($params) {
		$this->params = $params;
		$this->q = new Query();

		$query = $this->_doQuery();

		list( , $order_by, $raw) = $this->params->getFirst('order_by');
		$sortq = $this->getOrderSql('b.bibid', $raw);
		$sql = "select distinct b.bibid "
					 . $query['from'] . $sortq['from']
					 . $query['where'] . $sortq['order by'];
		//echo "sql===>$sql<br /><br />";					 
		return new BiblioRowsIter($this->q->select($sql));
	}
	function _tmpQuery($from, $to, $query) {
		$this->q->act($this->q->mkSQL('delete from %I', $to));
		$sql = $this->q->mkSQL('insert into %I select distinct b.bibid ', $to);
		array_unshift($query['from'], $this->q->mkSQL('%I as b ', $from));
		$sql .= 'from '.implode(', ', $query['from']).' ';
		$sql .= $query['where'];
		$this->q->act($sql);
		$row = $this->q->select1($this->q->mkSQL('select count(*) as rowcount from %I', $to));
		return $row['rowcount'];
	}
	// This function uses temporary tables to get around MySQL's join limitations,
	// It returns a query that will retrieve the results from the final temporary table.
	function _doQuery() {
		global $tab;	# FIXME - This is TERRIBLE

		$queries = array();
		$q = array('from'=>array(), 'where'=>'where ');
		if ($tab == 'opac') {
			$q['where'] .= "b.opac_flg='Y' ";
		} else {
			$q['where'] .= "1=1 ";
		}
		$i = 0;
		foreach (Search::getTerms($this->searchTypes, $this->params->getList('terms')) as $t) {
			$i++;
			if (count($q['from']) >= 6) {	// arbitrary
				array_push($queries, $q);
				$q = array('from' => array(), 'where' => 'where 1=1 ');
			}

			list($typename, $text, $exact) = $t;
			if (!array_key_exists($typename, $this->searchTypes)) {
				continue;
			}
			$type = $this->searchTypes[$typename];
			switch ($type['within']) {
			case 'MARC':
				array_push($q['from'], 'biblio_field as term'.$i.'f', 'biblio_subfield as term'.$i.'s');
				$q['where'] .= 'and term'.$i.'f.bibid=b.bibid and term'.$i.'s.fieldid=term'.$i.'f.fieldid ';
				if (!empty($type['fields'])) {
					$q['where'] .= "and (";
					$op = "";
					foreach ($type['fields'] as $f) {
						$l = explode('$', $f);
						if (isset($l[0])) {
							$q['where'] .= $this->q->mkSQL($op."term".$i."f.tag=%N ", $l[0]);
							if (isset($l[1])) {
								$q['where'] .= $this->q->mkSQL("and term".$i."s.subfield_cd=%Q ", $l[1]);
							}
						}
						$op = "or ";
					}
					$q['where'] .= ") ";
				}
				$column = 'term'.$i.'s.subfield_data';
				break;
			case 'collection_dm':
				array_push($q['from'], 'collection_dm as term'.$i.'coll');
				$q['where'] .= 'and b.collection_cd=term'.$i.'coll.code ';
				$column = 'term'.$i.'coll.'.$type['fields'][0];
				break;
			case 'material_type_dm':
				array_push($q['from'], 'material_type_dm as term'.$i.'matl');
				$q['where'] .= 'and b.material_cd=term'.$i.'matl.code ';
				$column = 'term'.$i.'matl.'.$type['fields'][0];
				break;
			case 'biblio':
				// XXX only supports one field
				array_push($q['from'], 'biblio as term'.$i.'b');
				$q['where'] .= 'and b.bibid=term'.$i.'b.bibid ';
				$column = 'term'.$i.'b.'.$type['fields'][0];
				break;
			case 'biblio_copy':
				// XXX only supports one field
				array_push($q['from'], 'biblio_copy as term'.$i.'copy');
				$q['where'] .= 'and b.bibid=term'.$i.'copy.bibid ';
				$column = 'term'.$i.'copy.'.$type['fields'][0];
				break;
			}
			if ($type['method'] == numeric) {
				$verb='%N';
			} else {
				$verb='%Q';
			}
			## if user specifies exact=Yes,  do that, 
			## else use what is specified in the searchTypes array at top of this page
			if ($exact) {
				$operator = '=';
			} else {
				$operator = $type['operator'];
			}
			if ($operator == 'like') {
				switch($type['where']){
				case 'anywhere':
					$text = '%'.$text.'%';
					break;
				case 'start':
					$text = $text.'%';
					break;
				case 'end':
					$text = '%'.$text;
					break;
				}
			}
			$q['where'] .= $this->q->mkSQL("and %C $operator $verb ", $column, $text);
		}
		array_push($queries, $q);
		if (count($queries) == 1) {	// Don't use temporaries, just execute the query.
			$q = $queries[0];
			array_unshift($q['from'], 'biblio as b');
			return array('from'=>'from '.implode(' join ', $q['from']).' ', 'where'=>$q['where']);
		}
		foreach (array('tmp0', 'tmp1') as $t) {
			$this->q->act($this->q->mkSQL('drop temporary table if exists %I', $t));
			$this->q->act($this->q->mkSQL('create temporary table %I (bibid integer)', $t));
		}
		// these get switched before they're used
		$to = 'biblio';
		$from = 'tmp0';
		foreach ($queries as $q) {
			$t = $from;
			$from = $to;
			$to = $t;
			if ($to == 'biblio') {
				$to = 'tmp1';
			}
			$r = $this->_tmpQuery($from, $to, $q);
			if ($r == 0) {
				break;
			}
		}
		return array('from'=>$this->q->mkSQL('from %I as b ', $to), 'where'=>'where 1=1 ');
	}
}
