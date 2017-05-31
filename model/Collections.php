<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class CircCollections extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('collection_circ');
		$this->setFields(array(
			'code'=>'number',
			'days_due_back'=>'number',
			'minutes_due_back'=>'number',
			'regular_late_fee'=>'number',
			'due_date_calculator'=>'string',
			'minutes_before_closing'=>'number',
			'important_date'=>'date',
			'important_date_purpose'=>'string',
			'number_of_minutes_between_fee_applications'=>'number',
			'number_of_minutes_in_grace_period'=>'number',
			'pre_closing_padding'=>'number',
		));
		$this->setKey('code');
		$this->setForeignKey('code', 'collection_dm', 'code');
		$this->calculators = [];

		/*
		/ If you would like to design your own way of calculating due dates, enter it below as an array with the
		/ following indices:
		/ 'code' => the short name for your calculator.  This is what an end user will select to choose your calculator
		/ 'calculation' => an SQL query that returns a single timestamp value in a column named "due".  This calculation
		/	will be run through OpenBiblio's mkSQL function, which will substitute in any params that you may need.
		/ 'required_params' => an array of labels that will help the calculator get the parameters that it needs.
		/ 	For example, if your calculator includes 'required_params' => array('calendarid'), when you call
		/	getDueDate(), it will automagically get the id for your current site's calendar.
		/	Valid values in this array are: calendarid, siteid, collectionid, bibid
		/ 
		*/
		$this->calculators[] = array(
			'code' => 'simple',
			'required_params' => array('calendarid', 'calenderid', 'collectionid'),
			'calculation' => 'SELECT CASE '
				// If due date would fall on a day the Library is closed, instead, move to 11:59 on the next open day
				. 'WHEN DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute) '
				. "IN (SELECT date FROM calendar WHERE calendar=%N AND open='No') "
				. 'THEN (SELECT (TIMESTAMP(calendar.date) + INTERVAL 23 hour + INTERVAL 59 minute) FROM calendar, collection_circ '
				. 'WHERE calendar.date>(now() + INTERVAL collection_circ.days_due_back day + INTERVAL collection_circ.minutes_due_back minute) '
				. "AND calendar.calendar=%N AND calendar.open='Yes' "
				. 'ORDER BY date ASC LIMIT 1) '
				// Otherwise, just add the requested days and minutes to the current time to arrive at the due date
				. 'ELSE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute) '
				. 'END AS due FROM collection_circ WHERE code=%N ',
		);
		$this->calculators[] = array(
			'code' => 'at_midnight',
			'required_params' => array('calendarid', 'calenderid', 'collectionid'),
			'calculation' => 'SELECT CASE '
				// If due date would fall on a day the Library is closed, instead, move to 11:59 on the next open day
				. 'WHEN DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute) '
				. 'WHEN DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute) '
				. "IN (SELECT date FROM calendar WHERE calendar=%N AND open='No') "
				. 'THEN (SELECT (TIMESTAMP(calendar.date) + INTERVAL 23 hour + INTERVAL 59 minute) FROM calendar, collection_circ '
				. 'WHERE calendar.date>(now() + INTERVAL collection_circ.days_due_back day + INTERVAL collection_circ.minutes_due_back minute) '
				. "AND calendar.calendar=%N AND calendar.open='Yes' "
				. 'ORDER BY date ASC LIMIT 1) '
				// Otherwise, just set to 11:59pm on the day the due date would normally fall
				. 'ELSE (DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute)  + INTERVAL 23 hour + 59 minute) '
				. 'END AS due FROM collection_circ WHERE code=%N ',
		);
        $this->calculators[] = array(
            'code' => 'before_we_close',
            'required_params' => array('calendarid', 'calenderid', 'siteid', 'collectionid'),
            'calculation' => 'SELECT CASE '
			// Check if due date would fall on a day the Library is closed
	                        . 'WHEN DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute) '
	                        . "IN (SELECT date FROM calendar WHERE calendar=%N AND open='No') "
			// If the library will indeed be closed,  find the closest previous open day with open hours
			// that are not by appointment
			// then set the due date to X minutes before the library closes on the open day we've identified
			. 'THEN (SELECT (calendar.date + '
			. 'INTERVAL TRUNCATE((open_hours.end_time / 100), 0) hour + INTERVAL (open_hours.end_time %% 100) minute - '
			. 'INTERVAL pre_closing_padding minute) '
			. 'FROM calendar, open_hours, collection_circ '
			. 'WHERE open="Yes" AND DAYOFWEEK(calendar.date) = open_hours.day'
			. 'AND NOT open_hours.by_appointment AND calendar.calendar=%N AND open_hours.siteid=%N '
			. 'AND calendar.date<(now() + INTERVAL collection_circ.days_due_back day + INTERVAL collection_circ.minutes_due_back minute) '
			. 'ORDER BY date DESC, open_hours.end_time DESC LIMIT 1) '
			// Otherwise, set to X minutes before closing on the calculated date
	                        . 'ELSE (DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute)  + '
			. 'INTERVAL TRUNCATE((open_hours.end_time / 100), 0) hour + INTERVAL (open_hours.end_time %% 100) minute - '
			. 'INTERVAL pre_closing_padding minute) '
	                        . 'END AS due FROM collection_circ WHERE code=%N ',
		);
