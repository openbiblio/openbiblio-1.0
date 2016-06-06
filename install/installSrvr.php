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
 	require_once(REL(__FILE__, "../classes/InstallQuery.php"));
	require_once(REL(__FILE__, "../classes/UpgradeQuery.php"));

	$installQ = new InstallQuery($dbConst);
	switch ($_REQUEST['mode']) {
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
        case 'connectDBServer':
			$msg = $installQ->getDbServerVersion();
			echo $msg;
			break;

        case 'createNewDB':
            $msg = $installQ->createDatabase();
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
ini_set('display_errors', 1);
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
echo $_POST;
			echo $installQ->freshInstall($Locale, $_POST['installTestData']);
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
