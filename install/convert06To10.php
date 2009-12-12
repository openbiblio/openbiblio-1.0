<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	session_cache_limiter(null);

	$tab = "install";
	$nav = "convert6to10";
	//$focus_form_name = "barcodesearch";
	//$focus_form_field = "searchText";

	Page::header(array('nav'=>$nav, 'title'=>''));
?>

<h3><?php echo T("Convert v0.6 to v1.0"); ?></h3>

<?php
	######
	# This program will read the v0.6+ db biblio tables
	# and fill an esisting empty v1.0 table set.
	# It will probably be necessary to set the db name
	# in the folowing lines. - F.L. - 8 Dec 2009
	######
	#
	require_once(REL(__FILE__, "../classes/Query.php"));
	$db = new Query;
	$oldDb = 'OpenBiblio';
	$newDb = 'openbibliowork';
	
	$sql = "TRUNCATE TABLE $newDb.`biblio`";
	$rslt = $db->Act($sql); echo "$sql<br />";

	$sql = "TRUNCATE TABLE $newDb.`biblio_field`";
	$rslt = $db->Act($sql);	echo "$sql<br />";

	$sql = "TRUNCATE TABLE $newDb.`biblio_subfield`";
	$rslt = $db->Act($sql); echo "$sql<br />";

	#### scan all existing biblio entries in biblio_id order
	$sql = "SELECT * FROM `$oldDb`.`biblio` ORDER BY `bibid` ";
	$bibs = $db->select($sql);
	$n = 0; $fldid = 1; $subid = 1;
	
	$bibSql = "INSERT INTO $newDb.`biblio` "
					. "(`bibid`,`create_dt`,`last_change_dt`,`last_change_userid`,`material_cd`,`collection_cd`,`opac_flg`) "
					. "VALUES ";
	while (($bib = $bibs->next()) != NULL) {
		$n++;
		$bibSql .= '('.$bib[bibid].',"'.$bib[create_dt].'", "'.$bib[last_change_dt].'", "'.$bib[last_change_userid].'", "'.$bib[material_cd].'", "'.$bib[collection_cd].'", "'.$bib[opac_flg].'"),';

	$fldSql = "INSERT INTO $newDb.`biblio_field` "
					. "(`bibid`,`fieldid`,`seq`,`tag`,`ind1_cd`,`ind2_cd`,`field_data`,`display`) "
					. "VALUES ";
	$subSql = "INSERT INTO $newDb.`biblio_subfield` "
					. "(`bibid`,`fieldid`,`subfieldid`,`seq`,`subfield_cd`,`subfield_data`) "
					. "VALUES ";

		### get those fields & sub-fields previosly kept in biblio table
		$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", "0", "245", NULL, NULL, NULL, NULL),';
		$bib[title] = preg_replace("/'/","''",$bib[title]);
 		$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[title].'"),'; $subid++;
		$bib[title_remainder] = preg_replace("/'/","''",$bib[title_remainder]);
		if ($bib[title_remainder]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "b", "'.$bib[title_remainder].'"),'; $subid++;}
		$bib[responsibility_stmt] = preg_replace("/'/","''",$bib[responsibility_stmt]);
		if ($bib[responsibility_stmt]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "c", "'.$bib[responsibility_stmt].'"),'; $subid++;}
    $fldid++;
    
		$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "100", NULL, NULL, NULL, NULL),';  
		$bib[author] = preg_replace("/'/","''",$bib[author]);
		if ($bib[author]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[author].'"),'; $subid++;}
    $fldid++;
    
		$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "099", NULL, NULL, NULL, NULL),';
		if ($bib[call_nmbr1]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[call_nmbr1].'"),'; $subid++;}
    $fldid++;

		$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "650", NULL, NULL, NULL, NULL),';  
		$bib[topic1] = preg_replace("/'/","''",$bib[topic1]);
		if ($bib[topic1]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "a", "'.$bib[topic1].'"),'; $subid++;}
		$bib[topic2] = preg_replace("/'/","''",$bib[topic2]);
		if ($bib[topic2]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 1, "a", "'.$bib[topic2].'"),'; $subid++;}
		$bib[topic3] = preg_replace("/'/","''",$bib[topic3]);
		if ($bib[topic3]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 2, "a", "'.$bib[topic3].'"),'; $subid++;}
		$bib[topic4] = preg_replace("/'/","''",$bib[topic4]);
		if ($bib[topic4]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 3, "a", "'.$bib[topic4].'"),'; $subid++;}
		$bib[topic5] = preg_replace("/'/","''",$bib[topic5]);
		if ($bib[topic5]) {$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 4, "a", "'.$bib[topic5].'"),'; $subid++;}
    $fldid++;
    
		### get each biblio_field entry for this biblio in MARC tag order
		$sql = "SELECT * FROM `$oldDb`.`biblio_field` WHERE (bibid=$bib[bibid]) ORDER BY `tag` ";
		$flds = $db->select($sql);
		while ($fld = $flds->next()) {
		  $tag = sprintf("%03d",$fld[tag]);
			$fldSql .= '("'.$bib[bibid].'", "'.$fldid.'", 0, "'.$tag.'", NULL, NULL, NULL, NULL),';
			$fld[field_data] = preg_replace("/'/","''",$fld[field_data]);
			$fld[field_data] = preg_replace('/"/','',$fld[field_data]);
			$subSql .= '("'.$bib[bibid].'", "'.$fldid.'", "'.$subid.'", 0, "'.$fld[subfield_cd].'", "'.$fld[field_data].'"),'; $subid++;
      $fldid++;
		}
	$bibSql = substr($bibSql,0,-1);
	//echo "biblio==>$bibSql<br />";
	$rslt = $db->Act($bibSql);
	$fldSql = substr($fldSql,0,-1);
	//echo "fields==>$fldSql<br />";
	$rslt = $db->Act($fldSql);
	$subSql = substr($subSql,0,-1);
	//echo "subFields==>$subSql<br />";
	$rslt = $db->Act($subSql);

		//if ($n=1)break; ## for bebug only
	}
	echo "$n biblio records written.<br />";
	echo "$fldid field records written.<br />";
	echo "$subid sub-field records written.<br />";
?>

</body>
</html>
