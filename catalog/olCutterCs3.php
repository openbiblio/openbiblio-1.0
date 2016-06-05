<?PHP
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/global_constants.php");
//require_once("../classes/Query.php");
require_once("../classes/DBTable.php");
/*
class ctrQuery {
	function ctrQuery() {
		$this->db = new Query();
	}

	function execSelect($aName) {
		$sql = "select theNmbr from cutter ";
		$sql .= "where theName in (";
	  $sql .= "select max(theName) from cutter where theName < '" . $aName . "')";
	  return $this->_query($sql, "Error accessing the cutter table.");
	}

	function fetchField($fldName) {
    $array = $this->_conn->fetchRow();
		return $array[$fldName];
	}

	function getNmbrPt1($theName) {
		$sql .= "select max(theName) as name from cutter where theName < '" . $theName . "'";
		if (! $this->select1($sql, "Error in trying to match name in cutter table.")) {
			return "???";	
		} else {
			$array = $this->fetchRow();
			$name = $array['name'];	
			return $name;
		}
	}
	function getNmbrPt2($theName) {
		$sql = "select theNmbr from cutter where theName = '" . $theName . "'";
		if (! $this->select01($sql, "Error in trying to fetch number from cutter table.")) {
			return "XXX";	
		} else {
			$array = $this->fetchRow();
			$nmbr = $array['theNmbr'];
			return $nmbr;
		}
	}
}
*/
function getCutter ($aName) {
	##### implementation of Cutter - Sanborn 3-digit table

	## remove apostrophes, commas from text before comparrison to table entries
	$aName = str_replace(array(",","'"), "", $aName);
	//echo "looking for '$aName'<br />";

	$ctrQ = new DBTable();
	$ctrQ->setName('cutter');
	$ctrQ->setFields(array('theName'=>'string', 'theNmbr'=>'number'));
	$ctrQ->setKey('theName');

	$sql = $ctrQ->mkSQL("SELECT MAX(`theName`) as name FROM `cutter` WHERE `theName` < '$aName'" );
	$rslt1 = $ctrQ->select1($sql);
	$name = $rslt1[name];
	//echo "using key '$name' <br />";

	$sql = $ctrQ->mkSQL("SELECT `theNmbr` FROM `cutter` WHERE `theName` = '$name'" );
	$rslt2 = $ctrQ->select1($sql);
  return substr($aName,0,1) . $rslt2[theNmbr];
}
?>
