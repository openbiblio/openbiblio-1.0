<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
/**
 * Back-end API for those functions unique to OB initial installation
 * @author Fred LaPlante
 */

    //print_r($_REQUEST);echo "<br />";

	switch ($_REQUEST['mode']) {
        case 'createConstFile':
            $path = "..";
            $fn = $path . "/database_constants.php";

            $content =
                "<?php \n".
                '$this->dsn["host"] = '      ."'".   $_REQUEST['host']    ."'; \n".
                '$this->dsn["username"] = '  ."'".   $_REQUEST['user']    ."'; \n".
                '$this->dsn["pwd"] = '       ."'".   $_REQUEST['passwd']  ."'; \n".
                '$this->dsn["database"] = '  ."'".   $_REQUEST['db']      ."'; \n".
                '$this->dsn["mode"] = '      .       'haveConst'          ."; \n"
            ;

            if (false === file_put_contents($fn, $content)) {
                echo 'The file is NOT writable -- attempting chmod'."\n";
		try {
		    chmod($path, 0777);
		}
		catch (Exception $e) {
                    echo "Error: Unable to set write permission on folder '".$path."'";
                    echo "Please chmod 777 the folder holding '".$fn."'";
                    exit;
		}
                if (false === file_put_contents($fn, $content)) {
                    echo 'The database constants file could not be written.  Please create' .
			'it manually using the database_constants_deploy.php file as a model.';
		}
            }
            echo "success";
        break;

/*  hints for future development - FL May 2016
$host="localhost";

$root="root";
$root_password="rootpass";

$user='newuser';
$pass='newpass';
$db="newdb";

    try {
        $dbh = new PDO("mysql:host=$host", $root, $root_password);

        $dbh->exec("CREATE DATABASE `$db`;
                CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
                GRANT ALL ON `$db`.* TO '$user'@'localhost';
                FLUSH PRIVILEGES;")
        or die(print_r($dbh->errorInfo(), true));

    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }
*/

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
            echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
