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
#*  Common translation text shared among multiple pages
#****************************************************************************
$trans["sharedCancel"]             = "\$text = 'Cancel';";
$trans["sharedDelete"]             = "\$text = 'Delete';";

#****************************************************************************
#*  Translation text for page biblio_view.php
#****************************************************************************
$trans["biblioViewTble1Hdr"]       = "\$text = 'Bibliography Information';";
$trans["biblioViewMaterialType"]   = "\$text = 'Material Type';";
$trans["biblioViewCollection"]     = "\$text = 'Collection';";
$trans["biblioViewCallNmbr"]       = "\$text = 'Call Number';";
$trans["biblioViewTble2Hdr"]       = "\$text = 'Bibliography Copy Information';";
$trans["biblioViewTble2Col1"]      = "\$text = 'Barcode #';";
$trans["biblioViewTble2Col2"]      = "\$text = 'Description';";
$trans["biblioViewTble2Col3"]      = "\$text = 'Status';";
$trans["biblioViewTble2Col4"]      = "\$text = 'Status Dt';";
$trans["biblioViewTble2Col5"]      = "\$text = 'Due Back';";
$trans["biblioViewTble2ColFunc"]   = "\$text = 'Function';";
$trans["biblioViewTble3Hdr"]       = "\$text = 'Additional Bibliographic Information';";
$trans["biblioViewNoAddInfo"]      = "\$text = 'No additional bibliographic information available.';";
$trans["biblioViewNoCopies"]       = "\$text = 'No copies have been created.';";
$trans["biblioViewOpacFlg"]        = "\$text = 'Show in OPAC';";
$trans["biblioViewNewCopy"]        = "\$text = 'Add New Copy';";

#****************************************************************************
#*  Translation text for page biblio_search.php
#****************************************************************************
$trans["biblioSearchNoResults"]    = "\$text = 'No results found.';";
$trans["biblioSearchResults"]      = "\$text = 'Search Results';";
$trans["biblioSearchResultPages"]  = "\$text = 'Result Pages';";
$trans["biblioSearchPrev"]         = "\$text = 'prev';";
$trans["biblioSearchNext"]         = "\$text = 'next';";
$trans["biblioSearchResultTxt"]    = "if (%items% == 1) {
                                        \$text = '%items% result found.';
                                      } else {
                                        \$text = '%items% results found';
                                      }";
$trans["biblioSearchauthor"]       = "\$text = ' sorted by author';";
$trans["biblioSearchtitle"]        = "\$text = ' sorted by title';";
$trans["biblioSearchSortByAuthor"] = "\$text = 'sort by author';";
$trans["biblioSearchSortByTitle"]  = "\$text = 'sort by title';";
$trans["biblioSearchTitle"]        = "\$text = 'Title';";
$trans["biblioSearchAuthor"]       = "\$text = 'Author';";
$trans["biblioSearchMaterial"]     = "\$text = 'Material';";
$trans["biblioSearchCollection"]   = "\$text = 'Collection';";
$trans["biblioSearchCall"]         = "\$text = 'Call Number';";
$trans["biblioSearchCopyBCode"]    = "\$text = 'Copy Barcode';";
$trans["biblioSearchCopyStatus"]   = "\$text = 'Status';";
$trans["biblioSearchNoCopies"]     = "\$text = 'No copies are available.';";

#****************************************************************************
#*  Translation text for page loginform.php
#****************************************************************************
$trans["loginFormTbleHdr"]         = "\$text = 'Staff Login';";
$trans["loginFormUsername"]        = "\$text = 'Username';";
$trans["loginFormPassword"]        = "\$text = 'Password';";
$trans["loginFormLogin"]           = "\$text = 'Login';";

#****************************************************************************
#*  Translation text for page hold_del_confirm.php
#****************************************************************************
$trans["holdDelConfirmMsg"]        = "\$text = 'Are you sure you want to delete this hold request?';";

#****************************************************************************
#*  Translation text for page hold_del.php
#****************************************************************************
$trans["holdDelSuccess"]           = "\$text='Hold request was successfully deleted.';";

#****************************************************************************
#*  Translation text for page help_header.php
#****************************************************************************
$trans["helpHeaderTitle"]          = "\$text='OpenBiblio Help';";
$trans["helpHeaderCloseWin"]       = "\$text='Close Window';";
$trans["helpHeaderContents"]       = "\$text='Contents';";
$trans["helpHeaderPrint"]          = "\$text='Print';";

?>