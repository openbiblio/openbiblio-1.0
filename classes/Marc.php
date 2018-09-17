<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
/**
 * Data classes and a parser for MARC data.
 *
 * @author Micah Stetson, original author
 * @author Fred LaPlante, updated to PHP 7, July 2018
 */

define("MARC_DELIMITER", "\x1f");
define("MARC_FT", "\x1e");	# Field terminator
define("MARC_RT", "\x1d");	# Record terminator

/* -------------------------------------------------------------------------- */
# FIXME - These conversions only support those characters that are
# absolutey necessary.  The mnemonics for other characters should
# be added at some point.
/* -------------------------------------------------------------------------- */
class MarcHelpers {
	static function toMnem($s) {
		$map = array(
			'{' => '{lcub}',
			'}' => '{rcub}',
			'$' => '{dollar}',
			'\\' => '{bsol}',
		);
		$t = '';
		while (strlen($s)) {
			$did_subst = False;
			foreach($map as $from => $to) {
				if (substr($s, 0, strlen($from)) == $from) {
					$t .= $to;
					$s = substr($s, strlen($from));
					$did_subst = True;
					break;
				}
			}
			if (!$did_subst) {
				if (preg_match('/^  +/', $s, $m)) {
					$t .= str_repeat('\\', strlen($m[0]));
					$s = substr($s, strlen($m[0]));
				} else {
					$t .= $s{0};
					$s = substr($s, 1);
				}
			}
		}
		return $t;
	}
	static function fromMnem($s) {
		$map = array(
			'{lcub}' => '{',
			'{rcub}' => '}',
			'{dollar}' => '$',
			'{bsol}' => '\\',
			'\\' => ' ',
		);
		$t = '';
		while (strlen($s)) {
			$did_subst = False;
			foreach($map as $from => $to) {
				if (substr($s, 0, strlen($from)) == $from) {
					$t .= $to;
					$s = substr($s, strlen($from));
					$did_subst = True;
					break;
				}
			}
			if (!$did_subst) {
				$t .= $s{0};
				$s = substr($s, 1);
			}
		}
		return $t;
	}
}

/* -------------------------------------------------------------------------- */
/* Base class for Control and Data fields */
/* -------------------------------------------------------------------------- */
class MarcField {
	public $tag;

	public function __construct($tag='') {
		$this->tag=strtoupper($tag);
	}
	function getValue($identifier=NULL) {
		$l = $this->getValues($identifier);
		if (count($l) > 0) {
			return $l[0];
		} else {
			return NULL;
		}
	}
	/* Methods below should be 'extended' by descendents
		cannot use abstract methods, as the default action is
		used in a few cases. FL Jul 2018
	*/
	protected function getMnem() {
		return '='.$this->tag.'  ';
	}
	protected function get() {
	}
	protected function getValues($identifier=NULL) {
		return array();
	}
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
class MarcControlField extends MarcField {
	public $data;

	public function __construct($tag='', $data='') {
		//$this->MarcField($tag);
		parent::__construct($tag);
		$this->data=$data;
	}
	public function get() {
		return $this->data . MARC_FT;
	}
	public function getMnem() {
		return parent::getMnem() . MarcHelpers::toMnem($this->data) . "\n";
	}
	public function getValues($identifier=NULL) {
		if ($identifier !== NULL) {
			return array();
		} else {
			array($this->data);
		}
	}
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
class MarcSubfield {
	public $identifier;
	public $data;

	public function __construct($i, $d) {
		$this->identifier=strtolower($i);
		$this->data=$d;
	}
	public function get() {
		return MARC_DELIMITER . $this->identifier . $this->data;
	}
	public function getMnem() {
		return '$' . MarcHelpers::toMnem($this->identifier) . MarcHelpers::toMnem($this->data);
	}
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
class MarcDataField extends MarcField {
	public $indicators;
	public $subfields;

