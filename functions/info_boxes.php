<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Members.php"));

function currentMbrBox() {
	if (isset($_SESSION['currentMbrid'])) {
		$members = new Members;
		$mbr = $members->maybeGetOne($_SESSION['currentMbrid']);
		if (!$mbr) {
			unset($_SESSION['currentMbrid']);
			return;
		}
		echo HTML('<div class="current_mbr">{#trans}Current Patron:{#end} '
			. ' <a href="../circ/memberForms.php?mbrid={mbrid|url-param-value}">'
			. '{first_name} {last_name} ({barcode_nmbr})</a></div>',
			$mbr);
	}
}
