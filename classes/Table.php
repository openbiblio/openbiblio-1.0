<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../functions/inputFuncs.php"));

class Table {
	var $_cols;
	var $_params;
	var $_rown;
	var $_echolink;
	var $_checkbox;
	var $_idcol;
	var $_checked=false;
	function __construct($echolink=NULL, $checkbox=false) {
		$this->_echolink = $echolink;
		$this->_checkbox = $checkbox;
		$this->_cols = array();
		$this->_params = array();
	}
	function columns($cols) {
		$this->_cols = array_merge($this->_cols, $cols);
	}
	function parameters($params) {
		$this->_params = array_merge($this->_params, $params);
	}
	function start() {
		$echolink = $this->_echolink;
		$this->_rown=1;
		echo "<table class='results'>\n";
		echo "<tr>\n";
		if ($this->_checkbox) {
			foreach ($this->_cols as $col) {
				if (!$this->_idcol and $col['checkbox']) {
					$this->_idcol = $col['name'];
					if ($col['checked'] === true) {
						$this->_checked = true;
					}
				}
			}
			echo '<td valign="middle" align="center" class="primary">';
			echo '<span class="small">';
			echo '<b>All</b><br />';
			echo '<input type="checkbox" name="all" value="all" onclick="setCheckboxes()" ';
			if ($this->_checked) {
				echo 'checked="checked" ';
			}
			echo '/>';
			echo '</span>';
		}
		foreach ($this->_cols as $col) {
			if ($col['hidden']) {
				continue;
			}
			if (!$col['title']) {
				$col['title'] = $col['name'];
			}
			echo '<td valign="middle" align="center" class="primary">';
			echo '<span class="small"><b>'.$col['title'].'</b></span>';
			if ($col['sort'] and $echolink) {
				echo "<br /><nobr>";
				$echolink(1, "<img border='0' src='../images/down.png' alt='&darr;'>",
					$col['sort']);
				$echolink(1, "<img border='0' src='../images/up.png' alt='&uarr;'>",
					$col['sort'].'!r');
				echo "</nobr>";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
	function row($row) {
		$class = array('primary', 'alt1');
		echo "<tr>\n";
		if ($this->_checkbox) {
			echo '<td class="'.H($class[$this->_rown%2]).'" align="center">';
			if ($this->_idcol) {
				echo '<input type="checkbox" name="id[]" ';
				echo 'value="'.H($row[$this->_idcol]).'" ';
				if ($this->_checked) {
					echo 'checked="checked" ';
				}
				echo '/>';
			}
			echo "</td>\n";
		}
		foreach ($this->_cols as $col) {
			if ($col['hidden']) {
				continue;
			}
			echo '<td class="'.H($class[$this->_rown%2]).'"';
			if ($col['align']) {
				echo 'align="'.H($col['align']).'"';
			}
			echo '>';
			if ($col['func'] and in_array($col['func'], get_class_methods('TableFuncs'))) {
				echo TableFuncs::$col['func']($col, $row, $this->_params);
			} else {
				echo H($row[$col['name']]);
			}
			echo "</td>\n";
		}
		echo "</tr>\n";
		$this->_rown++;
	}
	function end() {
		echo "</table>\n";
	}
}

class TableFuncs {
	function raw($col, $row, $params) {
		return $row[$col['name']];
	}
	function _link_common($col, $row, $params, $url, $rpt_colname=NULL) {
		if ($rpt_colname and $params['rpt']
				and in_array($rpt_colname, $params['rpt_colnames'])) {
			assert('$row[".seqno"] !== NULL');
			$url .= '&amp;rpt='.HURL($params['rpt'])
				. '&amp;seqno='.HURL($row['.seqno']);
		}
		$s = '<a ';
		if ($col['link_class']) {
			$s .= 'class="'.$col['link_class'].'" ';
		}
		$s .= 'href="'.$url.'">'.H($row[$col['name']]).'</a>';
		return $s;
	}
	function item_cart_add($col, $row, $params) {
		global $tab;	# FIXME - get rid of $tab
		$url = '../shared/cart_add.php?name=bibid&amp;id[]='.HURL($row['bibid']).'&amp;tab='.HURL($tab);
		return TableFuncs::_link_common($col, $row, $params, $url);
	}
	function item_cart_del($col, $row, $params) {
		global $tab;	# FIXME - get rid of $tab
		$url = '../shared/cart_del.php?name=bibid&amp;id[]='.HURL($row['bibid']).'&amp;tab='.HURL($tab);
		return TableFuncs::_link_common($col, $row, $params, $url);
	}
	function biblio_link($col, $row, $params) {
		global $tab;	# FIXME - get rid of $tab
		$url = '../shared/biblio_view.php?bibid='.HURL($row['bibid']);
		if ($tab != 'opac') {
			$url .= '&amp;tab=cataloging';
		} else {
			$url .= '&amp;tab=opac';
		}
		return TableFuncs::_link_common($col, $row, $params, $url, 'bibid');
	}
	function subject_link($col, $row, $params) {
		global $tab;	# FIXME - get rid of $tab
		$url = '../shared/biblio_search.php?searchType=subject&amp;exact=1&amp;searchText='.HURL($row['subject']);
		if ($tab != 'opac') {
			$url .= '&amp;tab=cataloging';
		} else {
			$url .= '&amp;tab=opac';
		}
		return TableFuncs::_link_common($col, $row, $params, $url);
	}
	function series_link($col, $row, $params) {
		global $tab;	# FIXME - get rid of $tab
		$url = '../shared/biblio_search.php?searchType=series&amp;exact=1&amp;searchText='.HURL($row['series']);
		if ($tab != 'opac') {
			$url .= '&amp;tab=cataloging';
		} else {
			$url .= '&amp;tab=opac';
		}
		return TableFuncs::_link_common($col, $row, $params, $url);
	}
	function booking_link($col, $row, $params) {
		$url = '../circ/booking_view.php?bookingid='.HURL($row['bookingid']);
		return TableFuncs::_link_common($col, $row, $params, $url, 'bookingid');
	}
	function member_link($col, $row, $params) {
		$url = '../circ/memberForms.php?mbrid='.HURL($row['mbrid']);
		return TableFuncs::_link_common($col, $row, $params, $url, 'mbrid');
	}
	function site_link($col, $row, $params) {
		$url = '../admin/sites_edit_form.php?siteid='.HURL($row['siteid']);
		return TableFuncs::_link_common($col, $row, $params, $url, 'siteid');
	}
	function calendar_link($col, $row, $params) {
		$url = '../admin/calendarForm.php?calendar='.HURL($row['calendar']);
		return TableFuncs::_link_common($col, $row, $params, $url, 'calendar');
	}
	function checkbox($col, $row, $params) {
		assert('$col["checkbox_name"] != NULL');
		assert('$col["checkbox_value"] != NULL ');
		$s = '<input type="checkbox" ';
		$s .= 'name="'.H($col['checkbox_name']).'" ';
		$s .= 'value="'.H($row[$col['checkbox_value']]).'" ';
		if ($col['checkbox_checked'] === true) {
			$s .= 'checked="checked" ';
		} elseif (is_string($col['checkbox_checked'])) {
			if (strtolower($row[$col['checkbox_checked']]) == 'y') {
				$s .= 'checked="checked" ';
			}
		}
		$s .= '/>';
		return $s;
	}
	function select($col, $row, $params) {
		assert('$col["select_name"] != NULL');
		assert('$col["select_index"] != NULL');
		assert('$col["select_key"] != NULL');
		assert('$col["select_value"] != NULL ');
		$name = $col['select_name'].'['.$row[$col['select_index']].']';
		$data = array();
		foreach ($row[$col['name']] as $r) {
			$data[$r[$col['select_key']]] = $r[$col['select_value']];
		}
		if (isset($col['select_selected']) and isset($row[$col['select_selected']])) {
			$selected = $row[$col['select_selected']];
		} else {
			$selected = '';
		}
		return inputfield('select', $name, $selected, NULL, $data);
	}
	function member_list($col, $row, $params) {
		$s = '';
		foreach ($row[$col['name']] as $m) {
			$t = 'href="../circ/memberForms.php?mbrid';
			$s .= '<a '.$t.'='.HURL($m['mbrid']).'">'
						. H($m['first_name']).' '.H($m['last_name']).' ('.H($m['site_code']).')'
						. '</a>, ';
		}
		return substr($s, 0, -2);  # lose the final ', '
	}
}