	public function __construct($tag='', $indicators='  ') {
		//$this->MarcField($tag);
		parent::__construct($tag);
		$this->indicators=$indicators;
		$this->subfields=array();	# list of Subfield
	}
	public function get() {
		$s = $this->indicators;
		foreach ($this->subfields as $sf) {
			$s .= $sf->get();
		}
		return $s . MARC_FT;
	}
	public function getMnem() {
		$s = parent::getMnem() . str_replace(' ', '\\', $this->indicators);
		foreach ($this->subfields as $sf) {
			$s .= $sf->getMnem();
		}
		return $s . "\n";
	}
	public function getSubfields($identifier=NULL) {
		if ($identifier === NULL) {
			return $this->subfields;
		} else {
			$ret = array();
			foreach ($this->subfields as $sf) {
				if ($sf->identifier == $identifier) {
					array_push($ret, $sf);
				}
			}
			return $ret;
		}
	}
	public function getSubfield($identifier=NULL) {
		$l = $this->getSubfields($identifier);
		if (count($l) > 0) {
			return $l[0];
		} else {
			return NULL;
		}
	}
	public function getValues($identifier=NULL) {
		return array_map(create_function('$sf', 'return $sf->data;'),
			$this->getSubfields($identifier));
	}
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
class MarcRecord {
	protected $default_leader = '00000nam a2200000uu 4500';
	protected $_leader_fields = array(
		# array(name, type, length, title, required value)
		array('length', 'num', 5, 'length', NULL),
		array('status', 'str', 1, 'record status', NULL),
		array('type', 'str', 1, 'record type', NULL),
		array('impl_0708', 'str', 2, 'impl_0708', NULL),
		array('encoding', 'str', 1, 'character encoding', NULL),
		array('nindicators', 'num', 1, 'indicator count', 2),
		array('identlen', 'num', 1, 'subfield code length', 2),
		array('baseAddr', 'num', 5, 'base address of data', NULL),
		array('impl1719', 'str', 3, 'impl_1719', NULL),
		array('entryMapLength', 'num', 1, 'length-of-field length', 4),
		array('entryMapStart', 'num', 1, 'starting-character-position length', 5),
		array('entryMapImpl', 'num', 1, 'implementation-defined length', 0),
		array('entryMapUndef', 'num', 1, 'undefined entry-map field', 0),
	);
	//protected $fields;
	public $fields;

	public function __construct() {
//echo "in MarcRecord::__construct() <br />";
		# Provide a default leader
		$this->setLeader($this->default_leader);
		$this->fields = array();
	}
	public function addFields($entry) {
		$this->fields[] = $entry;
	}
	public function getFields($tag=NULL) {
		if ($tag === NULL) {
			return $this->fields;
		}
		$a = array();
		foreach ($this->fields as $f) {
			if ($f->tag == $tag) {
				array_push($a, $f);
			}
		}
		return $a;
	}
	public function setLeader($ldr, $lenient=False) {
//echo "in MarcRecord::setLeader() <br />";
		if ($lenient) {
			$ldr = rtrim($ldr);
		}
//echo "raw ldr  : $ldr<br />";
		if (strlen($ldr) != strlen($this->default_leader)) {
			if ($lenient) {
				$ldr .= substr($this->default_leader, strlen($ldr));
				$ldr = substr($ldr, 0, strlen($this->default_leader));
			} else {
				return 'wrong leader length';
			}
		}
//echo "adjtd ldr: $ldr<br />";
		foreach ($this->_leader_fields as $f) {
			$v = substr($ldr, 0, $f[2]);
			$ldr = substr($ldr, $f[2]);
			if ($f[1] == 'num') {
				if (!$lenient && !ctype_digit($v)) {
					return 'MARC21 requires ' . $f[3] . ' to be numeric';
				}
				$v += 0;
			}
			if (!$lenient and $f[4] !== NULL and $v != $f[4]) {
				return 'MARC21 requires ' . $f[3] . ' of ' . $f[4];
			}
			$this->$f[0] = $v;
		}
		return NULL;
	}

	public function getLeader() {
		$ldr = '';
		foreach ($this->_leader_fields as $f) {
			$s = '';
			if ($f[1] == 'str') {
				$s = $this->$f[0];
			} else if ($f[1] == 'num') {
				$s = sprintf('%0'.$f[2].'u', $this->$f[0]);
			}
			if (strlen($s) != $f[2]) {
				$s = sprintf('%-'.$f[2].'s', $s);
				$s = substr($s, 0, $f[2]);
			}
			$ldr .= $s;
		}
		assert('strlen($ldr) == 24');
		return $ldr;
	}

