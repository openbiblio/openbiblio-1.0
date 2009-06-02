<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Non-fatal errors
 *
 * This class allows errors to be reported to the user.
 * If an error is to be caught and handled by other code, derive a
 * class from this so that the code can detect the error with is_a().
 *
 * toStr() is intended for end-user consumption.
 *
 * By convention all functions returning error objects have names
 * ending in '_e'.
 */
class Error {
  function Error($msg) {
    $this->msg = $msg;
  }
  function toStr() {
    return $this->msg;
  }
}

/* Most DB errors are fatal, but we sometimes have to catch them. */
class DbError extends Error {
  /* The attributes here are public. */
  var $sql;
  var $msg;
  var $dberror;
  function DbError($sql, $msg, $dberror) {
    $this->sql = $sql;
    $this->msg = $msg;
    $this->dberror = $dberror;
  }
  function toStr() {
    $s = $this->msg.': '.$this->dberror;
    if ($this->sql) {
      $s .= ' -- FULL SQL: '.$this->sql;
    }
    return $s;
  }
}

/* Fatal Errors */
class Fatal {
  /* Override default behaviour, e.g. for supressing errors, unit testing, etc. */
  function setHandler(&$obj) {
    global $_Error_FatalHandler;
    $old =& $_Error_FatalHandler;
    $_Error_FatalHandler = $class;
    return $old;
  }
  /* "Can't happen" states */
  function internalError($msg) {
    global $_Error_FatalHandler;
    if (method_exists($_Error_FatalHandler, 'internalError')) {
      $_Error_FatalHandler->internalError($msg);
    } else {
      Fatal::error('Internal Error: '.$msg);
    }
  }
  /* Query errors */
  function dbError($sql, $msg, $dberror) {
    global $_Error_FatalHandler;
    if (method_exists($_Error_FatalHandler, 'dbError')) {
      $_Error_FatalHandler->dbError($sql, $msg, $dberror);
    } else {
      Fatal::error('Database Error: '.$msg.' in query: '.$sql.' DBMS says: '.$dberror);
    }
  }
  /* Generic */
  function error($msg) {
    global $_Error_FatalHandler;
    if (method_exists($_Error_FatalHandler, 'error')) {
      $_Error_FatalHandler->error($msg);
    } else {
      die($msg);
    }
  }
}

/* error is the only required method */
class FatalHandler {
  /* FIXME - Internationalize this stuff */
  function internalError($msg) {
    echo "<h1>Internal Error - You've Probably Found a Bug</h1>\n";
    echo "<p>Please give all the information on this page to your support personnel.</p>\n";
    echo "<p>".H($msg)."</p>\n";
    $this->printBackTrace();
    exit(1);
  }
  function dbError($sql, $msg, $dberror) {
    echo "<h1>Database Query Error - You've Probably Found a Bug</h1>\n";
    echo "<h2>".H($msg)."</h2>\n";
    echo "<p>Please give all the information on this page to your support personnel.</p>\n";
    echo "<p>Query ".H($sql)." failed.  The DBMS said this:</p>\n";
    echo "<pre>".H($dberror)."</pre>";
    $this->printBackTrace();
    exit(1);
  }
  function error($msg) {
    echo "<h1>Fatal Error</h1>\n";
    echo "<h2>".H($msg)."</h2>\n";
    $this->printBackTrace();
    exit(1);
  }
  function printBackTrace() {
    if (function_exists('debug_backtrace')) {
      echo "<h2>Debug Backtrace (most recent call first):</h2>\n";
      echo '<pre>';
      foreach(debug_backtrace() as $frame) {
        # As usual, PHP makes things more complicated.  This time by
        # deciding that all elements of the stack frame are optional.  Sigh.
        if (isset($frame['file'])) {
          echo H($frame['file'].':');
        } else {
          echo '?file?:';
        }
        if (isset($frame['line'])) {
          echo H($frame['line'].' ');
        } else {
          echo '?line? ';
        }
        if (isset($frame['class']) and isset($frame['type'])) {
          echo H($frame['class'].$frame['type']);
        }
        if (isset($frame['function'])) {
          echo H($frame['function'].'(');
          if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $a) {
              array_push($args, var_export($a, true));
            }
            echo H(implode($args, ', '));
          } else {
            echo '???';
          }
          echo ')';
        }
        echo "\n";
      }
      echo '</pre>';
    }
  }
}

$_Error_FatalHandler = new FatalHandler;

?>
