<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

session_cache_limiter(null);

$tab = "opac";
$nav = "home";
$focus_form_name = "catalog_search";
$focus_form_field = "searchText";

Page::header_opac(array('nav'=>$nav, 'title'=>''));

$lookup = "N";
if (isset($_GET["lookup"])) {
	$lookup = "Y";
}

include(REL(__FILE__, "../shared/searchbox.php"));

Page::footer();