	// Returns array(record_string, error)
	// where record_string is only valid if error is NULL
	public function get() {
		$directory = '';
		$data = '';
		foreach ($this->fields as $f) {
			$d = $f->get();
			$l = array(
				array($f->tag, 3, 'tag has wrong length: '.$f->tag),
				array(strlen($d), 4, $f->tag.' field too long'),
				array(strlen($data), 5, 'record too long'),
			);
			foreach ($l as $t) {
				$s = sprintf('%0'.$t[1].'u', $t[0]);
				if (strlen($s) != $t[1]) {
					return array(NULL, $t[2]);
				}
				$directory .= $s;
			}
			$data .= $d;
		}
		# 24 is the leader length, 1 for the field terminator
		$this->baseAddr = 24 + strlen($directory) + 1;
		# 1 for the record terminator
		$this->length = $this->baseAddr + strlen($data) + 1;
		return array($this->getLeader() . $directory . MARC_FT . $data . MARC_RT, NULL);
	}

	public function getMnem() {
		$s = '=LDR  ' . MarcHelpers::toMnem($this->getLeader()) . "\n";
		foreach ($this->fields as $f) {
			$s .= $f->getMnem();
		}
		return $s . "\n";
	}



	public function getField($tag=NULL) {
		$l = $this->getFields($tag);
		if (count($l) > 0) {
			return $l[0];
		} else {
			return NULL;
		}
	}

	public function getValues($spec=NULL) {
		$l = array();
		if ($spec === NULL) {
			array_push($l, NULL);
		} else {
			$l = explode('$', $spec, 2);
		}
		if (count($l) == 1) {
			array_push($l, NULL);
		}
		$a = array();
		foreach ($this->getFields($l[0]) as $f) {
			foreach ($f->getValues($l[1]) as $v) {
				array_push($a, $v);
			}
		}
		return $a;
	}

	public function getValue($spec=NULL) {
		$l = $this->getValues($spec);
		if (count($l) > 0) {
			return $l[0];
		} else {
			return NULL;
		}
	}
}

/* -------------------------------------------------------------------------- */
## MARC parsing stuff below here
/* -------------------------------------------------------------------------- */
class MarcParseError {
	protected $msg;
	protected $record;
	protected $line;

	public function __construct($msg, $record=NULL, $line=NULL) {
		$this->msg = $msg;
		$this->record = $record;
		$this->line = $line;
	}
	public function toStr() {
		$s = '';
		if ($this->line !== NULL) {
			$s .= 'Line '.$this->line.': ';
		}
		if ($this->record !== NULL) {
			$s .= 'Record '.$this->record.': ';
		}
		return $s . $this->msg;
	}
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
abstract class MarcBaseParser {
	public $records;

	protected $lenient;
	protected $recnum;
	protected $unparsed;

	public function __construct($lenient=true) {
		$this->lenient = $lenient;
		$this->records = array();
		$this->recnum = 0;
		$this->unparsed = '';
	}
	public function parse($input = "") {
		$this->unparsed .= $input;
//echo "in MarcBaseParser::parse()<br />";
//       if (strlen($this->unparsed) < 5) {
//			return $this->_error("Invalid MARC record length");
//echo "input file len = ".strlen($this->unparsed)."<br />";
//echo "unparsed: ";print_r($this->unparsed);echo "<br />";
//		}
	}

	# MUST be implemented in derived classes
	abstract protected function eof();
	abstract protected function error($s);
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
class MarcParser extends MarcBaseParser {
	public function __construct ($lenient=true) {
		parent::__construct($lenient);
	}
	public function parse($input = '') {
		$recLen = 0;
		$unparLen = 0;
//echo "in MarcParser::parse(), pt A<br />";

		parent::parse($input);
//echo "in MarcParser::parse(), pt B<br />";

		$unparLen = strlen($this->unparsed);
		while ($unparLen >= 5) {
//echo "Unparsed data len = $unparLen <br />";
//return;
			$this->recnum = count($this->records);
//echo $this->recnum." recs found so far<br />";
//return;
//echo "unparsed: ";print_r($this->unparsed);echo "<br />";
//return;
			$recLen = substr($this->unparsed, 0, 5);
//echo "current MARC record len field = $recLen<br />";
			//if (!ctype_digit($recLen)) {
			//	return $this->_error("garbled length field");
			//}
			if ($recLen < 24) {
				return $this->_error("impossibly small length field");
			}
//return;
			if ($unparLen < $recLen) {
				return $this->_error("Input size exceeds the coded record length.");
				//break;
			}
//echo "record dimensions OK.<br />";
//return;
			$marcRec = substr($this->unparsed, 0, $recLen);
//echo "MARC record: ";print_r($marcRec);echo "<br />";
//return;
			$r = $this->parseRecord($marcRec);
//return;
			if (is_a($r, 'MarcParseError')) {
				return $r;
			}
			array_push($this->records, $r);
			$this->unparsed = substr($this->unparsed, $recLen);
			$unparLen = strlen($this->unparsed);
		}
		return count($this->records)-$this->recnum;
	}

