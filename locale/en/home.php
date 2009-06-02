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

/**********************************************************************************
 *   Instructions for translators:
 *
 *   All gettext key/value pairs are specified as follows:
 *     $trans["key"] = "<php translation code to set the $text variable>";
 *   Allowing translators the ability to execute php code withint the transFunc string
 *   provides the maximum amount of flexibility to format the languange syntax.
 *
 *   Formatting rules:
 *   - Resulting translation string must be stored in a variable called $text.
 *   - Input arguments must be surrounded by % characters (i.e. %pageCount%).
 *   - A backslash ('\') needs to be placed before any special php characters 
 *     (such as $, ", etc.) within the php translation code.
 *
 *   Simple Example:
 *     $trans["homeWelcome"]       = "\$text='Welcome to OpenBiblio';";
 *
 *   Example Containing Argument Substitution:
 *     $trans["searchResult"]      = "\$text='page %page% of %pages%';";
 *
 *   Example Containing a PHP If Statment and Argument Substitution:
 *     $trans["searchResult"]      = 
 *       "if (%items% == 1) {
 *         \$text = '%items% result';
 *       } else {
 *         \$text = '%items% results';
 *       }";
 *
 **********************************************************************************
 */


#****************************************************************************
#*  Translation text for page index.php
#****************************************************************************
$trans["indexHeading"]       = "\$text='Welcome to OpenBiblio';";
$trans["indexIntro"]         = "\$text=
  'Use the navigation tabs at the top of each page to access the following library
  administration sections.';";
$trans["indexTab"]           = "\$text='Tab';";
$trans["indexDesc"]          = "\$text='Description';";
$trans["indexCirc"]          = "\$text='Circulation';";
$trans["indexCircDesc1"]     = "\$text='Use this tab to manage your member records.';";
$trans["indexCircDesc2"]     = "\$text='Member administration (new, search, edit, delete)';";
$trans["indexCircDesc3"]     = "\$text='Member bibliography checkout, holds, account, and history';";
$trans["indexCircDesc4"]     = "\$text='Bibliography checkin and shelving cart list';";
//$trans["indexCircDesc5"]     = "\$text='Member late fee payment';";
$trans["indexCat"]           = "\$text='Cataloging';";
$trans["indexCatDesc1"]      = "\$text='Use this tab to manage your bibliography records.';";
$trans["indexCatDesc2"]      = "\$text='Bibliography administartion (new, search, edit, delete)';";
//$trans["indexCatDesc3"]      = "\$text='Import bibliography from USMarc record';";
$trans["indexAdmin"]         = "\$text='Admin';";
$trans["indexAdminDesc1"]    = "\$text='Use this tab to manage staff and administrative records.';";
$trans["indexAdminDesc2"]    = "\$text='Staff administartion (new, edit, password, delete)';";
$trans["indexAdminDesc3"]    = "\$text='General library settings';";
$trans["indexAdminDesc5"]    = "\$text='Library material type list';";
$trans["indexAdminDesc4"]    = "\$text='Library collection list';";
$trans["indexAdminDesc6"]    = "\$text='Library theme editor';";
$trans["indexReports"]       = "\$text='Reports';";
$trans["indexReportsDesc1"]  = "\$text='Use this tab to run reports on your library data.';";
$trans["indexReportsDesc2"]  = "\$text='Report.';";
$trans["indexReportsDesc3"]  = "\$text='Labels.';";

?>