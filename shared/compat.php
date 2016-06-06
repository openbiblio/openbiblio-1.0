<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Functions for compatibility with older PHP versions. */
if (!function_exists('file_get_contents')) {
	function file_get_contents ($filename) {
		$s = "";
		$f = @fopen($filename, "r");
		if (!$f) {
			return false;
		}
		while (!feof($f)) {
			$s .= fgets($f, 4096);
		}
		fclose ($f);
		return $s;
	}
}

if (!function_exists('ctype_alnum')) {
	function ctypeAlnum($text){
		preg_match("/[a-zA-Z0-9]+/",$text,$regs);
		if (count($regs) == 0) {
			return false;
		}
		if ($regs[0] == $text) {
			return true;
		}
		return false;
	}
}
