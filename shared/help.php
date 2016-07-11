<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

	$tab = "help";
	$nav = "help";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

    ## load content page specified when called
    if (isset($_GET["page"])) {
    	$page = $_GET["page"];
    } else {
    	$page = "contents";
    }

    //assert('preg_match("/^[A-Za-z0-9_]+\\$/", $page)');
    include("../locale/".Settings::get('locale')."/help/".$page.".php");
