<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Iter.php"));
require_once(REL(__FILE__, "../model/MaterialFields.php"));

class BiblioRows {
	protected $q;

	function columns() {
		return array(
			array('name'=>'bibid', 'hidden'=>true, 'checkbox'=>true),
			array('name'=>'material_cd', 'hidden'=>true),
			array('name'=>'create_dt', 'hidden'=>true),
			array('name'=>'callno', 'title'=>'Call No.', 'sort'=>'callno'),
			array('name'=>'title', 'title'=>'Title', 'func'=>'biblio_link', 'sort'=>'title'),
			array('name'=>'author', 'title'=>'Author', 'sort'=>'author'),
			array('name'=>'date', 'title'=>'Date', 'sort'=>'date'),
//			array('name'=>'level', 'title'=>'Grade Level'),
			array('name'=>'pubdate', 'title'=>'Publication Date'),
			array('name'=>'material_type', 'title'=>'Type', 'sort'=>'type'),
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
	public function __construct($iter) {
		$this->q = new Queryi;
		$this->iter = $iter;
	}
	public function count() {
		return $this->iter->num_rows;
	}
	public function skip() {
		$this->iter->fetch_assoc();
	}
	public function next() {
		## this builds the sql to get search item details for later display
		$r = $this->iter->fetch_assoc();
		if ($r === NULL) return $r;
		//print_r($r);

		## Construct an array of displayable fields for the material_cd of this biblio
		$media = new MaterialFields;
		$data = $media->getDisplayInfo($r['material_cd']);
		$fldset = $data[$r['material_cd']];
		//print_r($fldset);echo"<br /><br />";
		for ($i=0; $i<count($fldset); $i++) {
			$t = $fldset[$i]['tag'];
			$s = $fldset[$i]['suf'];
			$l =$fldset[$i]['lbl'];
			$mc[$l] = $t.'$'.$s;
		}
		//print_r($mc);echo"<br />.................<br />";

		## get data for this biblio, one tag$suf per row
		$sql = "select b.bibid, b.create_dt, b.material_cd, m.description, "
					 . "bf.tag, bs.subfield_cd, bs.subfield_data "
					 . "from biblio b, material_type_dm m, biblio_field bf, biblio_subfield bs "
					 . $this->q->mkSQL("where b.bibid=%N ", $r['bibid'])
					 . "and m.code=b.material_cd "
					 . "and bf.bibid=b.bibid and bs.fieldid=bf.fieldid "
					 . "and ( (1=0) "; // <=== this is NOT a typo, do not FIX it!!!

		foreach ($mc as $f) {
			list($t, $s) = explode('$', $f);
			$sql .= $this->q->mkSQL('or (bf.tag=%Q and bs.subfield_cd=%Q) ', $t, $s);
		}
		$sql .= ") ";
		$sql .= " order by bf.tag,bs.subfield_cd"; ## be sure tags are in proper order
		//echo "sql===>$sql<br />";
		$iter = $this->q->select($sql);

		## process each biblio tag, one at a time
		while (($row = $iter->fetch_assoc()) !== NULL) {
			$r['material_cd'] = $row['material_cd'];
			$r['material_type'] = $row['description'];
			$r['create_dt'] = $row['create_dt'];

			## if a tag matches a displayable field, add it to the display array $r
			foreach ($mc as $name=>$f) {
				//echo "mc tag=".$f."; row tag=".$row['tag']."; row suff=".$row['subfield_cd'];
				list($t, $s) = explode('$', $f);
				if ($row['tag'] == $t and $row['subfield_cd'] == $s) {
					//echo " <== matched<br /><br />";
					if (strtolower($name) == 'call number') $name = 'callno';
					if (strtolower($name) == 'corporate name') $name = 'author';
					if (strtolower($name) == 'book title') $name = 'title';
					if (strtolower($name) == 'report title') $name = 'title';
					if (strtolower($name) == 'subtitle') $name = 'title';
					if (strtolower($name) == 'year') $name = 'date';
					if (strtolower($name) == 'publication date') $name = 'pubdate';
					if (empty($r[strtolower($name)])) {
							$r[strtolower($name)] = $row['subfield_data'];
					} else {
							$r[strtolower($name)] .= ' - '.$row['subfield_data'];
					}
					break; ## done with this $row, start on another
				}
			}
		}
		//print_r($r);echo"<br />............<br />";

		return $r;
	}
}
