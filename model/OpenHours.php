<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

ini_set('display_errors', 1);
require_once(REL(__FILE__, "../classes/DBTable.php"));
require_once(REL(__FILE__, "../classes/Week.php"));

class OpenHours extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('open_hours');
		$this->setFields(array(
			'hourid'=>'number',
			'siteid'=>'number',
			'day'=>'number',
			'start_time'=>'number',
			'end_time'=>'number',
			'by_appointment'=>'bool',
			'public_note'=>'string',
			'private_note'=>'string',
		));
		$this->setKey('hourid');
        $this->setReq(array(
            'siteid', 'day',
        ));
	}

	protected function validate_el($rec, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
		return $errors;
	}

	private function getHoursOnDay($day_code) {
		$hours = Array();
		$set = $this->getMatches(array('day' => $day_code));
		foreach ($set as $row) {
			$hours[] = $row;
		}
		return count($hours) ? $hours : FALSE;
	}

	private function parse($opening_hour) {
		$arr = Array();
		$arr['MM'] = str_pad(intval($opening_hour % 100), 2, '0', STR_PAD_LEFT);
		$arr['H'] = floor($opening_hour / 100);
		$arr['HH'] = str_pad(intval($arr['H']), 2, '0', STR_PAD_LEFT);
		if (12 < $arr['H']) {
			$arr['h'] = $arr['H'] % 12;
			$arr['meridian'] = 'P.M.';
		} else {
			$arr['h'] = $arr['H'];
			$arr['meridian'] = 'A.M.';
		}
		return $arr;
	}

	private function human_readable($opening_hour) {
		$time = $this->parse($opening_hour);
		return $time['h'] . ':' . $time['MM'] . ' ' . $time['meridian'];
	}

	private function partial_time($opening_hour) {
		// Returns times in the RFC3339 partial-time format
		$time = $this->parse($opening_hour);
		return $time['HH'] . ':' . $time['MM'] . ':00';
	}

	public function displayOpenHours() {
		$week = new Week;
		$days = $week->get_days();
		$first_day = Settings::get('first_day_of_week') ? Settings::get('first_day_of_week') : 0;

		$hours_string = '<div itemprop="openingHoursSpecification" itemtype="http://schema.org/OpeningHoursSpecification">';

		for ($i=$first_day;$i<(7+$first_day);$i++) {
			$day_code = ($i % 7);
			if($this->getHoursOnDay($day_code)) {
				$hours_string .= '<link itemprop="dayOfWeek" href="http://schema.org/' . jddayofweek($day_code, 1) . '" />' . T(jddayofweek($day_code, 1)) . ': '; 
				$openings = $this->getHoursOnDay($day_code);
				$openings_strings = Array();
				foreach ($openings as $opening) {
					if ($opening['by_appointment']) {
						$openings_strings[] = T('By appointment');
					} else {
						$openings_strings[] =  '<time itemprop="opens" datetime="' . $this->partial_time($opening['start_time']) . '"> ' . $this->human_readable($opening['start_time']) . '</time> - '
							. '<time itemprop="closes" datetime="' . $this->partial_time($opening['end_time']) . '"> ' . $this->human_readable($opening['end_time']) . '</time>';
					}
				}
				$hours_string .= implode(', ', $openings_strings);
				$hours_string .= '<br />';
			}
		}
		$hours_string .= '</div>';
		return $hours_string;
		
	}


/*
*/



}
