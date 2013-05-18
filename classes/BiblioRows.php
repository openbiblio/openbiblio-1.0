<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class BiblioRows {
	var $q;
	function columns() {
		return array(
			array('name'=>'bibid', 'hidden'=>true, 'checkbox'=>true),
			array('name'=>'title_0', 'hidden'=>true),
			array('name'=>'title_a', 'hidden'=>true),
			array('name'=>'title_b', 'hidden'=>true),
			array('name'=>'title_c', 'hidden'=>true),
			array('name'=>'title_d', 'hidden'=>true),
			array('name'=>'author_0', 'hidden'=>true),
			array('name'=>'author_a', 'hidden'=>true),
			array('name'=>'author_b', 'hidden'=>true),
			array('name'=>'date_0', 'hidden'=>true),
			array('name'=>'date_a', 'hidden'=>true),
			array('name'=>'material_cd', 'hidden'=>true),
			array('name'=>'create_dt', 'hidden'=>true),
			array('name'=>'callno', 'title'=>'Call No.', 'sort'=>'callno'),
			array('name'=>'title', 'title'=>'Title', 'func'=>'biblio_link', 'sort'=>'title'),
			array('name'=>'author', 'title'=>'Author', 'sort'=>'author'),
			array('name'=>'date', 'title'=>'Date', 'sort'=>'date'),
			array('name'=>'level', 'title'=>'Grade Level'),
			array('name'=>'pubdate', 'title'=>'Publication Date'),
			array('name'=>'material_type', 'title'=>'Type'),
		);
	}
	function getOrderSql($col, $order_by_raw) {
		/* expr had better not have side effects.
		 * it's substituted into the query more than once
		 */
		$sortFields = array(
			'callno'=>array('field'=>'099$a'),
			'title'=>array('field'=>'240$a',
				'expr'=>'ifnull(substring(sorts.subfield_data, sortf.ind2_cd+1), '
					. 'sorts.subfield_data)'),
			'author'=>array('field'=>'100$a'),
			'date'=>array('field'=>'240$f'),
		);
		$query = array();
		$sort_r = 0;
		if (substr($order_by_raw, -2) == "!r") {
			$sort_r = 1;
			$order_by_raw = substr($order_by_raw, 0, -2);
		}
		$query['from'] = '';
		$query['order by'] = '';
		if (isset($sortFields[$order_by_raw])) {
			$l = explode('$', $sortFields[$order_by_raw]['field']);
			$query['from'] .= 'left join biblio_field as sortf '
				. $this->q->mkSQL('on sortf.bibid=%C ', $col)
				. $this->q->mkSQL('and sortf.tag=%Q ', $l[0]);
			if ($l[1]) {
				$query['from'] .= 'left join biblio_subfield as sorts '
					. 'on sorts.fieldid=sortf.fieldid '
					. $this->q->mkSQL('and sorts.subfield_cd=%Q ', $l[1]);
			}
			if (array_key_exists('expr', $sortFields[$order_by_raw])) {
				$expr = $sortFields[$order_by_raw]['expr'];
			} else {
				$expr = 'sorts.subfield_data ';
			}
			$query['order by'] .= "order by if($expr regexp '^ *[0-9]', "
				. "concat('0', ifnull(floor(log10($expr)), 0), "
				. "$expr), $expr) ";
			if ($sort_r) {
				$query['order by'] .= 'desc ';
			}
		}
		return $query;
	}
}

class BiblioRowsIter extends Iter {
	function BiblioRowsIter($iter) {
		$this->q = new Query;
		$this->iter = $iter;
	}
	function count() {
		return $this->iter->count();
	}
	function skip() {
		$this->iter->skip();
	}
	function next() {
		## this builds the sql to get search item details for later display
		$r = $this->iter->next();
		if ($r === NULL) {
			return $r;
		}
		$marcCols = array(
			'callno' => '099$a',
			'title_0' => '240$a',
			'title_a' => '245$a',
			'title_b' => '245$b',
			'title_c' => '246$a',
			'title_d' => '246$b',
			'author_0' => '100$a',
			'author_a' => '700$a',
			'author_b' => '110$a',
			'date_0' => '130$f',
			'date_a' => '240$f',
			'pubdate' => '260$c',
			'level' => '521$a');
		$sql = "select b.bibid, b.create_dt, b.material_cd, m.description, bf.tag, bs.subfield_cd, bs.subfield_data "
					 . "from biblio b, material_type_dm m, biblio_field bf, biblio_subfield bs "
					 . $this->q->mkSQL("where b.bibid=%N ", $r['bibid'])
					 . "and m.code=b.material_cd "
					 . "and bf.bibid=b.bibid and bs.fieldid=bf.fieldid "
					 . "and ( (1=0) "; // <=== this is NOT a typo, do not FIX it!!!
		foreach ($marcCols as $f) {
			list($t, $s) = explode('$', $f);
			$sql .= $this->q->mkSQL('or (bf.tag=%Q and bs.subfield_cd=%Q) ', $t, $s);
		}
		$sql .= ") ";
		//echo "sql===>$sql<br />";

		$iter = $this->q->select($sql);
		while (($row = $iter->next()) !== NULL) {
			$r['material_cd'] = $row['material_cd'];
			$r['material_type'] = $row['description'];
			$r['create_dt'] = $row['create_dt'];
			foreach ($marcCols as $name=>$f) {
				list($t, $s) = explode('$', $f);
				if ($row['tag'] == $t and $row['subfield_cd'] == $s) {
					$r[$name] = $row['subfield_data'];
				}
			}
		}
		if (!isset($r['title_0'])) {
			$r['title_0'] = '';
		}
		if (!isset($r['title_a'])) {
			$r['title_a'] = '';
		}
		if (!isset($r['title_b'])) {
			$r['title_b'] = '';
		}
		if (!isset($r['title_c'])) {
			$r['title_c'] = '';
		}
		if (!isset($r['title_d'])) {
			$r['title_d'] = '';
		}
		$r['title'] = $r['title_0'].' '.$r['title_a'].' '.$r['title_b'].' '.$r['title_c'].' '.$r['title_d'];
		if (!isset($r['author_0'])) {
			$r['author_0'] = '';
		}
		if (!isset($r['author_a'])) {
			$r['author_a'] = '';
		}
		if (!isset($r['author_b'])) {
			$r['author_b'] = '';
		}
		$r['author'] = $r['author_0'].' '.$r['author_a'].' '.$r['author_b'];
		if (!isset($r['date_0'])) {
			$r['date_0'] = '';
		}
		if (!isset($r['date_a'])) {
			$r['date_a'] = '';
		}
		$r['date'] = $r['date_0'].' '.$r['date_a'];
		return $r;

	}
}
