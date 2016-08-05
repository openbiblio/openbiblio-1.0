<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * provides an interface to PHP's PDO API
 * derived from work by Guillaume Boschini Jun 2010
 * @author Fred LaPlante - May 2016
 */

/**
 *  An example of how to use this class
 *
 *  $sql = "select login, email from users where id = :id";
 *
 *  try {
 *      $core = Core::getInstance();
 *      $stmt = $core->dbh->prepare($sql);
 *      $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
 *
 *      if ($stmt->execute()) {
 *          $o = $stmt->fetch(PDO::FETCH_OBJ);
 *          // blablabla....
 */

class DbCore
{
    public $dbh; // handle of the db connection
    private static $instance;

    private function __construct() {
        //echo "in DbCore::__construct() <br />\n";
        $this->getConfig();
        $this->dsn["mode"] == 'haveconst';
        $real_DSN_string = "pgsql:host=".$this->dsn['host']."; port=3306; dbname=".$this->dsn['database']."; charset=utf8";
echo $real_DSN_string;
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->dbh = new PDO($real_DSN_string, $this->dsn['username'], $this->dsn['pwd'], $opt);
        } catch (PDOException $e) {
            echo "Error: Sttempted connection to DB failed".' ('.$this->dsn['database'].') '."<br />\n". $e->getMessage() ."<br />\n";
            print_r($this->dsn); echo "<br />\n";
            exit;
        }
        //echo "connection successful!";
        $this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

    private function getConfig () {
        $fn = '../database_constants.php';
        if (file_exists($fn) ) {
            include($fn); // DO NOT change to 'include_once()' !!!!!
        } else {
            $this->dsn['host'] = 'localhost';
            $this->dsn['username'] = 'admin';
            $this->dsn['pwd'] = 'admin';
            $this->dsn['database'] = 'xxxopenbiblioxxx';
            $this->dsn['mode'] = 'nodb';
        }
    }
}
