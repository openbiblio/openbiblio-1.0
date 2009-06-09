<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../shared/global_constants.php"));
require_once(REL(__FILE__, "../classes/Query.php"));

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

class InstallQuery extends Query {
	/* Override constructor so the installer can test the database connection */
	function InstallQuery() {
		;
	}
	function dropTable($tableName) {
		$sql = $this->mkSQL("DROP TABLE IF EXISTS %I ", $tableName);
		$this->act($sql);
	}

	function renameTables($fromTablePrfx, $toTablePrfx = DB_TABLENAME_PREFIX) {
		$fromTableNames = $this->getTableNames($fromTablePrfx.'%');
		foreach($fromTableNames as $fromTableName) {
			$toTableName = str_replace($fromTablePrfx, $toTablePrfx, $fromTableName);
			$this->renameTable($fromTableName, $toTableName);
		}
	}

	function renameTable($fromTableName, $toTableName) {
			$this->dropTable($toTableName);
			$sql = "RENAME TABLE ".$fromTableName." TO ".$toTableName;
			$this->act($sql);
	}

	function getTableNames($pattern = "") {
		if($pattern == "") {
			$pattern = DB_TABLENAME_PREFIX.'%';
		}
		$sql = "SHOW TABLES LIKE '".$pattern."'";
		$rows = $this->select($sql, OBIB_NUM);

		$tablenames = array();
		while ($row = $rows->next()) {
			# SHOW TABLES doesn't always use the same column name
			$row = array_values($row);
			$tablenames[] = $row[0];
		}
		return $tablenames;
	}

	function _getSettings($tablePrfx) {
		$sql = $this->mkSQL('SHOW TABLES LIKE %Q ', $tablePrfx.'settings');
		$row = $this->select01($sql);
		if (!$row) {
			return false;
		}
		$sql = $this->mkSQL('SELECT * FROM %I ', $tablePrfx.'settings');
		$res = $this->select($sql);
		/* Older versions worked like this. */
		if ($res->count() == 1) {
			return $res->next();
		}

		$settings = array();
		while ($s = $res->next()) {
			$settings[$s['name']] = $s['value'];
		}
		return $settings;
	}

	function getCurrentLocale($tablePrfx = DB_TABLENAME_PREFIX) {
		$array = $this->_getSettings($tablePrfx);
		if($array == false ||
			!isset($array["locale"])) {
			return 'en'; //Earlier versions of Openbiblio only supported English
		}
		else {
			return $array["locale"];
		}
	}

	function getCurrentDatabaseVersion($tablePrfx = DB_TABLENAME_PREFIX) {
		$array = $this->_getSettings($tablePrfx);
		if($array == false) {
			return false;
		}
		else {
			return $array["version"];
		}
	}

	function freshInstall($locale, $sampleDataRequired = false, $version=OBIB_LATEST_DB_VERSION, $tablePrfx = DB_TABLENAME_PREFIX) {
		$rootDir = '../install/' . $version . '/sql';
		$localeDir = '../locale/' . $locale . '/sql/' . $version;

		$this->executeSqlFilesInDir($rootDir, $tablePrfx);
		$this->executeSqlFilesInDir($localeDir . '/domain/', $tablePrfx);
		if($sampleDataRequired) {
			$this->executeSqlFilesInDir($localeDir . '/sample/', $tablePrfx);
		}
	}

	function executeSqlFilesInDir($dir, $tablePrfx = "") {
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($filename = readdir($dh)) !== false) {
					if(ereg('\\.sql$', $filename)) {
						$this->executeSqlFile($dir.'/'.$filename, $tablePrfx);
					}
				}
				closedir($dh);
			}
		}
	}

	/**********************************************************************************
	 * Read through an sql file executing SQL only when ";\n" or "\r\n" is encountered
	 **********************************************************************************/
	function executeSqlFile($filename, $tablePrfx = DB_TABLENAME_PREFIX) {
		$str = file_get_contents($filename);
		if ($str === false) {
			Fatal::error("Error reading file: ".H($filename));
		}
		$str = str_replace("\r", "", $str);
		foreach (explode(";\n", $str) as $sql) {
			$sql = trim($sql);
			if ($sql == "") {
				continue;
			}
			//replace table prefix, we're doing it here as the install script may
			//want to override the required prefix (eg. during upgrade / conversion
			//process)
			$sql = str_replace("%prfx%",$tablePrfx,$sql);
			if (strncasecmp("select", $sql, 6) == 0) {
				$this->select($sql);
			} else {
				$this->act($sql);
			}
		}
	}
}
