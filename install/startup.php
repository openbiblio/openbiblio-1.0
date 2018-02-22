<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<!DOCTYPE html >
<html lang="en" class="no-js obInstall" >
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <title>OpenBiblio Initial Startup</title>

    <script src="../shared/modernizr.min.js"></script>
    <script src="../shared/jquery/jquery-3.1.0.min.js"></script>

    <link rel="stylesheet" href="../shared/normalize.css" />
    <link rel="stylesheet" href="../shared/jquery/jquery-ui.min.css" />
    <link rel="stylesheet" href="../shared/style.css" />
    <link rel="stylesheet" href="../themes/default/theme.css" />

</head>
<body>
    <div id="content">
    	<fieldset id="progress">
    		<ul id="progressList">
    		</ul>
    	</fieldset>

    	<fieldset id="action">
    		<section id="plsWait">
    			<h3 id="waitMsg">Building OpenBiblio tables</h3>
    			<img src="../images/please_wait.gif" />
    			<span id="waitText">please wait...</span>
    		</section>

            <section id="const_editor">
              <p class="bold">Enter Database Constants</p><br /></P>
              <p class="note">Note: All fields MUST be filled.</P>
              <form role="form" id="dbConstForm">
                <label for="engine">Database Engine: </label>
                <select id="dbEngine">
                    <option value="cubrid">CUBRID</option>
                    <option value="odbc">DB2</option>
                    <option value="firebird">Firebird</option>
                    <option value="ibm">IBM</option>
                    <option value="informix">Informix</option>
                    <option value="sqlsrv">MS SQL Server</option>
                    <option value="mysql" selected>MySQL</option>
                    <option value="odbc">ODBC</option>
                    <option value="ocl">Oracle</option>
                    <option value="pgsql">PostgreSQL</option>
                    <option value="sqlite">SQLite</option>
                    <option value="4d">4D</option>
                </select><br />
                <label for="hostId">Host Id: </label>
                <input id="hostId" type="text" placeholder="Ip Address or Name" required /><br />
                <label for="userNm">User Name: </label>
                <input id="userNm" type="text" placeholder="missing" required /><br />
                <label for="passWd">User Password: </label>
                <input id="passWd" type="password" placeholder="missing" required /><br />
                <label for="dbName">Database Name: </label>
                <input id="dbName" type="text" placeholder="missing" required /><br />
                <p class="note">Note: Above user name & password must be valid for this database server</P>
                <br />
        		<input id="constBtn" type="submit" value="Submit" />
              </form>
            </section>

            <section id="createDB">
              <p class="bold">Enter Database Engine Access Data</p><br /></P>
              <p class="note">Note: These items will NOT be stored.</P>
              <form role="form" id="dbCreateForm">
                <label for="adminNm">Admin User: </label>
                <input id="adminNm" type="text" placeholder="missing" required /><br />
                <label for="adminPw">Admin Password: </label>
                <input id="adminPw" type="password" placeholder="missing" required /><br />
                <p class="note">Press button to create new Database</p><br /></p>
                <br />
        		<input id="newDbBtn" type="button" value="Create Database" />
        		<input id="skipBtn" type="button" value="Skip This Step" />
            </section>

            <section id="continue">
              <p class="bold">Press button to continue with installation</p><br /></p>
                <br />
        		<input id="contBtn" type="submit" value="Continue" />
        		<input id="restartBtn" type="button" value="Try Again" />
            </section>
        </fieldset>
    </div>

<?php
require_once("../install/startupJs.php");

