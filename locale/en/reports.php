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
#*  Translation text used on multiple pages
#****************************************************************************
$trans["reportsCancel"]            = "\$text = 'Cancel';";

#****************************************************************************
#*  Translation text for page index.php
#****************************************************************************
$trans["indexHdr"]                 = "\$text = 'Reports';";
$trans["indexDesc"]                = "\$text = 'Use the report or label list located in the left hand navagation area to produce reports or labels.';";

#****************************************************************************
#*  Translation text for page report_list.php
#****************************************************************************
$trans["reportListHdr"]            = "\$text = 'Report List';";
$trans["reportListDesc"]           = "\$text = 'Choose from one of the following links to run a report.';";
$trans["reportListXmlErr"]         = "\$text = 'Error occurred parsing report definition xml.';";

#****************************************************************************
#*  Translation text for page label_list.php
#****************************************************************************
$trans["labelListHdr"]             = "\$text = 'Label List';";
$trans["labelListDesc"]            = "\$text = 'Choose from one of the following links to produce labels in pdf format.';";
$trans["displayLabelsXmlErr"]      = "\$text = 'Error occurred parsing report definition xml.  Error = ';";

#****************************************************************************
#*  Translation text for page letter_list.php
#****************************************************************************
$trans["letterListHdr"]            = "\$text = 'Letter List';";
$trans["letterListDesc"]           = "\$text = 'Choose from one of the following links to produce letters in pdf format.';";
$trans["displayLettersXmlErr"]      = "\$text = 'Error occurred parsing report definition xml.  Error = ';";

#****************************************************************************
#*  Translation text for page report_criteria.php
#****************************************************************************
$trans["reportCriteriaHead1"]      = "\$text = 'Report Search Criteria (optional)';";
$trans["reportCriteriaHead2"]      = "\$text = 'Report Sort Order (optional)';";
$trans["reportCriteriaCrit1"]      = "\$text = 'Criteria 1:';";
$trans["reportCriteriaCrit2"]      = "\$text = 'Criteria 2:';";
$trans["reportCriteriaCrit3"]      = "\$text = 'Criteria 3:';";
$trans["reportCriteriaCrit4"]      = "\$text = 'Criteria 4:';";
$trans["reportCriteriaEQ"]         = "\$text = '=';";
$trans["reportCriteriaNE"]         = "\$text = 'not =';";
$trans["reportCriteriaLT"]         = "\$text = '&lt;';";
$trans["reportCriteriaGT"]         = "\$text = '&gt;';";
$trans["reportCriteriaLE"]         = "\$text = '&lt or =';";
$trans["reportCriteriaGE"]         = "\$text = '&gt or =';";
$trans["reportCriteriaBT"]         = "\$text = 'between';";
$trans["reportCriteriaAnd"]        = "\$text = 'and';";
$trans["reportCriteriaRunReport"]  = "\$text = 'Run Report';";
$trans["reportCriteriaSortCrit1"]  = "\$text = 'Sort 1:';";
$trans["reportCriteriaSortCrit2"]  = "\$text = 'Sort 2:';";
$trans["reportCriteriaSortCrit3"]  = "\$text = 'Sort 3:';";
$trans["reportCriteriaAscending"]  = "\$text = 'ascending';";
$trans["reportCriteriaDescending"] = "\$text = 'descending';";
$trans["reportCriteriaStartOnLabel"] = "\$text = 'Start printing on label:';";

#****************************************************************************
#*  Translation text for page run_report.php
#****************************************************************************
$trans["runReportReturnLink1"]     = "\$text = 'report selection criteria';";
$trans["runReportReturnLink2"]     = "\$text = 'report list';";
$trans["runReportTotal"]           = "\$text = 'Total Rows:';";

#****************************************************************************
#*  Translation text for page display_labels.php
#****************************************************************************
$trans["displayLabelsStartOnLblErr"] = "\$text = 'Field must be numeric.';";
$trans["displayLabelsXmlErr"]      = "\$text = 'Error occurred parsing report definition xml.  Error = ';";

