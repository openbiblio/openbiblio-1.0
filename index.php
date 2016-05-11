<?php
  if (file_exists('database_constants.php') ) {
    // usual startup process, customize to suit staff requirements
    // header("Location: circ/memberForms.php");  // circulation dept is main user
    header("Location: catalog/srchForms.php");    // catalogging is main user
  } else {
    // we have a new installation, so go to installer instead.
    header("Location: install/index.php");
  }
?>



