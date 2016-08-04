<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Week {
	public $days;

	function __construct() {
		for($i=0;$i<7;$i++) {
			$this->days[$i] = T(jddayofweek($i, 1));
		}
	}

	public function get_days() {
		return $this->days;
	}
}

