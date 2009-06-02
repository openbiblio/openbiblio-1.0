<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
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

/****************************************************************************
 *  Misc. system constants
 ****************************************************************************
 */
define("OBIB_DEFAULT_STATUS","in");
define("OBIB_STATUS_IN","in");
define("OBIB_STATUS_OUT","out");
define("OBIB_STATUS_ON_LOAN","ln");
define("OBIB_STATUS_ON_ORDER","ord");
define("OBIB_STATUS_SHELVING_CART","crt");
define("OBIB_STATUS_ON_HOLD","hld");
define("OBIB_MBR_CLASSIFICATION_JUVENILE","j");
define("OBIB_DEMO_FLG",false);
define("OBIB_HIGHLIGHT_I18N_FLG",false);
define("OBIB_SEARCH_MAXPAGES",20);

define("OBIB_MYSQL_DATETIME_TYPE","datetime");
define("OBIB_MYSQL_DATETIME_FORMAT","Y-m-d H:i:s");
define("OBIB_MYSQL_DATE_TYPE","date");
define("OBIB_MYSQL_DATE_FORMAT","Y-m-d");
?>