/*
                $this->calculators[] = array(
                        'code' => 'keep_it_longer',
                        'required_params' => array('calendarid', 'calenderid', 'collectionid'),
                        'calculation' => 'SELECT CASE '
                                . 'WHEN (DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute) '
                                . "IN (SELECT date FROM calendar WHERE calendar=%N AND open='No')) "
				// If it lands in a closed hour  -- not sure this is working yet, and now() needs to be replaced with the actual calculated date
                                . 'OR 1 IN  (SELECT 1 FROM open_hours WHERE (EXTRACT(HOUR FROM now() * 100) + EXTRACT(MINUTE FROM now()) > start_time) '
				. 'AND (EXTRACT(HOUR FROM now() * 100) + EXTRACT(MINUTE FROM now()) < end_time) AND DAYOFWEEK(now())=open_hours.day) '
				// THen select next closed time -- still needs work
                                . 'THEN (SELECT (TIMESTAMP(calendar.date) + INTERVAL 23 hour + INTERVAL 59 minute) FROM calendar, collection_circ '
                                . 'WHERE calendar.date>(now() + INTERVAL collection_circ.days_due_back day + INTERVAL collection_circ.minutes_due_back minute) '
                                . "AND calendar.calendar=%N AND calendar.open='Yes' "
                                . 'ORDER BY date ASC LIMIT 1) '
                                . 'ELSE (DATE (now() + INTERVAL days_due_back day + INTERVAL minutes_due_back minute)  + INTERVAL 23 hour + 59 minute) '
                                . 'END AS due FROM collection_circ WHERE code=%N ',
		);
*/
        $this->calculators[] = array(
            'code' => 'ask_me',
            'required_params' => array('calendarid', 'calenderid', 'collectionid'),
			// Don't select a due date at this stage
            'calculation' => 'SELECT NULL AS due ',
		);
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('days_due_back', 'minutes_due_back', 'due_date_calculator', 'regular_late_fee') as $req) {
			if ($insert and !isset($rec[$req]) or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}

		$positive = array('days_due_back', 'regular_late_fee');
		foreach ($positive as $f) {
			if (!is_numeric($rec[$f])) {
				$errors[] = new FieldError($f, T("Field must be numeric"));
			} else if ($rec[$f] < 0) {
				$errors[] = new FieldError($f, T("Field cannot be less than zero"));
			}
		}
		return $errors;
	}
	public function list_calculators() {
		$calc_codes = [];
		foreach ($this->calculators as $c) {
			$calc_codes[] = $c['code'];
		}
		return $calc_codes;
	}
	public function list_important_date_purposes() {
		return Array('not enabled', 'ceiling_date', 'specific_date');
	}
	public function propose_due_date($member_id, $copy_barcode) {
		$calculated_date = NULL;
		$copy = $this->select01($this->mkSQL('SELECT * FROM biblio_copy WHERE barcode_nmbr=%Q LIMIT 1', $copy_barcode));
		$collection_circ = $this->select01($this->mkSQL('SELECT * FROM collection_circ WHERE code IN (SELECT collection_cd FROM biblio WHERE biblio.bibid=%N)  LIMIT 1', $copy['bibid']));
		$calculator = $collection_circ['due_date_calculator'];
		$important_date = $collection_circ['important_date'];
		$important_date_purpose = $collection_circ['important_date_purpose'];

		// Calculate a tentative due date
		foreach ($this->calculators as $calc) {
			if ($calculator == $calc['code']) {
				$params = [];
				foreach ($calc['required_params'] as $i) {
					$params[] = $this->get_parameter_for_calculator($i, $member, $copy, $collection_circ);
				}
				$calculated_date = $this->select01($this->mkSQLFromArray(array_merge(array($calc['calculation']), $params)))['due'];
			}
		}

		// Then check for important date conflicts
		if ('ceiling_date' == $important_date_purpose && $important_date<$calculated_date) {
			return $important_date;
		} elseif ('specific_date' == $important_date_purpose) {
			return $important_date;
		}
		return $calculated_date;
	}

	private function get_parameter_for_calculator($param, $member, $copy, $collection_circ) {
		switch ($param) {
			case 'calendarid':
				return $this->select01($this->mkSQL('SELECT calendar FROM site WHERE siteid=%N', $_SESSION['current_site']))['calendar'];
			case 'collectionid':
				return $collection_circ['code'];
			case 'siteid':
				return $_SESSION['current_site'];
			default:
				return NULL;
		}
	}
}

