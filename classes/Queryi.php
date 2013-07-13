<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

$_Query_lock_depth = 0;

class Queryi extends mysqli{
	function __construct() {
		$this->lockDepth = 0;
		parent::__construct(OBIB_HOST,OBIB_USERNAME,OBIB_PWD,OBIB_DATABASE);
		if(mysqli_connect_error()) {
			return array(NULL, new DbError(T("Connecting to database server..."), T("Cannot connect to database server."), mysql_error()));
		}
		return;
	}
	function act($sql) {
		//$this->lock();
		$results = $this->_act($sql);
		//$this->unlock();
		return $results;
	}
	function select($sql) {
		$results = $this->_act($sql);
		if (is_bool($results)) {
			Fatal::dbError($sql, T("Select did not return results."), T("NothingFoundError"));
		}
		return $results;
	}
	function select1($sql) {
		$r = $this->select($sql);
		if ($r->num_rows != 1) {
			Fatal::dbError($sql, T("QueryWrongNrRows", array('count'=>$r->num_rows)), T("NothingFoundError"));
		} else {
			return $r->fetch_assoc();
		}
	}
	function select01($sql) {
		$r = $this->select($sql);
		//if ($r->count() == 0) {
		if ($r->num_rows == 0) {
			return NULL;
		//} else if ($r->count() != 1) {
		} else if ($r->num_rows != 1) {
			//Fatal::dbError($sql, T("QueryWrongNrRows", array('count'=>$r->count())), T("Wrong Number Found error."));
			Fatal::dbError($sql, T("QueryWrongNrRows", array('count'=>$r->num_rows)), T("Wrong Number Found error."));
		} else {
			//return $r->fetch_assoc();
			return $r->fetch_assoc();
		}
	}
	function _act($sql) {
		$r =  parent::query($sql);
		if ($r === false) {
			Fatal::dbError($sql, T("Database query failed"), mysql_error());
		}
		return $r;
	}

	/* This is not easily portable to many SQL DBMSs.  A better scheme
	 * might be something like PEAR::DB's sequences.
	 */
	function getInsertID() {
		//return mysql_insert_id($this->_link);
		return $this->insert_id;
	}

	/* Locking functions
	 *
	 * Besides switching to InnoDB for transactions, I haven't been able to
	 * come up with a good way to do locking reliably.  For now, we'll get
	 * and release one big advisory lock around every sensitive transaction
	 * and every database write except per-session data.  This should make
	 * everything work, even if it is heavy-handed.
	 *
	 * Calls to lock/unlock may be nested, but must be paired.
	 */

	function clearLocks () {
//echo "clearing all DB locks!<br />\n";
		$this->_act('set global read_only = off');
		$this->_act('unlock tables');
	}

	function lock() {
//		global $_Query_lock_depth;
		if ($this->lockDepth < 0) {
			Fatal::internalError(T("Negative lock depth"));
		}
		if ($this->lockDepth == 0) {
			$row = $this->select1($this->mkSQL('select get_lock(%Q, %N) as locked',
				OBIB_LOCK_NAME, OBIB_LOCK_TIMEOUT));
			if (!isset($row['locked']) or $row['locked'] != 1) {
				Fatal::cantLock();
			}
		}
		$this->lockDepth++;
//echo "locking; depth=$this->lockDepth\n";
	}
	function unlock() {
//		global $_Query_lock_depth;
		if ($this->lockDepth <= 0) {
			Fatal::internalError(T("Tried to unlock an unlocked database."));
		}
		$this->lockDepth--;
//echo "un-locking; depth=$this->lockDepth\n";
		if ($this->lockDepth == 0) {
			$row = $this->select1($this->mkSQL('select release_lock(%Q) as unlocked',
				OBIB_LOCK_NAME));
			if (!isset($row['unlocked']) or $row['unlocked'] != 1) {
				Fatal::internalError(T("Cannot release lock"));
			}
		}
	}

