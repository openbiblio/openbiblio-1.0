<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class enMetaData {
	function enMetaData() {
		$this->locale_description = "English";
	}
	function pluralForm($n) {
		if ($n == 1 or $n == -1) {
			return 'singular';
		} else {
			return 'plural';
		}
	}
	function moneyFormat($amount) {
		if ($amount < 0) {
			return sprintf("(-$%.2f)", abs($amount));
		} else {
			return sprintf("$%.2f", $amount);
		}
	}
}
