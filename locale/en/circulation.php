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
$trans["indexHeading"]       = "\$text='Circulation';";
$trans["indexIntro"]         = "\$text=
  'Use the following functions located in the left hand navagation area 
   to manage your library\'s member records.';";
$trans["indexFunc"]          = "\$text='Function';";
$trans["indexDesc"]          = "\$text='Description';";
$trans["indexMbrSrch"]       = "\$text='Member Search';";
$trans["indexMbrSrchDesc1"]  = "\$text='Search and view library member records.  Once a member record is selected you can';";
$trans["indexMbrSrchDesc2"]  = "\$text='edit the information';";
$trans["indexMbrSrchDesc3"]  = "\$text='checkout bibliographies';";
$trans["indexMbrSrchDesc4"]  = "\$text='delete the member';";
$trans["indexMbrSrchDesc5"]  = "\$text='record a late fee payment for the member';";
$trans["indexNewMbr"]        = "\$text='New Member';";
$trans["indexNewMbrDesc"]    = "\$text='Build a new library member record.';";
$trans["indexCheckIn"]       = "\$text='Check In';";
$trans["indexCheckInDesc"]   = "\$text='Access the shelving cart list to checkin bibliographies.';";
$trans["indexReports"]       = "\$text='Reports';";
$trans["indexReportsDesc"]   = "\$text='Access member reports.';";

#****************************************************************************
#*  Translation text for page mbr_new_form.php, mbr_edit_form.php and mbr_fields.php
#****************************************************************************
$trans["mbrNewForm"]         = "\$text='Add New';";
$trans["mbrEditForm"]        = "\$text='Edit';";
$trans["mbrFldsHeader"]      = "\$text='Member:';";
$trans["mbrFldsCardNmbr"]    = "\$text='Card Number:';";
$trans["mbrFldsLastName"]    = "\$text='Last Name:';";
$trans["mbrFldsFirstName"]   = "\$text='First Name:';";
$trans["mbrFldsAddr1"]       = "\$text='Address Line 1:';";
$trans["mbrFldsAddr2"]       = "\$text='Address Line 2:';";
$trans["mbrFldsCity"]        = "\$text='City:';";
$trans["mbrFldsStateZip"]    = "\$text='State, Zip:';";
$trans["mbrFldsHomePhone"]   = "\$text='Home Phone:';";
$trans["mbrFldsWorkPhone"]   = "\$text='Work Phone:';";
$trans["mbrFldsClassify"]    = "\$text='Classification:';";
$trans["mbrFldsGrade"]       = "\$text='School Grade:';";
$trans["mbrFldsTeacher"]     = "\$text='School Teacher:';";
$trans["mbrFldsSubmit"]      = "\$text='Submit';";
$trans["mbrFldsCancel"]      = "\$text='Cancel';";


?>