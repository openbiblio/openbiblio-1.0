<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once("../model/Biblios.php");


	function getBibsMissingCalls() {
		$sql = 'SELECT bibid FROM biblio_field ' .
			'GROUP BY bibid ' .
			"HAVING COUNT(CASE WHEN tag='099' THEN 1 END) = 0";

		$call = new Biblios;
                $rslt = $call->select($sql);
                while ($row = $rslt->fetch_assoc()) {
                        $results[] = $row['bibid'];
                }

		return $results;
	}

	function getProposedCallNumbers($schemata) {
		$sql = 'SELECT subfield_data, tag, sf.bibid ' .
			'FROM biblio_subfield sf, biblio_field f ' .
			'WHERE f.fieldid=sf.fieldid AND ' .
			"sf.subfield_cd='a' AND " .
			"f.tag IN ('" . implode("', '", $schemata) . "') AND " .
			"sf.bibid IN (" .
				'SELECT bibid FROM biblio_field ' .
				'GROUP BY bibid ' .
				"HAVING COUNT(CASE WHEN tag='099' THEN 1 END) = 0)";

		$call = new Biblios;
                $rslt = $call->select($sql);
                while ($row = $rslt->fetch_assoc()) {
			$tmp['bibid'] = $row['bibid'];
			$tmp['tag'] = $row['tag'];
			$tmp['data'] = $row['subfield_data'];
                       	$results[] = $tmp;
               	}
		return $results;
		

	}

	function copyCallNo($field) {
		$sql = 'INSERT INTO biblio_field (bibid, seq, tag) ' .
			"SELECT bibid, MAX(seq)+1, '099' FROM biblio_field " .
			'WHERE bibid=' . $field['bibid'];

		$call = new Biblios;
		$call->query($sql);
		
		$sql = 'INSERT INTO biblio_subfield (bibid, fieldid, seq, subfield_cd,  subfield_data) ' .
			'VALUES (' . $field['bibid'] . ', ' . $call->insert_id . ", 1, 'a', '" .
			$field['data'] . "')";
		$call->query($sql);
	}



	switch ($_REQUEST['mode']){
	  	case 'search':
			echo '<h4>' . T('missing call numbers') . '</h4>';
			$missing = getBibsMissingCalls();
			foreach ($missing as $bibid) {
				echo $bibid . '<br />';
			}
			break;

		case 'dry-run':
			if (isset($_REQUEST['schemata'])) {
				echo '<h4>' . T('proposed call numbers') . '</h4>';
				$proposed_calls = getProposedCallNumbers($_REQUEST['schemata']);
				if (isset($proposed_calls[0])) {
					foreach ($proposed_calls as $field) {
						echo '<b>' . T('Record') . ' ' . $field['bibid'] . '</b><br />';
						echo T('Source field') . ': ' . $field['tag'] . '$a<br />';
						echo T('Call number') . ': ' . $field['data'] . '<br /><br />';
					}
				} else {
					echo "No call numbers are available to add.";
				}
			} else {
				echo 'Please choose a call number schema.';
			}
			break;
			
		case 'add':
			if (isset($_REQUEST['schemata'])) {
				echo '<h4>' . T('added call numbers') . '</h4>';
				$proposed_calls = getProposedCallNumbers($_REQUEST['schemata']);
				if (isset($proposed_calls[0])) {
					foreach ($proposed_calls as $field) {
						copyCallNo($field);
						echo '<b>' . T('Record') . ' ' . $field['bibid'] . '</b><br />';
						echo T('Source field') . ': ' . $field['tag'] . '$a<br />';
						echo T('call number') . ': ' . $field['data'] . '<br /><br />';
					}
				} else {
					echo "No call numbers are available to add.";
				}
			} else {
				echo 'Please choose a call number schema.';
			}
			break;
			
		default:
			echo "<h4>".T("invalid mode").": &gt;$_REQUEST[mode]&lt;</h4><br />";
			break;
	}
