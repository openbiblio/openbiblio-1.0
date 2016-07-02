<?php
require_once("../shared/common.php");
require_once("../model/Staff.php");

class ObServer {
	public static function check_hmac() {
		$headers = apache_request_headers();
  		if(isset($headers['Authorization'])){
    			$matches = array();
    			preg_match('/Token token="(.*)"/', $headers['Authorization'], $matches);
    			if(isset($matches[1])){
      				$token = $matches[1];
    			} else {
				return 0;
			}
  		} 
		if (Settings::get('hmac_timeout')) {
			$tokenTimedOut = time() - (Settings::get('hmac_timeout') * 60); 
		} else {
			$tokenTimedOut = time() + (28 * 24 * 60 * 60); // Four weeks later
		}
		if (!isset($_POST['timestamp'])) {
			return 0;
		}
		if ($tokenTimedOut < ($_POST['timestamp'])) {
			return 0;
		}
		$requestor = new Staff;
		$rows = $requestor->getMatches(array('username'=>$_POST['username']));
                foreach ($rows as $row) {
			$expected_hash = hash_hmac('md5', $_POST['mode'].'-'.$row['username'].'-'.$_POST['timestamp'], $row['secret_key']);
                }
		return ($expected_hash === $token);
	}

}

?>
