<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/****************************************************************************
 * result types:
 * OBIB_ASSOC - associative array result type
 * OBIB_NUM - numeric array result type
 * OBIB_BOTH - both assoc and numeric array result type
 ****************************************************************************
 */
define("OBIB_ASSOC","1");
define("OBIB_NUM","2");
define("OBIB_BOTH","3");

/****************************************************************************
 * search types:
 * OBIB_SEARCH_TITLE
 * OBIB_SEARCH_AUTHOR
 * OBIB_SEARCH_SUBJECT
 ****************************************************************************
 */
define("OBIB_SEARCH_BARCODE","1");
define("OBIB_SEARCH_TITLE","2");
define("OBIB_SEARCH_AUTHOR","3");
define("OBIB_SEARCH_SUBJECT","4");
define("OBIB_SEARCH_NAME","5");
define("OBIB_SEARCH_KEYWORD","6");
define("OBIB_SEARCH_PUBLISHER","7");
define("OBIB_SEARCH_SERIES","8");

/****************************************************************************
 *  Misc. system constants
 ****************************************************************************
 */
define("OBIB_CODE_VERSION","CVS");
define("OBIB_LATEST_DB_VERSION","0.4.0-mps3");

define("OBIB_DEFAULT_STATUS","in");
define("OBIB_STATUS_PROCESSING", "prc");
define("OBIB_STATUS_IN","in");
define("OBIB_STATUS_OUT","out");
define("OBIB_STATUS_ON_LOAN","ln");
define("OBIB_STATUS_ON_ORDER","ord");
define("OBIB_STATUS_SHELVING_CART","crt");
define("OBIB_STATUS_ON_HOLD","hld");
define("OBIB_STATUS_NOT_ON_LOAN","nol");
define("OBIB_MBR_CLASSIFICATION_JUVENILE","j");
define("OBIB_DEMO_FLG",false);
define("OBIB_HIGHLIGHT_I18N_FLG",false);
define("OBIB_SEARCH_MAXPAGES",10);

define("OBIB_UPLOAD_DIR",REL(__FILE__, "../uploads/"));

define("OBIB_MASTER_CALENDAR",1);

# Locking
define("OBIB_LOCK_NAME", OBIB_DATABASE.'.OpenBiblio.BigLock');
define("OBIB_LOCK_TIMEOUT", 5);

define("OBIB_MYSQL_DATETIME_TYPE","datetime");
define("OBIB_MYSQL_DATETIME_FORMAT","Y-m-d H:i:s");
define("OBIB_MYSQL_DATE_TYPE","date");
define("OBIB_MYSQL_DATE_FORMAT","Y-m-d");

define("LOCALE_ROOT",REL(__FILE__, "../locale"));

# Not fully implemented yet.
define("DB_TABLENAME_PREFIX", "");
