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
		    $response[] = 'Please create database_constants.php manually using the database_constants_deploy.php file as a model.';
        		}
            echo json_encode($response);
            } else {
            echo "success";
            }
        break;

        case 'createDatabase':
            $host=$_REQUEST["host"];
            $adminNm=$_REQUEST["adminNm"];
            $adminPw=$_REQUEST["adminPw"];
            $user=$_REQUEST['user'];
            $passwd=$_REQUEST['passwd'];
            $db=$_REQUEST["db"];

            try {
                $dbh = new PDO("mysql:host=$host", $adminNm, $adminPw);

                $dbh->exec("CREATE DATABASE `$db`;
                    CREATE USER '$user'@'$host' IDENTIFIED BY '$passwd';
                    GRANT ALL ON `$db`.* TO '$user'@'$host';
                    FLUSH PRIVILEGES;");
                    //or die(print_r($dbh->errorInfo(), true));
                    //FLUSH PRIVILEGES;");
            } catch (PDOException $e) {
                return ("Error: ". $e->getMessage());
                exit;
            }
            echo "success";
        break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
            echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
