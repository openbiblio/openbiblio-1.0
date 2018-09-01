<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

#****************************************************************************
#*  Temporarily disabling security for demo since sourceforge.net
#*  seems to be using mirrored servers that do not share session info.
#****************************************************************************
if (!OBIB_DEMO_FLG) {
	$pages = array(
		'opac'=>'../opac/index.php',
		'circulation'=>'../circ/memberForms.php',//'../circ/index.php',
		'cataloging'=>'../catalog/srchForms.php',//../catalog/index.php',
		'admin'=>'../admin/index.php',
		'tools'=>'../tools/index.php',
		'reports'=>'../reports/index.php',
	);
	$returnPage = $pages[$tab];
	$_SESSION["returnPage"] = $returnPage;

	#****************************************************************************
	#*  Checking to see if session variables exist
	#****************************************************************************
	if (!isset($_SESSION["userid"]) or ($_SESSION["userid"] == "")) {
		// If siteId is given, pass it on. This allows for an easy link to be setup
		// on the desktop of a certain site
		if(isset($_REQUEST['selectSite'])){
			header("Location: ../shared/loginform.php?selectSite=" . $_REQUEST['selectSite']);
		} else {
			header("Location: ../shared/loginform.php");
		}	
		exit();
	}

	#****************************************************************************
	#*  Checking authorization for this tab
	#*  The session authorization flags were set at login in login.php
	#****************************************************************************
	if ($tab == "circulation"){
		if (!$_SESSION["hasCircAuth"]) {
			header("Location: ../circ/noauth.php");
			exit();
		} elseif (isset($restrictToMbrAuth) and !$_SESSION["hasCircMbrAuth"]) {
			header("Location: ../circ/noauth.php");
			exit();
		}
	} elseif ($tab == "cataloging") {
		// I would like to make a distinction, as Circulation users should be able to
		// view the catalogue as OPAC users.
		// If hasCircAuth allow biblio_search.php
		if (!$_SESSION["hasCatalogAuth"]) {
			if(!((basename($_SERVER['PHP_SELF']) == "srchForms.php") && $_SESSION["hasCircAuth"])){
				header("Location: ../catalog/noauth.php");
				exit();
			}
		}
	} elseif ($tab == "admin") {
		if (!$_SESSION["hasAdminAuth"]) {
			header("Location: ../admin/noauth.php");
			exit();
		}
	} elseif ($tab == "reports") {
		if (!$_SESSION["hasReportsAuth"]) {
			header("Location: ../reports/noauth.php");
			exit();
		}
	} elseif ($tab == "tools") {
		if (!$_SESSION["hasToolsAuth"]) {
			header("Location: ../tools/noauth.php");
			exit();
		}
	}
}

#****************************************************************************
#*  Checking to see if we are in demo mode and if we should not execute this
#*  page.
#****************************************************************************
if (isset($restrictInDemo) && $restrictInDemo && OBIB_DEMO_FLG) {
	include(REL(__FILE__, "../shared/demo_msg.php"));
}