	private function parseRecord($rec) {
//echo "in MarcParser::parseRecord()<br />";

		$r = new MarcRecord();
		$this->recnum += 1;
		$ldr = substr($rec, 0, 24);
		$err = $r->setLeader(ldr, $this->lenient);

//echo "ldr err: ";print_r($err);echo "<br />";
//return;
//		if (!empty($err)) {
//			echo "Invalid Leader: ".$err;
//			return;
//		}
//echo "record leader OK.<br />";
//return;
$r->baseAddr = substr($rec, 12, 5);
		$base=$r->baseAddr;
//echo "marc fields base addr = $base<br />";
//return;
		$dir = substr($rec, 24, $base-24);
//echo "MARC record dir: ";print_r($dir);echo "<br />";
//return;
		$entries = $this->parseDirectory($dir);
//		if (is_a($entries, 'MarcParseError')) {
//			return $entries;
//		}
//echo "MARC record entries: ";print_r($entries);echo "<br />";
//echo "record directories OK.<br />";
//return;
		foreach ($entries as $e) {
			$f = substr($rec, $base+$e['start'], $e['length']);
			$field = $this->parseField($e['tag'], $f);
//			if (is_a($field, 'MarcParseError')) {
//				return $field;
//echo "MARC err: ";print_r($field);echo"<br />";
//			}
			array_push($r->fields, $field);
		}
//echo "MARC Record: ";print_r($r);echo"<br /><br />";
		return $r;
	}

	private function parseDirectory($directory) {
//echo "in MarcParser::parseDirectory()<br />";
//echo "MARC record dir: ";print_r($directory);echo "<br />";
//return array();
		if (!$this->lenient and $directory{strlen($directory)-1} != MARC_FT) {
			return $this->_error('directory unterminated');
		}
//echo "directory termination OK.<br />";
//return array();

		$directory = substr($directory, 0, -1);
		$emap = array(
			'tag' => 3,
			'length' => 4,
			'start' => 5,
		);
		$entry_len = $emap['tag'] + $emap['length'] + $emap['start'];
		$dirLen = strlen($directory);
//echo "entryLen = $entry_len; dirLen = $dirLen<br />";
//return array();
		if ($dirLen % $entry_len != 0) {
			//echo "Dir Len: ".$dirLen." SHOULD BE a multiple of $entry_len";
			$mult = round(($dirLen / $entry_len), 0, PHP_ROUND_HALF_DOWN); // get integer result
			$dirLen = $entry_len * $mult;
//echo "Dir len forced to $dirLen<br />";
		}
//return array();

//echo "making directory pointer array<br />";
		$entries=array();
		$directory = substr($directory, 0, $dirLen);
		while (strlen($directory)) {
			$e = array();
			$e['tag'] = substr($directory, 0, $emap['tag']);
			$p = $emap['tag'];
			foreach (array('length', 'start') as $f) {
				$s = substr($directory, $p, $emap[$f]);
//				if (!ctype_digit($s)) {
//					return self._error('non-numeric '.$f.' field in directory entry '.count(entries));
//					echo "bad field in directory entry $s, #" . count(entries) . "<br />";
//				}
				$e[$f] = $s;
				$p += $emap[$f];
			}
			array_push($entries, $e);
			$directory = substr($directory, $p);
		}
//echo "Dir Entries: ";print_r($entries);echo "<br />";
		return $entries;
	}

	private function parseField($tag, $field) {
//echo "in MarcParser::parseField()<br />";
//echo "working tag ".$tag."<br />";
		if (!$this->lenient and $field{strlen($field)-1} != MARC_FT) {
			return $this->_error('variable field unterminated: '+$field);
		}
		$field = substr($field, 0, -1);

		if (substr($tag, 0, 2) == '00') {
			return new MarcControlField($tag, $field);
		}
//echo "raw field data: ";print_r($field);echo "<br />";

		# 2 is the number of indicators
		$f = new MarcDataField($tag, substr($field, 0, 2));
//echo "field data: ";print_r($f);echo "<br />";
		$field = substr($field, 2);
//echo "using as delimiter: ";print_r(MARC_DELIMITER);echo "<br />";
//		if ($field{0} != MARC_DELIMITER) {
//			return $this->_error("missing delimiter in ".$f->tag." field, got '".$field."' instead");
//		}

		$elems = explode(MARC_DELIMITER, $field);
		# Elements begin with a delimiter, but we treat it as
		# a separator, so the first one will always be empty and
		# is discarded.
		array_shift($elems);
		$f->subfields = array();
		foreach ($elems as $e) {
			# $e{0} is the subfield code
			array_push($f->subfields, new MarcSubfield($e{0}, substr($e, 1)));
		}
		return $f;
	}

