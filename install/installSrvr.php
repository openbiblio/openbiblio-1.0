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

	$installQ = new InstallQuery();
	switch ($_REQUEST['mode']){
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'connectDB':
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
			//echo "fetching version\n";
			$version = $installQ->getCurrentDatabaseVersion();
			if (!$version || empty($version)) {
				echo T("noDB");
			} else {	
				echo $version;
			}
			break;
			
		case 'getLocales':
			//echo "fetching locales\n";
			$arr_lang = Localize::getLocales();
			foreach ($arr_lang as $langCode => $langDesc) {
				echo '<option value="'.H($langCode).'">'.H($langDesc)."</option>\n";
			}
			break;
			
		case 'doFullInstall':
			//echo "full install underway\n";
			echo 	$installQ->freshInstall($Locale, $_POST['installTestData']);
			break;
			
		case 'doDbUpgrade':
			//echo "upgrading database\n";
			$upgradeQ = new UpgradeQuery();
			$results = $upgradeQ->performUpgrade_e('', 'obupgrade_test');
			echo json_encode($results);
			break;
			
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
			break;
	}
?>
