<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<!DOCTYPE html >
<!-- there are many lines here with obscure comments. For more info see http://html5boilerplate.com/ -->

<html lang="en" class="no-js obInstall" >

<head>
    <!-- charset MUST be specified within first 1024 char of file start to be effective -->
    <meta charset="utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <title>OpenBiblio Initial Startup</title>
    <meta name="author" content="Fred LaPlante">
    <meta name="author" content="Jane Sandberg">

    <!-- this line MUST precede all .css & JS files - FL
    	 Based on the browser in use, it places many conditional classes
    	 into the <body> tag for use by feature-specific CSS & JS statements.
    	 It also deals with html5 support issues for older IE browsers. 	 -->
    <script src="../shared/modernizr.min.js"></script>

    <!-- *********************************************************************** -->
    <!-- prefixFree.js adds appropriate vendor prefixes to CSS as needed -->
    <!-- this is considered to be temporary until the use of prefixes ends -->
    <!--script src="../shared/prefixfree.min.js"></script--> <!-- review yearly -->
    <!-- *********************************************************************** -->

    <!-- we place these JS files here because several JS modules loaded in line -->
    <!-- depend on them being in place. -->
    <!--[if lt IE 9]><script src="../shared/jquery/jquery-1.10.2.min.js"></script><!--<![endif]-->
    <!--[if gt IE 8]><!-->
    <script src="../shared/jquery/jquery-2.2.2.min.js"></script>
    <!--<![endif]-->

    <!-- All other JavaScript is placed at the end of <body>
    	 to match industry best practices and to improve overall performance -->

    <!-- This style sheet resets all browsers to a common default style -->
    <link rel="stylesheet" href="../shared/normalize.css" />

    <!-- This style sheet is specific to the jQuery UI library -->
    <link rel="stylesheet" href="../shared/jquery/jquery-ui.min.css" />

    <!-- OpenBiblio style is set here using default Theme folder -->
    <link rel="stylesheet" href="../themes/default/style.css" />

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
              <form id="dbConstForm">
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
              <form id="dbCreateForm">
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
require_once("./startupJs.php");

