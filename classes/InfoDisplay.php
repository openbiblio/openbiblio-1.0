<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Buttons.php"));

class InfoDisplay {
	var $title;
	function InfoDisplay() {
		$this->title = NULL;
		$this->buttons = array();
	}
	function begin() {
		$s = "<table class='info_display'>\n";
		if ($this->title) {
			$s .= '<tr><td class="header" colspan="2">'
						. '<table width="100%"><tr><th class="title">'.$this->title.'</th>';
			if ($this->buttons) {
				$s .= '<td class="buttons">'.Buttons::display($this->buttons).'</td>';
			}
			$s .= "</tr></table></th></tr>\n";
		}
		return $s;
	}
	function row($heading, $value) {
		return '<tr><th>'.$heading.'</th><td>'.$value.'</td></tr>'."\n";
	}
	function end() {
		return "</table>\n";
	}
}
