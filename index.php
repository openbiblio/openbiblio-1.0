<?php
  if (file_exists('database_constants.php') ) {
    // usual startup process
    // header("Location: circ/memberForms.php");
    header("Location: catalog/srchForms.php");
  } else {
    // we have a new installation, so go to installer instead.
    header("Location: install/index.php");
  }
?>



