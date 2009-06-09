<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/*********************************************************************************
 * Displays the error page
 * @param Query $query Query object containing query error parameters.
 * @return void
 * @access public
 *********************************************************************************
 */
function displayErrorPage($query){
	echo "\n<pre>db_errno = ".htmlspecialchars($query->getDbErrno())."\n";
	echo "db_error = ".htmlspecialchars($query->getDbError())."\n";
	echo "SQL = ".htmlspecialchars($query->getSQL())."\n</pre>";
	exit($query->getError());
}
