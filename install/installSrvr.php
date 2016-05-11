<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
/**
 * Back-end API for those functions unique to OB installation
 * @author Fred LaPlante
 */

	$doing_install = true;
  require_once("../shared/common.php");

	#-.-.-.-.-.- special case, MUST precede reference to anything mySQLi related -.-.-.-.-.-.-
  if ($_REQUEST['mode'] == 'doCreateConst') {
      $path = REL(__FILE__, "..");
      $fn = $path . "/database_constants.php";
      $content = 'define("OBIB_HOST","'.$_REQUEST["host"].'");'."\n".
		             'define("OBIB_USERNAME","'.$_REQUEST["user"].'");'."\n".
		             'define("OBIB_PWD","'.$_REQUEST["passwd"].'");'."\n".
		             'define("OBIB_DATABASE","'.$_REQUEST["db"].'");'."\n"
                 ;
      if (!chmod($path, 0777)) {
        echo "Error: Unable to set write permission on folder '".$path."'";
        exit;
      }
      if (false === file_put_contents($fn, $content)) {
        echo 'Error: The file is NOT writable.'."\n";
        echo "Please chmod 777 the folder holding '".$fn."'";
        exit;
      }
      echo "success";
    exit;
  };
	#-.-.-.-.-.- end special case -.-.-.-.-.-.-

 	require_once(REL(__FILE__, "../classes/InstallQuery.php"));
	require_once(REL(__FILE__, "../classes/UpgradeQuery.php"));

	$installQ = new InstallQuery();
	switch ($_REQUEST['mode']){
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'connectDBServer':
			$msg = $installQ->getDbServerVersion();
			echo $msg;
			break;
			
		case 'getSettings':
			$resp = $installQ->getSettings();
			if ($resp == 'NothingFound') {
				echo "noTbl";
			} else {	
				echo $resp;
			}
			break;
			
		case 'getDbVersion':
			$version = $installQ->getCurrentDatabaseVersion();
			if (!$version || empty($version)) {
				echo T("noDB");
			} else {	
				echo $version;
			}
			break;
			
		case 'getLocales':
			$arr_lang = Localize::getLocales();
			foreach ($arr_lang as $langCode => $langDesc) {
				echo '<option value="'.H($langCode).'">'.H($langDesc)."</option>\n";
			}
			break;
			
		case 'doFullInstall':
			echo 	$installQ->freshInstall($Locale, $_POST['installTestData']);
			break;
			
		case 'doDbUpgrade':
			$upgradeQ = new UpgradeQuery($_POST['startVer']);
			if ($upgradeQ->upgradeAvailable($_POST['startVer'])) {
				$results = $upgradeQ->performUpgrade_e();
				echo json_encode($results);
      } else {
        $msg = '!-!'.T("Unknown database version").': '.$version.'. '.T("No automatic upgrade routine available.");
        echo $msg;
			}
			break;
			
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
			break;
	}
?>
