<?php
require_once("../shared/common.php");
require_once("../model/Staff.php");

class ObServer {
        public static function check_hmac() {
            $headers = ObServer::get_headers();
                if(isset($headers['Authcheck'])){
                        $matches = array();
                        preg_match('/Token token="(.*)"/', $headers['Authcheck'], $matches);
                        if(isset($matches[1])){
                            $token = $matches[1];
                        } else {
                            return 0;
                        }
                }
                if (Settings::get('hmac_timeout')) {
                        $earliestLegitSendTime = time() - (Settings::get('hmac_timeout') * 60);
                } else {
                        $earliestLegitSendTime = time() - (28 * 24 * 60 * 60); // Four weeks ago
                }
                if (!isset($_POST['timestamp'])) {
                        return 0;
                }
                if ($earliestLegitSendTime > ($_POST['timestamp'])) {
                        return 0;
                }
                $requestor = new Staff;
                $rows = $requestor->getMatches(array('username'=>$_POST['username']));
                foreach ($rows as $row) {
                        $expected_hash = hash_hmac('md5', $_POST['mode'].'-'.$row['username'].'-'.$_POST['timestamp'], $row['secret_key']);
                }
                return ($expected_hash === $token);
        }


	public static function get_headers() {
		if (!function_exists('getallheaders'))  {
			$headers = array();
        		if (is_array($_SERVER)) {
        			foreach ($_SERVER as $name => $value) {
            				if (substr($name, 0, 5) == 'HTTP_') {
                				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            				}
        			}
        		}
          		return $headers;
		} else {
			return getallheaders();
		}

	}
}

?>
