<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Translation table for Russian (ru).
 * typically accessed using the T("...") function
 * provided in the ..../shared/templates.php
 * @author Jane Sandberg
 */

#****************************************************************************
#* Formats
#****************************************************************************
$trans["headerDateFormat"]	 = "d.m.Y";

#****************************************************************************
#* Common translation text, arranged alphabetically
#****************************************************************************
####### C #######
$trans["Circulation"] = "Sirkulasi";
$trans["City"] = "Kota";
####### D #######
$trans["Database"] = "Database";
####### K #######
$trans["Keyword"] = "Kata kunci";
####### P #######
$trans["pwd"] = "Kata sandi";

$list = getPlugIns('tran.tran');
	for ($x=0; $x<count($list); $x++) {
		include($list[$x]);
	}

