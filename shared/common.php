<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  # Forcibly disable register_globals
  if (ini_get('register_globals')) {
    foreach ($_REQUEST as $k=>$v) {
      unset(${$k});
    }
    foreach ($_ENV as $k=>$v) {
      unset(${$k});
    }
    foreach ($_SERVER as $k=>$v) {
      unset(${$k});
    }
  }
  
  /****************************************************************************
   * Cover up for the magic_quotes disaster.
   * Modified from ryan@wonko.com.
   ****************************************************************************
   */
  set_magic_quotes_runtime(0);

  if (get_magic_quotes_gpc()) {
    function magicSlashes($element) {
      if (is_array($element))
        return array_map("magicSlashes", $element);
      else
        return stripslashes($element);
    }

    // Remove slashes from all incoming GET/POST/COOKIE data.
    $_GET    = array_map("magicSlashes", $_GET);
    $_POST   = array_map("magicSlashes", $_POST);
    $_COOKIE = array_map("magicSlashes", $_COOKIE);
    $_REQUEST = array_map("magicSlashes", $_REQUEST);
  }
  
  # Escaping shorthands
  function H($s) {
    return htmlspecialchars($s, ENT_QUOTES);
  }
  function HURL($s) {
    return H(urlencode($s));
  }
  function U($s) {
    return urlencode($s);
  }
  
  # Error handling
  require_once("../classes/Error.php");
  
  # Load settings
  require_once("../shared/global_constants.php");
  
  if (!isset($doing_install) or !$doing_install) {
    require_once("../shared/read_settings.php");

    session_start();
    # Forcibly disable register_globals
    if (ini_get('register_globals')) {
      foreach ($_SESSION as $k=>$v) {
        unset(${$k});
      }
    }
  }

?>
