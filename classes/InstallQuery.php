<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/global_constants.php");
require_once("../classes/Queryi.php");

/**
 * This class provides an interface for DB functions unique to OB installation
 * @author Fred LaPlante
 */

class InstallQuery extends Queryi {
  public function __construct() {
    parent::__construct();
  }
  
	public function getDbServerVersion () {
		$sql = $this->mkSQL("select VERSION()");
    $rslt = $this->select1($sql);
		return $rslt['VERSION()'];
	}

  public function getSettings($tablePrfx = OBIB_DATABASE) {
    //$sql = $this->mkSQL('SHOW TABLES LIKE %C ', $tablePrfx.'.settings');
    //$rows = $this->select01($sql);
    $sql = $this->mkSQL('SELECT * FROM %C ', $tablePrfx.'.settings');
//echo "sql={$sql}<br/>\n";
    $rows = $this->select($sql);
    if (!$rows || empty($rows) || !isset($rows)) {
      return 'NothingFound';
    }
//    $sql = $this->mkSQL('SELECT * FROM %C ', $tablePrfx.'.settings');
//echo "sql={$sql}<br/>\n";
//    return $this->select($sql);
  }

  public function getCurrentDatabaseVersion($tablePrfx = OBIB_DATABASE) {
    $sql = $this->mkSQL('SELECT `value` FROM %i WHERE `name`=%Q', $tablePrfx.'settings','version');
    $row = $this->select01($sql);
		if ($row) {
			$version = $row['value'];
		} else {
    	$sql = $this->mkSQL('SELECT `version` FROM %i ', $tablePrfx.'.settings');
    	$rslt = $this->select01($sql);
			$version = $rslt['version'];
		}
		return $version;
  }

	## ------------------------------------------------------------------------ ##
  protected function getCurrentLocale($tablePrfx = DB_TABLENAME_PREFIX) {
    $sql = $this->mkSQL('SELECT * FROM %I WHERE `name`=%Q', $tablePrfx.'.settings','locale');
    $row = $this->select01($sql);
		return $row['value'];
  }

  protected function renameTables($fromTablePrfx, $toTablePrfx = DB_TABLENAME_PREFIX) {
    $fromTableNames = $this->getTableNames($fromTablePrfx.'%');
    foreach($fromTableNames as $fromTableName) {
      $toTableName = str_replace($fromTablePrfx, $toTablePrfx, $fromTableName);
      $this->renameTable($fromTableName, $toTableName);
    }
  }

  protected function freshInstall($locale, $sampleDataRequired = false,
                        $version=OBIB_LATEST_DB_VERSION,
                        $dbName = DB_TABLENAME_PREFIX) {
echo "in InstallQuery: freshInstall()<br/>\n";
    $rootDir = '../install/' . $version . '/sql';
    $localeDir = '../locale/' . $locale . '/sql/' . $version;
    $this->executeSqlFilesInDir($rootDir, $dbName);
    $this->executeSqlFilesInDir($localeDir . '/domain/', $dbName);
    if($sampleDataRequired) {
      $this->executeSqlFilesInDir($localeDir . '/sample/', $dbName);
    }
  }

  protected function createDatabase($dbName) {
    $sql = $this->mkSQL("create database if not exists %I ", $dbName);
echo "creating: sql={$sql}<br/>\n";
    return $this->act($sql);
  }

  protected function copyDatabase($fromDb, $toDb) {
    $sql = $this->mkSQL('SHOW TABLES FROM %I ', $fromDb);
//echo "copying: sql={$sql}<br/>\n";
    $rslt = $this->select($sql);
    while ($row = $rslt->fetch_array()) {
echo "copying table {$row[0]}<br/>\n";
      $sql = $this->mkSQL('create table %q as select * from %q', $toDb.'.'.$row[0], $fromDb.'.'.$row[0]);
			$this->act($sql);
			$this->dropTable($fromDb.'.'.$row[0]);
    }
  }

	## ------------------------------------------------------------------------ ##
  private function dropTable($tableName) {
//    $sql = $this->mkSQL("drop table if exists %I ", $tableName);
    $sql = $this->mkSQL("drop table if exists %q ", $tableName);
echo "droping: sql={$sql}<br/>\n";
    return $this->act($sql);
  }

  private function renameTable($fromTableName, $toTableName) {
      $this->dropTable($toTableName);
      $sql = "rename table ".$fromTableName." to ".$toTableName;
      return $this->act($sql);
  }

  private function getTableNames($pattern = "") {
    if($pattern == "") {
      $pattern = DB_TABLENAME_PREFIX.'%';
    }
    $sql = "show tables like '".$pattern."'";
    $rows = $this->act($sql, OBIB_NUM);

    $tablenames = array();
    foreach ($rows as $row) {
      $tablenames[] = $row[0];
    }
    return $tablenames;
  }
  
  private function executeSqlFilesInDir($dir, $dbName = "") {
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($filename = readdir($dh)) !== false) {
echo "processing sql file: $filename <br />\n";
          if(preg_match('/\\.sql$/', $filename)) {
            $this->executeSqlFile($dir.'/'.$filename, $dbName);
          }
        }
        closedir($dh);
      }
    }
  }

  /**
   * Function to read through an sql file executing SQL only when ";" is encountered
   */
  private function executeSqlFile($filename, $dbName = DB_TABLENAME_PREFIX) {
    $fp = fopen($filename, "r");
    # show error if file could not be opened
    if ($fp == false) {
      Fatal::error("Error reading file: ".H($filename));
    } else {
      $sqlStmt = "";
      while (!feof ($fp)) {
        $char = fgetc($fp);
        if ($char == ";") {
          //replace table prefix, we're doing it here as the install script may
          //want to override the required prefix (eg. during upgrade / conversion 
          //process)
          $sql = str_replace("%prfx%",$dbName,$sqlStmt);
echo "execute sql: sql={$sql}<br/>\n";
          $this->act($sql);
          $sqlStmt = "";
        } else {
          $sqlStmt .= $char;
        }
      }
      fclose($fp);
    }
  }
}

?>
