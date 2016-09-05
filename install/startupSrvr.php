<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Back-end API for those functions unique to OB initial installation
 * @author Fred LaPlante - May 2016
 * improved error handling - Js May 2016
 * added database creation - FL May 2016
 */

    //print_r($_REQUEST);echo "<br />";
    function createDB ($dbh, $db) {
        $sql = "CREATE DATABASE `$db` ";
        $dbh->exec($sql);
    };
    function createUser ($dbh, $user, $host, $passwd) {
        $sql = "CREATE USER '$user'@'$host' IDENTIFIED BY '$passwd' ";
        $dbh->exec($sql);
    };
    function grantPriv ($dbh, $db, $user, $host) {
        $sql = "GRANT ALL ON `$db`.* TO '$user'@'$host'; FLUSH PRIVILEGES;";
        $dbh->exec($sql);
    };
    function hndlError($e) {
        print ("Error: ". $e->getMessage());
        exit;
    };

	switch ($_REQUEST['mode']) {
        case 'createConstFile':
            $path = "..";
            $fn = $path . "/dbParams.php";

            $content =
                "<?php \n".
                '$this->dsn["dbEngine"] = '  ."'".   $_REQUEST['dbEngine'] ."'; \n".
                '$this->dsn["host"] = '      ."'".   $_REQUEST['host']     ."'; \n".
                '$this->dsn["username"] = '  ."'".   $_REQUEST['user']     ."'; \n".
                '$this->dsn["pwd"] = '       ."'".   $_REQUEST['passwd']   ."'; \n".
                '$this->dsn["database"] = '  ."'".   $_REQUEST['db']       ."'; \n".
                '$this->dsn["mode"] = '      .       'haveConst'           ."; \n"
            ;

            $response = array();

            if (false === file_put_contents($fn, $content)) {
                $response[] = 'The file is NOT writable -- attempting chmod';
        		try {
        		    chmod($path, 0777);
        		}
        		catch (Exception $e) {
                            $response[] = "Error: Unable to set write permission on folder '".$path."'";
                            $response[] = "Please chmod 777 the folder holding '".$fn."'";
                            exit;
        		}
                if (false === file_put_contents($fn, $content)) {
                    $response['error'] = 'The database constants file could not be written.';
		            $response[] = 'Please create dbParams.php manually using the dbParams_deploy.php file as a model.';
        		}
                echo json_encode($response);
            } else {
                echo json_encode(array("success"));
            }
        break;

        case 'createDatabase':
            $host=$_REQUEST["host"];
            $adminNm=$_REQUEST["adminNm"];
            $adminPw=$_REQUEST["adminPw"];
            $user=$_REQUEST['user'];
            $passwd=$_REQUEST['passwd'];
            $db=$_REQUEST["db"];

            $dbh = new PDO("mysql:host=$host", $adminNm, $adminPw);
                    //or die(print_r($dbh->errorInfo(), true));
                    //FLUSH PRIVILEGES;");

            try { createDB($dbh, $db); }
            catch (PDOException $e) { hndlError($e); }

            try { createUser ($dbh, $user, $host, $passwd); }
            catch (PDOException $e) { hndlError ($e); }

            try { grantPriv ($dbh, $db, $user, $host); }
            catch (PDOException $e) { hndlError ($e); }

            echo "success";
        break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
            echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
