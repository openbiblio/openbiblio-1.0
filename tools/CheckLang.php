<?php

/*
 * CheckLang Class
 *
 *   Checks for orphaned language strings in the whole code base and
 *   duplicate language definitions in the specified language file.
 *
 * Usage:
 *   $check = new CheckLang;
 *   $check->run([path, localization, filename]);
 *
 *   Optional parameters:
 *
 *   path - default: '../' but could be '../../openbiblio-test/' if you have
 *          more than one installation of OpenBiblio
 *   localization - the name of the localization directory, default: 'en'
 *   filename - the name of the language file to check, default: 'trans'
 *
 */

class CheckLang {
	private $trans = array();
	private $path;
	private $pattern;
	private $orphanedStrings;
	private $orphanedKeys;
	private $duplicates;
	private $loc;
	private $file;
	private $usedKeys = array();

	function __construct() {
		$this->path = '../';
		$this->pattern = '/php$/';
		$this->orphanedStrings = false;
		$this->orphanedKeys = false;
		$this->duplicates = false;
		$this->orphanedKey = true;
	}

	function run($path = '', $loc = 'en', $file = 'trans') {
		if (!empty($path)) {
			$this->path = rtrim($path, '/').'/';
		}
		$this->loc = $loc;
		$this->file = rtrim($file, '.php');
		include_once($this->path.'locale/'.$this->loc.'/'.$this->file.'.php');
		$this->trans = array_merge($this->trans, $trans);

		echo '<html><head><title></title><style type="text/css">h1 {font: 20px/1.5 arial;} table {border-collapse: collapse;} th, td {font: 11px/1.1 verdana; text-align: left; vertical-align: top; border: 1px solid #aaa; padding: 2px;} th {font-weight: bold; background-color: #ddd;}</style></head><body>';

		echo '<h1>Orphaned language strings</h1>';
		echo '<table>';
		echo '<tr><th>File</th><th>#</th><th>Key</th></tr>';
		$this->findOrphanedStrings($this->path);
		if (!$this->orphanedStrings) {
			echo '<tr><td colspan="3">No orpahaned strings found</td></tr>';
		}
		echo '</table>';

		echo '<h1>Duplicate language definitions</h1>';
		echo '<table>';
		echo '<tr><th>#</th><th>Line</th></tr>';
		$this->findDuplicates();
		if (!$this->duplicates) {
			echo '<tr><td colspan="2">No duplicates found</td></tr>';
		}
		echo '</table>';

		echo '<h1>Orphaned langugage keys in '.$this->file.'.php</h1>';
		echo '<table>';
		echo '<tr><th>Key</th></tr>';
		$this->findOrphanedKeys();
		if (!$this->orphanedKeys) {
			echo '<tr><td>No orphaned keys found</td></tr>';
		}
		echo '</table>';

		echo '</body></html>';
	}

	private function findOrphanedStrings($path) {
		$path = rtrim(str_replace("\\", "/", $path), '/') . '/';
		$entries = Array();
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			$entries[] = $entry;
		}
		$dir->close();
		foreach ($entries as $entry) {
			$fullname = $path . $entry;
			if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
				$this->findOrphanedStrings($fullname);
			} else if (is_file($fullname) && preg_match($this->pattern, $entry)) {
				$this->checkStrings($fullname);
			}
		}
	}

	private function checkStrings($fullname) {
		$lines = file($fullname);
		foreach ($lines as $line_num => $line) {
			preg_match_all("|T\([\'\"]{1}(.*)[\"\']{1}[\,\)]{1}|U", $line, $out, PREG_PATTERN_ORDER);
			foreach ($out[1] as $key) {
				$key = str_replace('\"','"',$key);
				$key = str_replace("\'","'",$key);
				$key = str_replace('\$','$',$key);
			  $this->usedKeys[] = $key;
				if (!array_key_exists($key, $this->trans)) {
				  $this->orphanedStrings = true;
				  $key = str_replace("<", "&lt;", $key);
				  $key = str_replace(">", "&gt;", $key);
					echo '<tr><td>'.ltrim($fullname, './').'</td><td>'.($line_num + 1).'</td><td>'.$key.'</td></tr>';
				}
			}
		}
	}

	private function findOrphanedKeys() {
		foreach ($this->trans as $key => $value) {
			if (!in_array($key, $this->usedKeys)) {
				$this->orphanedKeys = true;
				echo '<tr><td>'.$key.'</td></tr>';
			}
		}
	}

	private function findDuplicates() {
		$arrKeys = array();
		$arrValues = array();
		$lines = file($this->path.'locale/'.$this->loc.'/'.$this->file.'.php');

		foreach ($lines as $line_num => $line) {
			if (substr($line, 0, 1) != " " && substr($line, 0, 1) != "#" && substr($line,0,1) != "<" && substr($line, 0, 1) != "?" && substr($line, 0, 1) != "\n") {
				if (strpos($line, "]") > 1) {
					list($key, $value) = explode("]",$line);
					$key = str_replace("\$trans[\"", "", $key);
					$key = str_replace("\"", "", $key);
					if (in_array($key, $arrKeys, true)) {
						$row = array_search($key ,$arrKeys);
						$pointout = '<b>'.$key.'</b>';
						$showline = str_replace($key, $pointout, $lines[$row - 1]);
						echo '<tr><td>'.$row.'</td><td>'.$showline.'</td></tr>';
						echo '<tr><td>'.($line_num + 1).'</td><td><em>'.$lines[$line_num].'</em></td></tr>';
						$this->duplicates = true;
					} else {
						$arrKeys[$line_num + 1] = $key;
					}
					$value = str_replace('= "', '', $value);
					$value = str_replace("= '", "", $value);
					$value = str_replace('";', '', $value);
					$value = str_replace("';", "", $value);
					$value = trim($value);
					if (in_array($value, $arrValues, true)) {
						$row = array_search($value ,$arrValues);
						$pointout = '<b>'.$value.'</b>';
						$showline = str_replace($value, $pointout, $lines[$row - 1]);
						echo '<tr><td>'.$row.'</td><td>'.$showline.'</td></tr>';
						echo '<tr><td>'.($line_num + 1).'</td><td><em>'.$lines[$line_num].'</em></td></tr>';
						$this->duplicates = true;
					} else {
						$arrValues[$line_num + 1] = $value;
					}
				}
			}
		}
	}

}

$check = new CheckLang;
$check->run();
