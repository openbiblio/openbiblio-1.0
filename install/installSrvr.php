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
  switch ($_REQUEST['mode']) {
    case 'createConstFile':
      $path = REL(__FILE__, "..");
      $fn = $path . "/database_constants.php";
      $content = '$dbConst["host"] ='     .$_REQUEST['host'].";  \n".
		             '$dbConst["username"] =' .$_REQUEST['user'].";  \n".
		             '$dbConst["pwd"] ='      .$_REQUEST['passwd'].";  \n".
		             '$dbConst["database"] ='   .$_REQUEST['db'].";  \n".
                 '$dbConst["mode"] ='     .'haveConst'.";  \n";

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
      break;
  }
	#-.-.-.-.-.- end special case -.-.-.-.-.-.-

 	require_once(REL(__FILE__, "../classes/InstallQuery.php"));
	require_once(REL(__FILE__, "../classes/UpgradeQuery.php"));

  //echo "in installSrvr: host=".$dbConst["host"]."; user=".$dbConst["username"]."; pw=".$dbConst["pwd"]."; db=".$dbConst["database"]."<br />\n";
  //print_r($dbConst);echo "<br /> \n";
	$installQ = new InstallQuery($dbConst);

	switch ($_REQUEST['mode']){
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'connectDBServer':
      if (($dbConst["host"] == '') || ($dbConst["host"] != 'x.x.x')) {
        $msg = T('Denied, No DB host defined');
      } else {
			  $msg = $installQ->getDbServerVersion();
      }
			echo $msg;
			break;

    case 'createNewDB':
			$resp = $installQ->createDatabase($_REQUEST['db'], $_REQUEST['user']);
			echo $resp;
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
