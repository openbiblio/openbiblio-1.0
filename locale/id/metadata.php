<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * this class implements locale-specific formats, etc.
 * @author Jane Sandberg
 */

## TODO FIXME needs to be replaced by global INTL class

class idMetaData {
	public function __construct() {
		$this->locale_description = "Bahasa Indonesia";
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
			return sprintf("(-Rp %.2f)", abs($amount));
		} else {
			return sprintf("Rp %.2f", $amount);
		}
	}
}
