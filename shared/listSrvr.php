<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_REQUEST);echo "<br />";

	function getDbData ($db) {
		$set = $db->getSelect();
		foreach ($set as $val => $desc) {
			$list[$val] = $desc;
		}
		return $list;
	}
	function getDmData ($db) {
		$set = $db->getAll('description');
		while ($row = $set->fetch_assoc()) {
		  $list[$row['code']] = $row['description'];
		}
		return $list;
	}
	
	switch ($_REQUEST['mode']) {
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
		break;

	case 'getCalendarList':
		require_once(REL(__FILE__, "../model/Calendars.php"));
		$db = new Calendars;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

	case 'getCollectionList':
		require_once(REL(__FILE__, "../model/Collections.php"));
		$db = new Collections;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

	case 'getMediaList':
		require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$db = new MediaTypes;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

	case 'getMbrTypList':
		require_once(REL(__FILE__, "../model/MemberTypes.php"));
		$db = new MemberTypes;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

	case 'getSiteList':
		require_once(REL(__FILE__, "../model/Sites.php"));
		$db = new Sites;
		$list = getDbData($db);
		echo json_encode($list);
	  break;

	case 'getStateList':
		require_once(REL(__FILE__, "../model/States.php"));
		$db = new States;
		$list = getDmData($db);
		echo json_encode($list);
	  break;

	default:
		  echo "<h4>".T("invalid mode")."@listSrvr.php: &gt;".$_REQUEST['mode']."&lt;</h4><br />";
		break;
	}

?>
