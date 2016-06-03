<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_POST);echo "<br />";

/**
 * back-end API for various pull-down lists based on DB tables
 * @author Fred LaPlante
 */

	function getDbData ($db) {
		$set = $db->getSelect();
		foreach ($set as $val => $desc) {
			$list[$val] = $desc;
		}
		return $list;
	}
	function getDmData ($db) {
		$set = $db->getAll('description');
		//while ($row = $set->fetch_assoc()) {
        foreach ($set as $row) {
		  $list[$row['code']] = $row['description'];
		}
		return $list;
	}
	
	switch ($_POST['mode']) {
    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getAudienceList':
		require_once(REL(__FILE__, "../model/Biblios.php"));
		$db = new Biblios;
		$sql = "SELECT subfield_data, COUNT(*) as count " .
 			"FROM biblio_subfield sf, biblio_field f " .
 			"WHERE f.tag='521' AND sf.fieldid=f.fieldid " .
 			"GROUP BY subfield_data " .
 			"ORDER BY COUNT DESC " .
 			"LIMIT 10";
		$rslt = $db->select($sql);
		//while ($col = $rslt->fetch_assoc()) {
        foreach ($rslt as $row) {
			$list[$col['subfield_data']] = $col['subfield_data'];
		}
		echo json_encode($list);
	  break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getCalendarList':
		require_once(REL(__FILE__, "../model/Calendars.php"));
		$db = new Calendars;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getCollectionList':
		require_once(REL(__FILE__, "../model/Collections.php"));
		$db = new Collections;
		$list = getDmData($db);
		echo json_encode($list);
	  break;
	case 'getDefaultCollection':
		require_once(REL(__FILE__, "../model/Collections.php"));
        $db = new Collections;
        $rslt = $db->getDefault();
		echo json_encode($rslt);
        break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getInputTypes':
		require_once(REL(__FILE__, "../model/MaterialFields.php"));
		$db = new MaterialFields;
		$sql = "SHOW COLUMNS FROM material_fields";
		$rslt = $db->select($sql);
		//while ($col = $rslt->fetch_assoc()) {
        foreach ($rslt as $col) {
			if ($col['Field'] == 'form_type') break;
		}
		$enum = $col['Type'];
		echo $enum;
	  break;
    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getLocaleList':
		$arr_lang = Localize::getLocales();
		foreach ($arr_lang as $langCode => $langDesc) {
			//echo '<option value="'.H($langCode).'">'.H($langDesc)."</option>\n";
			$list[$langCode] = $langDesc;
		}
		echo json_encode($list);
		break;

	case 'getMediaMarcTags':
		require_once(REL(__FILE__, "../model/MaterialFields.php"));
		$db = new MaterialFields;
		$sql = "SELECT * FROM `material_fields` WHERE `material_cd`={$_POST['media']} ORDER BY tag,subfield_cd";
		$rslt = $db->select($sql);
		//while ($row = $rslt->fetch_assoc()) {
        foreach ($rslt as $row) {
			$tags[$row['tag'].'$'.$row['subfield_cd']] = $row['label'];
		}
		echo json_encode($tags);
		break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getMediaList':
		require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$db = new MediaTypes;
		$list = getDmData($db);
		echo json_encode($list);
	  break;
	case 'getMediaIconUrls':
		require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$db = new MediaTypes;
		$rslt = $db->getIcons();
		//while ($row = $rslt->fetch_assoc()) {
        foreach ($rslt as $row) {
		  $list[$row['code']] = $row['image_file'];
		}
		echo json_encode($list);
	  break;
	case 'getDefaultMaterial':
		require_once(REL(__FILE__, "../model/MediaTypes.php"));
        $db = new MediaTypes;
        $rslt = $db->getDefault();
		echo json_encode($rslt);
        break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getMbrTypList':
		require_once(REL(__FILE__, "../model/MemberTypes.php"));
		$db = new MemberTypes;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
		break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getSiteList':
		require_once(REL(__FILE__, "../model/Sites.php"));
		$db = new Sites;
		$list = getDbData($db);
		echo json_encode($list);
	  break;
	case 'getDefaultSite':
/*
		require_once(REL(__FILE__, "../model/Sites.php"));
        $db = new Sites;
        $rslt = $db->getDefault();
		echo json_encode($rslt);
*/
        $siteId = Settings::get('library_name');
echo $siteId;
        return $siteId;
        break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getStateList':
		require_once(REL(__FILE__, "../model/States.php"));
		$db = new States;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getStatusCds':
		require_once(REL(__FILE__, "../model/CopyStatus.php"));
        $db = new CopyStatus;
/*
        $rslt = $db->getStatusCds();
        //while ($row = $rslt->fetch()) {
        foreach ($rslt as $row) {
            //print_r($row);
            $cdData[] = $row;
        }
		echo json_encode($cdData);
*/
		$list = getDmData($db);
		echo json_encode($list);
		break;
	case 'getDefaultStatusCd':
		require_once(REL(__FILE__, "../model/CopyStatus.php"));
        $db = new CopyStatus;
        $rslt = $db->getDefault();
		echo json_encode($rslt);
        break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getThemeList':
		require_once(REL(__FILE__, "../model/Themes.php"));
		$db = new Themes;
		$set = $db->getAll('theme_name');
		//while ($row = $set->fetch_assoc()) {
        foreach ($set as $row) {
		  $list[$row['themeid']] = $row['theme_name'];
		}
		echo json_encode($list);
	  break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	case 'getValidations':
		require_once(REL(__FILE__, "../model/Validations.php"));
		$db = new Validations;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	default:
		  echo "<h4>".T("invalid mode")."@listSrvr.php: &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

?>
