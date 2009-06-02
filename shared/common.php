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

  #apd_set_pprof_trace();
  error_reporting(E_ALL ^ E_NOTICE);
  if (isset($cache)) {
    session_cache_limiter($cache);
  } else {
    session_cache_limiter('nocache');
  }

  /* Convenience functions for everywhere */
  /* Work around PHP's braindead include_path stuff. */
  function REL($sf, $if) {
    return dirname($sf)."/".$if;
  }
  /* Escaping */
  function H($s) {
    return htmlspecialchars($s, ENT_QUOTES);
  }
  function U($s) {
    return urlencode($s);
  }
  function HURL($s) {
    return H(U($s));
  }
  /* Translation */
  function T($s, $v=NULL) {
    global $LOC;
    return $LOC->getText($s, $v);
  }
  function nT($n, $s, $v=NULL) {
    global $LOC;
    return $LOC->nGetText($n, $s, $v);
  }

  /* This one should be used by all the form handlers that return errors. */
  function _mkPostVars($arr, $prefix) {
    $pv = array();
    foreach ($arr as $k => $v) {
      if ($prefix !== NULL) {
        $k = $prefix."[$k]";
      }
      if (is_array($v)) {
        $pv = array_merge($pv, _mkPostVars($v, $k));
      } else {
        $pv[$k] = $v;
      }
    }
    return $pv;
  }
  function mkPostVars() {
    return _mkPostVars($_REQUEST, NULL);
  }

  require_once(REL(__FILE__, '../database_constants.php'));
  require_once(REL(__FILE__, '../shared/global_constants.php'));
  require_once(REL(__FILE__, '../classes/Error.php'));
  require_once(REL(__FILE__, '../classes/Iter.php'));
  require_once(REL(__FILE__, "../classes/Nav.php"));
  require_once(REL(__FILE__, "../classes/Localize.php"));

  if (!isset($doing_install) or !$doing_install) {
    include_once(REL(__FILE__, "../model/Settings.php"));
    Settings::load();

    /* Global variables for use with themes */
    global $ThemeDirUrl, $ThemeDir, $SharedDirUrl, $HTMLHead;
    $ThemeDirUrl = "../themes/".Settings::get('theme_name');
    $ThemeDir = REL(__FILE__, $ThemeDirUrl);
    $SharedDirUrl = "../shared";
    $HTMLHead = "";

    /* Make session user info available on all pages. */
    include_once(REL(__FILE__, "../classes/SessionHandler.php"));
    session_start();
    # Forcibly disable register_globals
    if (ini_get('register_globals')) {
      foreach ($_SESSION as $k=>$v) {
        unset(${$k});
      }
    }

    global $LOC;
    $LOC = new Localize;
    $LOC->init(Settings::get('locale'));
    setlocale(LC_MONETARY,Settings::get('locale'));
    setlocale(LC_NUMERIC,Settings::get('locale'));

    include_once(REL(__FILE__, "../classes/Page.php"));
  }