class DistCollections extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('collection_dist');
		$this->setFields(array(
			'code'=>'number',
			'restock_threshold'=>'number',
		));
		$this->setKey('code');
		$this->setForeignKey('code', 'collection_dm', 'code');
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('restock_threshold') as $req) {
			if ($insert and !isset($rec[$req]) or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		$positive = array('restock_threshold');
		foreach ($positive as $f) {
			if (!is_numeric($rec[$f])) {
				$errors[] = new FieldError($f, T("Field must be numeric"));
			} else if ($rec[$f] < 0) {
				$errors[] = new FieldError($f, T("Field cannot be less than zero"));
			}
		}
		return $errors;
	}
}

class Collections extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('collection_dm');
		$this->setFields(array(
			'code'=>'number',
			'description'=>'string',
			'default_flg'=>'string',
			'type'=>'string',
		));
        $this->setReq(array(
            'description', 'default_flg', 'type'
        ));
		$this->setSequenceField('code');
		$this->setKey('code');

		$this->colltypes = array(
			'Circulated' => new CircCollections,
			'Distributed' => new DistCollections,
		);
	}

	protected function validate_el($rec, $insert) {
		$errors = array();
        // all required fields present?
		foreach ($this->reqFields as $req) {
			if ($insert and !isset($rec[$req]) or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
        // valid collection type?
		if (isset($rec['type']) and !array_key_exists($rec['type'], $this->colltypes)) {
			$errors[] = new FieldError('type', T("Bad collection type"));
		}
        // duplicate state codes not allowed
		$sql = $this->mkSQL("SELECT * FROM %q WHERE code=%Q ", $this->name, $rec['code']);
		$rslt = $this->select($sql);
        $rows = $rslt->fetchAll();
        if ($insert&& (count($rows) != 0)) {
			//$errors[] = new FieldError('code', T("Duplicate State Code not allowed"));
			$errors[] = T("Duplicate Code not allowed");
		}
        // otherwise limit default flg to Y or N only
        if ($rec['default_flg'] != 'Y' && $rec['default_flg']!= 'N') {
			$errors[] = new FieldError('default_flg', T("Default Flg MUST be 'Y' or 'N'"));
        }
		return $errors;
	}

	public function insert($rec, $confirmed=false) {
        // if no default flg present, set to 'N'
		if (!isset($rec['default_flg'])) {
            $rec['default_flg'] = 'N';
        }
        list($parm1, $parm2) = parent::insert($rec, $confirmed=false);
        return array($parm1, $parm2);
    }

	public function getTypeSelect() {
		$types = array();
		foreach (array_keys($this->colltypes) as $t) {
			$types[$t] = $t;
		}
		return $types;
	}
	public function getByBibid($bibid) {
		$sql = "SELECT c.* FROM collection_circ c, biblio b "
			. "WHERE c.code=b.collection_cd "
			. $this->mkSQL("AND b.bibid=%N ", $bibid);
//echo "sql=$sql<br />\n";
		return $this->select1($sql);
	}
	public function getAllWithStats() {
		$sql = "SELECT c.*, "
			. "COUNT(distinct b.bibid) as count "
			. "FROM collection_dm c "
			. "LEFT JOIN biblio b "
			. "ON b.collection_cd=c.code "
			. "GROUP BY c.code, c.description, c.default_flg "
			. "ORDER BY c.description ";
		return $this->select($sql);
	}
	public function getTypeData($rec) {
		$table = $this->colltypes[$rec['type']];
		return $table->getOne($rec['code']);
	}
	public function get_name($code) {
		$sql = "SELECT description "
			. "FROM collection_dm "
			. "WHERE code='".$code."';";
		$row = $this->select1($sql);
		return $row['description'];
	}
	public function insert_el($rec, $confirmed = false) {
		list ($id, $errs) = DBTable::insert_el($rec, $confirmed);
		if ($errs)
			return array(NULL, $errs);
		$rec['code'] = $id;
		list (, $errs) = $this->colltypes[$rec['type']]->insert_el($rec);
		if ($errs) {
			DBTable::deleteOne($id);
			return array(NULL, $errs);
		}
		return array($id, NULL);
	}
	public function update_el($rec, $confirmed = false) {
		$old = $this->getOne($rec['code']);
		$errs = DBTable::update_el($rec, $confirmed);
		if ($errs)
			return $errs;
		$updated = $this->getOne($rec['code']);
		if ($old['type'] == $updated['type']) {
			$table = $this->colltypes[$updated['type']];
			$errs = $table->update_el($rec);
		} else {
			$otable = $this->colltypes[$old['type']];
			$ntable = $this->colltypes[$updated['type']];
			$otable->deleteOne($rec['code']);
			list(, $errs) = $ntable->insert_el($rec);
		}
		return $errs;
	}
	public function deleteOne() {
		//echo "in Collections::deleteOne()";
		$code = func_get_arg(0);
		echo parent::deleteOne($code);
		foreach ($this->colltypes as $table) {
			echo $table->deleteOne($code);
		}
	}
	public function deleteMatches($fields) {
		$rows = $this->getMatches($fields);
		while ($row = $rows->fetch_assoc()) {
			$this->deleteOne($row['code']);
		}
	}
}
