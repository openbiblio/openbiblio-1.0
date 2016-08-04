<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Week {
	public $days;

	function __construct() {
		$first_day = Settings::get('first_day_of_week') ? Settings::get('first_day_of_week') : 1;
		for($i=$first_day;$i<($first_day+7);$i++) {
			$this->days[$i] = T(jddayofweek(($i%7), 1));
		}
	}

	public function get_days() {
		return $this->days;
	}
}

