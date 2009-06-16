<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../shared/global_constants.php"));
require_once(REL(__FILE__, "../classes/Query.php"));
require_once(REL(__FILE__, 'LookupOpts.php'));

/******************************************************************************
 * LookupQuery data access component for lookupOpts table
 *
 * @author Fred LaPlante <flaplante@flos-inc.com>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class LookupOptsQuery extends Query {

  /****************************************************************************
   * Executes a query
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect() {
    $sql = "select * from lookup_settings";
    return $this->_query($sql, "Error accessing lookup settings information.");
  }

  /****************************************************************************
   * Fetches the row from the query result and populates the Settings object.
   * @return settings object or false if no more rows to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    global $postVars;
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }
		//echo "from fetch:<br />";print_r($array);echo "<br />";
    $set = new LookupOpts();
    $set->setProtocol($array["protocol"]);
    $set->setMaxHits($array["max_hits"]);
    $set->setKeepDashes($array["keep_dashes"]);
    $set->setCallNmbrType($array["callNmbr_type"]);
    $set->setAutoDewey($array["auto_dewey"]);
    $set->setDefaultDewey($array["default_dewey"]);
    $set->setAutoCutter($array["auto_cutter"]);
    $set->setCutterType($array["cutter_type"]);
    $set->setCutterWord($array["cutter_word"]);
    $set->setAutoCollect($array["auto_collect"]);
    $set->setFictionName($array["fiction_name"]);
    $set->setFictionCode($array["fiction_code"]);
    $set->setFictionLoC($array["fiction_loc"]);
    $set->setFictionDew($array["fiction_dewey"]);

    return $set;
  }

  /****************************************************************************
   * Update the row in the lookup_settings table.
   * @param $set LookupOpts object to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($set) {
    $sql = $this->mkSQL("update lookup_settings set "
                        . "protocol=%Q, max_hits=%N, "
                        . "keep_dashes=%Q, callNmbr_type=%Q, "
                        . "auto_dewey=%Q, default_dewey=%Q, "
                        . "auto_cutter=%Q, cutter_type=%Q, "
                        . "cutter_word=%Q, auto_collect=%Q, "
                        . "fiction_name=%Q, fiction_code=%Q, "
                        . "fiction_loc=%Q, fiction_dewey=%Q  ",
                        $set->getProtocol(), $set->getMaxHits(),
                        $set->getKeepDashes()?"y":"n", $set->getCallNmbrType(),
                        $set->getAutoDewey()?"y":"n", $set->getDefaultDewey(),
                        $set->getAutoCutter()?"y":"n", $set->getCutterType(),
                        $set->getCutterWord(),
                        $set->getAutoCollect()?"y":"n",
                        $set->getFictionName(), $set->getFictionCode(),
                        $set->getFictionLoC(), $set->getFictionDew()
                        );
		//echo "sql=$sql <br />";
    return $this->_query($sql, "Error updating lookup settings information");
  }

}

function makeOptsDataSet($array) {
  $set = new LookupOpts();
  $set->setProtocol($array["protocol"]);
  $set->setMaxHits($array["maxHits"]);
  $set->setKeepDashes($array["keepDashes"]);
  $set->setCallNmbrType($array["callNmbrType"]);
  $set->setAutoDewey($array["autodewey"]);
  $set->setDefaultDewey($array["defaultDewey"]);
  $set->setAutoCutter($array["autoCutter"]);
  $set->setCutterType($array["cutterType"]);
  $set->setCutterWord($array["cutterWord"]);
  $set->setAutoCollect($array["autoCollect"]);
  $set->setFictionName($array["fictionName"]);
  $set->setFictioncode($array["fictionCode"]);
  $set->setFictionLoC($array["fictionLoC"]);
  $set->setFictionDew($array["fictionDew"]);

  return $set;
}
function updateOpts ($array) {
	$optQ = new LookupOptsQuery();
 	$optQ->connect();
  if ($optQ->errorOccurred()) {
    $optQ->close();
    displayErrorPage($optQ);
  }

	$set = makeOptsDataSet($array);
	return $optQ->update($set);
}

function getOpts() {
  global $postVars;
  global $useYAZ,$useSRU;
  global $noise_words;

    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);

    $optQ = new LookupOptsQuery();
    $optQ->connect();
    if ($optQ->errorOccurred()) {
      $optQ->close();
      displayErrorPage($optQ);
    }
    $optQ->execSelect();
    if ($optQ->errorOccurred()) {
      $optQ->close();
      displayErrorPage($optQ);
    }
    $opt = $optQ->fetchRow();
    $postVars["protocol"] = $opt->getProtocol();
    $postVars["maxHits"] = $opt->getMaxHits();
    $postVars["keepDashes"] = $opt->getKeepDashes();
    $postVars["callNmbrType"] = $opt->getCallNmbrtype();
    $postVars["autoDewey"] = $opt->getAutoDewey();
    $postVars["defaultDewey"] = $opt->getDefaultDewey();
    $postVars["autoCutter"] = $opt->getAutoCutter();
    $postVars["cutterType"] = $opt->getCutterType();
    $postVars["cutterWord"] = $opt->getCutterWord();
    $postVars["autoCollect"] = $opt->getAutoCollect();
    $postVars["fictionName"] = $opt->getFictionName();
    $postVars["fictionCode"] = $opt->getFictioncode();
    $postVars["fictionLoC"] = $opt->getFictionLoC();
    $postVars["fictionDew"] = $opt->getFictionDew();

    ## not yet in database or user opts screens, but should be!!!!
    $postVars["timeout"] = 60;
    $postVars[noiseWords] = 'a an and for of the this those';

    $optQ->close();
}
?>
