<?php
 	require_once("./classes/InstallQuery.php");

    $dbConst = array ();
    $dbConst['host'] = 'localhost';
    $dbConst['username'] = 'fred';
    $dbConst['pwd'] = 'shhh';
    $dbConst['database'] = 'openbibliowork';
    $dbConst['mode'] = '';

	$installQ = new InstallQuery($dbConst);

    $msg = $installQ->createDatabase($dbConst['database'], $dbConst['username']);
    echo $msg;