	/****************************************************************************
	 * Makes SQL by interpolating values into a format string.
	 * This function works something like printf() for SQL queries.  Format
	 * strings contain %-escapes signifying that a value from the argument
	 * list should be inserted into the string at that point.  The routine
	 * properly quotes or transforms the value to make sure that it will be
	 * handled correctly by the SQL server.  The recognized format strings
	 * are as follows:
	 *  %% - is replaced by a single '%' character and does not consume a
	 *       value form the argument list.
	 *  %! - inserts the argument in the query unaltered -- BE CAREFUL!
	 *  %B - treates the argument as a boolean value and inserts either
	 *       'Y' or 'N'as appropriate.
	 *  %C - treats the argument as a column reference.  This differs from
	 *       %I below only in that it passes the '.' operator for separating
	 *       table and column names on to the SQL server unquoted.
	 *  %I - treats the argument as an identifier to be quoted.
	 *  %i - does the same escaping as %I, but does not add surrounding
	 *       quotation marks.
	 *  %N - treats the argument as a number and strips off all of it but
	 *       an initial numeric string with optional sign and decimal point.
	 *  %Q - treats the argument as a string and quotes it.
	 *  %q - does the same escaping as %Q, but does not add surrounding
	 *       quotation marks.
	 * @param string $fmt format string
	 * @param string ... optional argument values
	 * @return string the result of interpreting $fmt
	 * @access public
	 ****************************************************************************
	 */
	function mkSQL() {
		$badSqlFmt = T("Bad mkSQL() format string.");
		
		$n = func_num_args();
		if ($n < 1) {
			Fatal::internalError(T("Not enough arguments given to mkSQL()."));
		}
		$i = 1;
		$SQL = "";
		$fmt = func_get_arg(0);
		while (strlen($fmt)) {
			$p = strpos($fmt, "%");
			if ($p === false) {
				$SQL .= $fmt;
				break;
			}
			$SQL .= substr($fmt, 0, $p);
			if (strlen($fmt) < $p+2) {
				Fatal::internalError($badSqlFmt);
			}
			if ($fmt{$p+1} == '%') {
				$SQL .= "%";
			} else {
				if ($i >= $n) {
					Fatal::internalError(T("Not enough arguments given to mkSQL()."));
				}
				$arg = func_get_arg($i++);
				switch ($fmt{$p+1}) {
				case '!':
					/* very dangerous, but sometimes very useful -- be careful */
					$SQL .= $arg;
					break;
				case 'B':
					if ($arg) {
						$SQL .= "'Y'";
					} else {
						$SQL .= "'N'";
					}
					break;
				case 'C':
					$a = array();
					foreach (explode('.', $arg) as $ident) {
						array_push($a, '`'.$this->_ident($ident).'`');
					}
					$SQL .= implode('.', $a);
					break;
				case 'I':
					$SQL .= '`'.$this->_ident($arg).'`';
					break;
				case 'i':
					$SQL .= $this->_ident($arg);
					break;
				case 'N':
					$SQL .= $this->_numstr($arg);
					break;
				case 'Q':
					//$SQL .= "'".mysql_real_escape_string($arg, $this->_link)."'";
					$SQL .= "'".parent::real_escape_string($arg)."'";
					break;
				case 'q':
					//$SQL .= mysql_real_escape_string($arg, $this->_link);
					$SQL .= parent::real_escape_string($arg);
					break;
				default:
					Fatal::internalError($badSqlFmt);
				}
			}
			$fmt = substr($fmt, $p+2);
		}
		if ($i != $n) {
			Fatal::internalError(T("Too many arguments to mkSQL()."));
		}
		return $SQL;
	}

	function _ident($i) {
		# Because the MySQL manual is unclear on how to include a ` in a `-quoted
		# identifer, we just drop them.  The manual does not say whether backslash
		# escapes are interpreted in quoted identifiers, so I assume they are not.
		return str_replace('`', '', $i);
	}
	function _numstr($n) {
		if (preg_match("/^([+-]?[0-9]+(\.[0-9]*)?([Ee][0-9]+)?)/", (string)$n, $subs)) {
			return $subs[1];
		} else {
			return "0";
		}
	}
}

/*
class DbIter extends Iter {
	function DbIter($results) {
		$this->results = $results;
	}
	function count() {
		return mysql_num_rows($this->results);
	}
	function next() {
		$r = mysql_fetch_assoc($this->results);
		if ($r === false) {
			return NULL;
		}
		return $r;
	}
}
*/
