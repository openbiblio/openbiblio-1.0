<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

$_Query_lock_depth = 0;

class Query {
	var $_link;

	/* This constructor will never do more than call connect_e() and throw a
	 * fatal error if it fails.  If you want to catch the error, subclass Query and
	 * call connect_e() yourself.
	 */
	function Query() {
		$e = $this->connect_e();
		if ($e) {
			Fatal::dbError($e->sql, $e->msg, $e->dberror);
		}
	}
	function connect_e() {
		list($this->_link, $e) = Query::_connect_e();
		return $e;
	}
	/* This static method shares the actual DBMS connection
	 * with all Query instances.
	 */
	function _connect_e() {
		static $link;
		if (!isset($link)) {
			if (!function_exists('mysql_connect')) {
				return array(NULL, new DbError(T("Checking for MySQL Extension..."), T("Unable to connect to database."), T("The MySQL extension is not available")));
			}
			$link = mysql_connect(OBIB_HOST,OBIB_USERNAME,OBIB_PWD);
			if (!$link) {
				return array(NULL, new DbError(T("Connecting to database server..."), T("Cannot connect to database server."), mysql_error()));
			}
			$rc = mysql_select_db(OBIB_DATABASE, $link);
			if (!$rc) {
				return array(NULL, new DbError(T("Selecting database..."), T("Cannot select database."), mysql_error($link)));
			}
			// Set to UTF8 characterset. This is NOT the prefered way, but mysql_set_charset is not supported until PHP 5.2.3 - LJ
			$version = explode('.', phpversion());
			if($version[0] >= 5 AND $version[1] >= 2 AND $version[2] >= 3){
				mysql_set_charset("utf8");
			} else {
				mysql_query("set names utf8");
			}			
		}
		return array($link, NULL);
	}

	function act($sql) {
		$this->lock();
		$results = $this->_act($sql);
		$this->unlock();
		if (!is_bool($results)) {
			Fatal::dbError($sql, T("Action query returned results."), T("No error."));
		}
	}
	function select($sql) {
		$results = $this->_act($sql);
		if (is_bool($results)) {
			Fatal::dbError($sql, T("Select did not return results."), T("Nothing Found error."));
		}
		return new DbIter($results);
	}
	function select1($sql) {
		$r = $this->select($sql);
		if ($r->count() != 1) {
			Fatal::dbError($sql, T('QueryWrongNrRows', array('count'=>$r->count())), T("Nothing Found error."));
		} else {
			return $r->next();
		}
	}
	function select01($sql) {
		$r = $this->select($sql);
		if ($r->count() == 0) {
			return NULL;
		} else if ($r->count() != 1) {
			Fatal::dbError($sql, T('QueryWrongNrRows', array('count'=>$r->count())), T("Wrong Number Found error."));
		} else {
			return $r->next();
		}
	}
	function _act($sql) {
		if (!$this->_link) {
			Fatal::internalError(T('QueryBeforeConnect'));
		}
		$r =  mysql_query($sql, $this->_link);
		if ($r === false) {
			Fatal::dbError($sql, T("Database query failed"), mysql_error());
		}
		return $r;
	}

	/* This is not easily portable to many SQL DBMSs.  A better scheme
	 * might be something like PEAR::DB's sequences.
	 */
	function getInsertID() {
		return mysql_insert_id($this->_link);
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
	function lock() {
		global $_Query_lock_depth;
		if ($_Query_lock_depth < 0) {
			Fatal::internalError(T("Negative lock depth"));
		}
		if ($_Query_lock_depth == 0) {
			$row = $this->select1($this->mkSQL('select get_lock(%Q, %N) as locked',
				OBIB_LOCK_NAME, OBIB_LOCK_TIMEOUT));
			if (!isset($row['locked']) or $row['locked'] != 1) {
				Fatal::cantLock();
			}
		}
		$_Query_lock_depth++;
	}
	function unlock() {
		global $_Query_lock_depth;
		if ($_Query_lock_depth <= 0) {
			Fatal::internalError(T("Tried to unlock an unlocked database."));
		}
		$_Query_lock_depth--;
		if ($_Query_lock_depth == 0) {
			$row = $this->select1($this->mkSQL('select release_lock(%Q) as unlocked',
				OBIB_LOCK_NAME));
			if (!isset($row['unlocked']) or $row['unlocked'] != 1) {
				Fatal::internalError(T("Can't release lock"));
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
					$SQL .= "'".mysql_real_escape_string($arg, $this->_link)."'";
					break;
				case 'q':
					$SQL .= mysql_real_escape_string($arg, $this->_link);
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