	/* ........................ */
	protected function eof() {
		if (!$this->lenient and strlen($this->unparsed) > 0) {
			return new MarcParseError('trailing junk or incomplete record at end of file');
		}
		$this->recnum = 0;
		return 0;
	}
	protected function error($s) {
		return new MarcParseError($s, $this->recnum);
	}
}

/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
class MarcMnemParser extends MarcBaseParser {
	public function __construct($lenient=True) {
		//$this->MarcBaseParser($lenient);
		parent::__construct($lenient);
		$this->_line = 0;
		$this->_rec = NULL;
		$this->_field = NULL;
		$this->recnum = 1;
	}

	public function parse($input = "") {
		$old_len = count($this->records);
		$data = str_replace("\r", "", $this->unparsed);
		$lines = explode("\n", $data);
		$this->unparsed = '';
		if (count($lines)) {
			# The last element is a partial line or an empty string.
			$this->unparsed = array_pop($lines);
		}
		foreach ($lines as $l) {
			// Correct for explode() removing the newlines.
			$l .= "\n";
			if ($l{0} == '#') {
				// Comment
			} else if ($l{0} == '=') {
				$err = $this->_addField($this->_field);
				if (is_a($err, 'MarcParseError')) {
					return $err;
				}
				$this->_field = $l;
			} else if (trim($l) == '') {
				if ($this->_field) {
					$err = $this->_addField($this->_field);
					if (is_a($err, 'MarcParseError')) {
						return $err;
					}
					$this->_field = NULL;
				}
				if ($this->_rec) {
					array_push($this->records, $this->_rec);
					$this->recnum += 1;
					$this->_rec = NULL;
				}
			} else if (!$this->_field) {
				return $this->_error("extra garbage outside of fields");
			} else {
				$this->_field .= $l;
			}
			$this->_line += 1;
		}
		return count($this->records)-$old_len;
	}

	public function _addField($field) {
		if (!$field) {
			return;
		}
		if ($field{0} != '=') {
			return $this->_error("can't happen: non-field data in _field");
		}
		$field = rtrim($field, "\r\n");		# lose final newline
		if (strlen($field) < 4) {
			return $this->_error("field too short");
		}
		$tag = substr($field, 1, 3);
		if (substr($field, 4, 2) != '  ') {
			return $this->_error("two spaces must separate the tag from field data");
		}
		if (!$this->_rec) {
			$this->_rec = new MarcRecord();
		}

		# Set leader
		if (preg_match("/^(000|LDR)$/i", $tag)) {
			$ldr = MarcHelpers::fromMnem(substr($field, 6));
			$err = $this->_rec->setLeader($ldr, $this->lenient);
			if ($err) {
				return $this->_error("Invalid Leader: ".$err);
			}
			return;
		}

		if (substr($tag, 0, 2) == '00') {
			$data = MarcHelpers::fromMnem(substr($field, 6));
			$f = new MarcControlField($tag, $data);
		} else {
			$ind = MarcHelpers::fromMnem(substr($field, 6, 2));
			$f = new MarcDataField($tag, $ind);
			$data = substr($field, 8);
			$subs = explode('$', $data);
			# Subfields begin with a delimiter, but we treat it as
			# a separator, so the first one will always be empty (or
			# junk) and is discarded.
			array_shift($subs);
			$f->subfields = array();
			foreach ($subs as $s) {
				$d = MarcHelpers::fromMnem(substr($s, 1));
				# $s{0} is the subfield code
				array_push($f->subfields, new MarcSubfield($s{0}, $d));
			}
		}
		array_push($this->_rec->fields, $f);
		return;
	}

	/* .......................... */
	public function error($s) {
		return new MarcParseError($s, $this->recnum, $this->_line);
	}
	public function eof() {
		$this->unparsed .= "\n\n";
		$n = $this->parse();
		if (is_a($n, 'MarcParseError')) {
			return $n;
		}
		$this->recnum = 1;
		if ($this->_rec != NULL) {
			array_push($this->records, $this->_rec);
			$this->_rec = NULL;
			return $n+1;
		}
		return $n;
	}
}
