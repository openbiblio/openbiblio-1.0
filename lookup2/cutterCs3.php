<?PHP
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/global_constants.php");
require_once("../classes/Query.php");

class ctrQuery extends Query {

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
		if (! $this->_query($sql, "Error in trying to match name in cutter table.")) {
			return "???";	
		} else {
			$array = $this->_conn->fetchRow(); 
			$name = $array['name'];	
			return $name;
		}
	}
	function getNmbrPt2($theName) {
		$sql = "select theNmbr from cutter where theName = '" . $theName . "'";
		if (! $this->_query($sql, "Error in trying to fetch number from cutter table.")) {
			return "XXX";	
		} else {
			$array = $this->_conn->fetchRow(); 
			$nmbr = $array['theNmbr'];
			return $nmbr;
		}
	}
}

function getCutter ($aName) {
	##### implementation of Cutter - Sanborn 3-digit table
	//echo "cutter input string: " . $aName . "<br />";

	## remove spaces from text before comparrison to table entries
	$aName = str_replace(" ", "", $aName);

	## what follows, while simpler only works with mySQL ver 4.1 and later
//	$ctrQ = new ctrQuery();
// 	$ctrQ->connect();
//  if ($ctrQ->errorOccurred()) {
//    $ctrQ->close();
//    displayErrorPage($ctrQ);
//  } 
//
//  $ctrQ->execSelect($aName);
//  $result = $ctrQ->fetchfield('theNmbr');
//  $ctrQ->close();
//	return substr($aName,0,1) . $result;

	## this more complex process works all the time.
	$ctrQ = new ctrQuery();
 	$ctrQ->connect();
  if ($ctrQ->errorOccurred()) {
    $ctrQ->close();
    displayErrorPage($ctrQ);
  } 
	$rslt1 = $ctrQ->getNmbrPt1($aName);
	$ctrQ->close();

	$ctrQ = new ctrQuery();
 	$ctrQ->connect();
  if ($ctrQ->errorOccurred()) {
    $ctrQ->close();
    displayErrorPage($ctrQ);
  } 
	$rslt2 = $ctrQ->getNmbrPt2($rslt1);
	$ctrQ->close();

	return substr($aName,0,1) . $rslt2;
}
//echo "loaded cs3_cutter file. <br />";
?>
