<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	## collect data from a search submittal
	$srchBy =			$_REQUEST[srchBy];
	$lookupVal =	$_REQUEST[lookupVal];
	$srchBy2 =		$_REQUEST[srchBy2];
	$lookupVal2 =	$_REQUEST[lookupVal2];
	$srchBy3 =		$_REQUEST[srchBy3];
	$lookupVal3 =	$_REQUEST[lookupVal3];
	$srchBy4 =		$_REQUEST[srchBy4];
	$lookupVal4 =	$_REQUEST[lookupVal4];
	$srchBy5 =		$_REQUEST[srchBy5];
	$lookupVal5 =	$_REQUEST[lookupVal5];

	$sruIndexTerm = 'dc'; // ContextSet = Dublin Core
//	$sruIndexTerm = 'bath'; // ContextSet = Bath

		//echo 'original Query specification is: ' . htmlspecialchars("#$srchBy => $lookupVal") . '<br />';
		//echo 'srchBy: ' . $srchBy . ', srchBy2: ' . $srchBy2 . '<br />';
		#### First search criteria line
		switch($srchBy) {
		case "4":
			$srchByName = 'Title';
			$sruQry = "$sruIndexTerm.title=";
			$lookupVal = '"' . $lookupVal . '"';
			break;

		case "7":
			$srchByName = 'ISBN';
			$sruQry = "$sruIndexTerm.isbn%3d";
			//echo "input ISBN=$lookupVal <br />";
			$lookupVal = verifyISBN($lookupVal,$keepIsbnDashes);
			//echo 'final isbn: ' . $lookupVal . '<br />';
		   break;

		case "8":
			$srchByName = 'ISSN';
			$sruQry = "$sruIndexTerm.issn=";
			//protocol requires that '-' be included in ISSN searches
			break;

		case "9":
			$srchByName = 'LCCN';
			$sruQry = "$sruIndexTerm.lccn=";
			//echo "input lccn=$lookupVal <br />";
			$lookupVal = verifyLCCN($lookupVal);
			//echo 'final lccn: ' . $lookupVal . '<br />';
		  break;

		case "1016":
			$srchByName = 'Keyword';
			$sruQry = "$sruIndexTerm.subject=";
			$lookupVal = '"' . $lookupVal . '"';
  		break;
  		
		case "999":
		  ## special for external use as an general search engine
		  $srchByName = 'general';
		  $sruQuery = "";
			$lookupVal = '"' . $lookupVal . '"';
			break;
		}

		#### Second search criteria line
		if (!empty($lookupVal2)) {
			if ($srchBy2 == "1004") {
				$srchByName2 = 'Author';
				$sruQry2 = "$sruIndexTerm.author=";
			}	else if ($srchBy2 == "1016") {
				$srchByName2 = 'Keyword';
				$sruQry2 = "$sruIndexTerm.subject=";
  		}
			$lookupVal2 = '"' . $lookupVal2 . '"';
		}
		
		#### Third search criteria line
		if (!empty($lookupVal3)) {
			if ($srchBy3 == "1018") {
				$srchByName3 = 'Publisher';
				$sruQry3 = "$sruIndexTerm.publisher=";
			}	else if ($srchBy3 == "59") {
				$srchByName3 = 'Pub Loc';
				$sruQry3 = "$sruIndexTerm.geographicName=";
			}	else if ($srchBy3 == "31") {
				$srchByName3 = 'Pub Date';
				$sruQry3 = "dc.date=";
			}	else if ($srchBy3 == "1016") {
				$srchByName3 = 'Keyword';
				$sruQry3 = "$sruIndexTerm.subject=";
			}
			$lookupVal3 = '"' . $lookupVal3 . '"';
		}

		#### Fourth search criteria line
		if (!empty($lookupVal4)) {
			if ($srchBy4 == "1018") {
				$srchByName4 = 'Publisher';
				$sruQry4 = "$sruIndexTerm.publisher=";
			}	else if ($srchBy4 == "59") {
				$srchByName4 = 'Pub Loc';
				$sruQry4 = "$sruIndexTerm.geographicName=";
			}	else if ($srchBy4 == "31") {
				$srchByName4 = 'Pub Date';
				$sruQry4 = "$sruIndexTerm.date=";
			}	else if ($srchBy4 == "1016") {
				$srchByName4 = 'Keyword';
				$sruQry4 = "$sruIndexTerm.subject=";
			}
			$lookupVal4 = '"' . $lookupVal4 . '"';
		}

		#### Fifth search criteria line
		if (!empty($lookupVal5)) {
			if ($srchBy5 == "1018") {
				$srchByName5 = 'Publisher';
				$sruQry5 = "$sruIndexTerm.publisher=";
			}	else if ($srchBy5 == "59") {
				$srchByName5 = 'Pub Loc';
				$sruQry5 = "$sruIndexTerm.geographicName=";
			}	else if ($srchBy5 == "31") {
				$srchByName5 = 'Pub Date';
				$sruQry5 = "$sruIndexTerm.date=";
			}	else if ($srchBy5 == "1016") {
				$srchByName5 = 'Keyword';
				$sruQry5 = "$sruIndexTerm.subject=";
			}
			$lookupVal5 = '"' . $lookupVal5 . '"';
		}

		#### create z39.50 RPN-style query string
		$zQuery = '@attr 1=' . $srchBy . ' ' . $lookupVal;
		if (!empty($lookupVal2))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy2 . ' ' . $lookupVal2;
		}
		if (!empty($lookupVal3))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy3 . ' ' . $lookupVal3;
		}
		if (!empty($lookupVal4))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy4 . ' ' . $lookupVal4;
		}
		if (!empty($lookupVal5))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy5 . ' ' . $lookupVal5;
		}
		//echo 'z39.50 rpn-style query specification is: ' . htmlspecialchars($zQuery) . '<br />';
		
		#### create SRU CQL-style query string
		$sQuery = "$sruQry$lookupVal";
		if (!empty($lookupVal2)) $sQuery .= " and $sruQry2$lookupVal2";
		if (!empty($lookupVal3)) $sQuery .= " and $sruQry3lookupVal3";
		if (!empty($lookupVal4)) $sQuery .= " and $sruQry4$lookupVal4";
		if (!empty($lookupVal5)) $sQuery .= " and $sruQry5$lookupVal5";
		//echo 'SRU cql-style query specification is: ' . htmlspecialchars($sQuery) . '<br />';

?>