#****************************************************************************
#*  Translation text for page noauth.php
#****************************************************************************
$trans["noauthMsg"]                = "\$text = 'You are not authorized to use the Reports tab.';";

#****************************************************************************
#*  Report Titles
#****************************************************************************
$trans["reportHolds"]              = "\$text = 'Hold Requests Containing Member Contact Info';";
$trans["reportCheckouts"]          = "\$text = 'Bibliography Checkout Listing';";
$trans["reportLabels"]             = "\$text = 'Label Printing Query (used by labels)';";
$trans["popularBiblios"]           = "\$text = 'Most Popular Bibliographies';";
$trans["overdueList"]              = "\$text = 'Over Due Member List';";
$trans["balanceDueList"]           = "\$text = 'Balance Due Member List';";

#****************************************************************************
#*  Label Titles
#****************************************************************************
$trans["labelsMulti"]              = "\$text = 'Multi Label Example';";
$trans["labelsSimple"]             = "\$text = 'Simple Label Example';";

#****************************************************************************
#*  Column Text
#****************************************************************************
$trans["biblio.create_dt"]         = "\$text = 'Date Added';";
$trans["biblio.last_change_dt"]    = "\$text = 'Last Changed';";
$trans["biblio.material_cd"]       = "\$text = 'Material Cd';";
$trans["biblio.collection_cd"]     = "\$text = 'Collection';";
$trans["biblio.call_nmbr1"]        = "\$text = 'Call 1';";
$trans["biblio.call_nmbr2"]        = "\$text = 'Call 2';";
$trans["biblio.call_nmbr3"]        = "\$text = 'Call 3';";
$trans["biblio.title_remainder"]   = "\$text = 'Title Remainder';";
$trans["biblio.responsibility_stmt"] = "\$text = 'Stmt of Resp';";
$trans["biblio.opac_flg"]          = "\$text = 'OPAC Flag';";

$trans["biblio_copy.barcode_nmbr"] = "\$text = 'Barcode';";
$trans["biblio.title"]             = "\$text = 'Title';";
$trans["biblio.author"]            = "\$text = 'Author';";
$trans["biblio_copy.status_begin_dt"]   = "\$text = 'Status Begin Date';";
$trans["biblio_copy.due_back_dt"]       = "\$text = 'Due Back Date';";
$trans["member.mbrid"]             = "\$text = 'Member ID';";
$trans["member.barcode_nmbr"]      = "\$text = 'Member Barcode';";
$trans["member.last_name"]         = "\$text = 'Last Name';";
$trans["member.first_name"]        = "\$text = 'First Name';";
$trans["member.address1"]          = "\$text = 'Address1';";
$trans["member.address2"]          = "\$text = 'Address2';";
$trans["member.city"]              = "\$text = 'City';";
$trans["member.state"]             = "\$text = 'State';";
$trans["member.zip"]               = "\$text = 'Zip';";
$trans["member.zip_ext"]           = "\$text = 'Ext';";
$trans["biblio_hold.hold_begin_dt"] = "\$text = 'Hold Begin Date';";
$trans["member.home_phone"]        = "\$text = 'Home Phone';";
$trans["member.work_phone"]        = "\$text = 'Work Phone';";
$trans["member.email"]             = "\$text = 'Email';";
$trans["member.school_grade"]      = "\$text = 'Grade';";
$trans["biblio_status_dm.description"] = "\$text = 'Status';";
$trans["settings.library_name"]    = "\$text = 'Library Name';";
$trans["settings.library_hours"]   = "\$text = 'Library Hours';";
$trans["settings.library_phone"]   = "\$text = 'Library Phone';";
$trans["days_late"]                = "\$text = 'Days Late';";
$trans["title"]                    = "\$text = 'Title';";
$trans["author"]                   = "\$text = 'Author';";
$trans["due_back_dt"]              = "\$text = 'Due Back';";
$trans["checkoutCount"]            = "\$text = 'Checkout Count';";

?>