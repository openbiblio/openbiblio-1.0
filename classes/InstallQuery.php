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
    $sql = $this->mkSQL('SHOW TABLES LIKE %C ', $tablePrfx.'.settings');
    $rows = $this->select01($sql);

    if (!$rows || empty($rows) || !isset($rows)) {
      return 'NothingFound';
    }

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

  public function getCurrentDatabaseVersion($dbName = OBIB_DATABASE) {
		## versions 1.0+
    $sql = $this->mkSQL('SELECT `value` FROM %i WHERE `name`=%Q', $dbName.'.settings','version');
    $row = $this->select01($sql);
		if ($row) {
			$version = $row['value'];
		} else {
			## versions < 1.0
    	$sql = $this->mkSQL('SELECT `version` FROM %i ', $dbName.'.settings');
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

  public function freshInstall($locale, $sampleDataRequired = false,
                        $version=OBIB_LATEST_DB_VERSION,
                        $dbName = DB_TABLENAME_PREFIX) {
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
    $res = $this->act($sql);
    $sql = $this->mkSQL("GRANT ALL PRIVILEGES ON %I TO %Q", $dbName, OBIB_USERNAME);
    $r = $this->act($sql);
    return $r;
  }

  protected function copyDatabase($fromDb, $toDb) {
    $sql = $this->mkSQL("SHOW TABLES FROM %I ", $fromDb);
    $rslt = $this->select($sql);
	echo "copy ".$rslt->num_rows." tables from ".$fromDb." to ".$toDb." <br />\n";
    $renaming = true;
    while ($row = $rslt->fetch_array()) {
        $src = $fromDb.'.'.$row[0];
        $dst = $toDb.'.'.$row[0];
    
        if ($renaming){
              $this->dropTable($dst);
              $sql = "rename table ".$src." to ".$dst;
              if ($this->_act($sql) == 0){
                $renaming = false;
              }
        }
        if (!$renaming) {
            $this->dropTable($dst);
            $sql = $this->mkSQL("drop table if exists %i", $dst);
            $res = $this->act($sql);
            $sql = $this->mkSQL("create table %i as select * from %i", $dst, $src);
            $res = $this->act($sql);
            $this->dropTable($src);
        }
    }
  }

	## ------------------------------------------------------------------------ ##
  public function dropTable($tableName) {
    $sql = $this->mkSQL("drop table if exists %q ", $tableName);
    return $this->act($sql);
  }

  public function renameTable($fromTableName, $toTableName) {
      $this->dropTable($toTableName);
      $sql = "rename table ".$fromTableName." to ".$toTableName;
      return $this->act($sql);
  }

  public function getTableNames($pattern = "") {
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
  
  public function executeSqlFilesInDir($dir, $dbName = "") {
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($filename = readdir($dh)) !== false) {
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
  function executeSqlFile($filename, $dbName = DB_TABLENAME_PREFIX) {
    $fp = fopen($filename, "r");
    # show error if file could not be opened
    if ($fp == false) {
      Fatal::error("Error reading file: ".H($filename));
    } else {
        //this code based rom here :
        //http://stackoverflow.com/questions/147821/loading-sql-files-from-within-php
      $sqlStmt = "";
      while (!feof ($fp)) {
        $line = fgets($fp);
        $line = trim($line);
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;

        $sqlStmt .= $line;
        
        if (substr($line, -1) == ';') {
          //replace table prefix, we're doing it here as the install script may
          //want to override the required prefix (eg. during upgrade / conversion 
          //process)
          $sql = str_replace("%prfx%",$dbName,$sqlStmt);
          $this->act($sql);
          $sqlStmt = "";
        }
      }
      fclose($fp);
    }
  } //executeSqlFile
}

?>
