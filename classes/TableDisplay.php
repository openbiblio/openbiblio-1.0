<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
/*
 * Alowable column parameters for display table:
 * title="........", align=[left|right|center], width=??
 */
 
class TableDisplay {
	var $title;
	var $columns;
	var $rown;
	function TableDisplay() {
		$this->title = NULL;
		$this->columns = array();
		$this->rown = 0;
	}
	function mkCol($title, $params=NULL) {
		if ($params==NULL) {
			$params = array();
		}
		return array_merge($params, array('title'=>$title));
	}
	function begin() {
		$this->rown=1;
		$s = "<table class='table_display'>\n";
		$s .= '<thead>';
		if ($this->title) {
			$s .= '<tr><th colspan="'.H(count($this->columns)+1).'" ><div class="title">'
						. $this->title."</div></th></tr>\n";
		}
		$s .= '<tr class="headings">';
		foreach ($this->columns as $col) {
			$s .= '<th>'.$col['title']."</th>\n";
		}
		$s .= "</tr>\n";
		$s .= '</thead>';
		$s .= '<tbody class="striped">';
		return $s;
	}
	function row() {
		return $this->rowArray(func_get_args());
	}
	function rowArray($row) {
		if (count($row) != count($this->columns)) {
			Fatal::internalError(T("Column count mismatch in TableDisplay"));
		}
		$class = array('even', 'odd');
		$s = '<tr class="'.H($class[$this->rown%2]).'">';
		for ($i=0;$i<count($this->columns);$i++) {
			$col = $this->columns[$i];
			$s .= '<td ';
			if ($col['align']) {
				$s .= 'align="'.H($col['align']).'"';
			} else {
				$s .= 'align="left"';
			}
			$s .= '>'.$row[$i]."</td>\n";
		}
		$s .= "</tr>\n";
		$this->rown++;
		return $s;
	}
	function end() {
		$s = '</tbody>';
		if ($this->rown == 1) {
			$s .= '<tr class="odd">'
					 . '<td colspan="'.H(count($this->columns)).'">' . T("No results found.") . '</td></tr>';
		}
		$s .= "</table>\n";
		return $s;
	}
}
