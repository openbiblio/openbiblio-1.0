<?php
    if (file_exists('./dbParams.php') ) {
        ## usual startup process, customize to suit staff requirements
        // header("Location: circ/memberForms.php");  // if circulation dept is main user
        header("Location: ./catalog/srchForms.php");    // if catalogging is main user
    } elseif (file_exists('./database_constants.php') && !file_exists('./database_constants_deploy.php')) {
        ## transition patch due to renaming of database_constants.php
        rename('./database_constants.php', './dbParams.php');
        header("Location: ./catalog/srchForms.php");    // if catalogging is main user
    } else {
        ## we have a new installation, so go to installer instead.
        header("Location: ./install/startup.php");
    }
