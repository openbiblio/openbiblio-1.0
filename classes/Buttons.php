<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Buttons {
	function display($buttons) {
		$buttons = Buttons::_cleanup($buttons);
		$s .= '<table class="buttons"><tr>';
		foreach ($buttons as $b) {
			list($title, $url, $confirm) = $b;
			$s .= '<td><a href="'.H($url).'"';
			if ($confirm) {
				$s .= ' onClick="return confirm(&quot;'.H(addslashes($confirm)).'&quot;)"';
			}
			$s .= '>'.$title.'</a></td>';
		}
		$s .= '</tr></table>';
		return $s;
	}
	function _cleanup($buttons) {
		$l = array();
		foreach ($buttons as $b) {
			switch (count($b)) {
			case 2:
				$l[] = array($b[0], $b[1], NULL);
				break;
			case 3:
				$l[] = array($b[0], $b[1], $b[2]);
				break;
			default:
				Fatal::internalError('Bad button spec');
			}
		}
		return $l;
	}
}
