<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Week {
	public $days;

	// TODO: Order this according to first_day_of_week setting
	function __construct() {
		for($i=0;$i<7;$i++) {
			$this->days[$i] = jddayofweek($i, 1);
		}
	}

	public function get_days() {
		return $this->days;
	}
}

