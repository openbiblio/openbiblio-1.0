<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  //require_once(REL(__FILE__, "../shared/logincheck.php"));

	if ((empty($_REQUEST[mode]))&& (!empty($_REQUEST[editMode]))) {
    $_REQUEST[mode] = $_REQUEST[editMode];
	}
	
	switch ($_REQUEST[mode]){
	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getPluginList':
			## prepare list of Material Types
			clearstatcache();
			$plugSet = array();
  		if (is_dir('../plugins')) {
				//echo "Plugin Dir found: <br />";
  	  	## find all plugin directories
				if ($dirHndl = opendir('../plugins')) {
		    	# look at all plugin dirs
		    	while (false !== ($plug = readdir($dirHndl))) {
		      	if (($plug == '.') || ($plug == '..')) continue;
  	      	if (is_dir("../plugins/$plug")) {
							//echo "plugin => $plug<br />";
		  				$list = $_SESSION['plugin_list'];
		  				$aray = explode(',', $list);
							$ok = (in_array($plug, $aray)?'Y':'N');
  	      	  $plugSet[] = array('name'=>$plug,'OK'=>$ok);
						}
  		  	}
  		  	closedir($dirHndl);
				}
			}
			echo json_encode($plugSet);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updatePluginSetting':
		  ## update the Plugins entry in Settings DB
			require_once(REL(__FILE__, "../model/Settings.php"));
			$ptr = new Settings;
			$rslt = $ptr->setOne_el('allow_plugins_flg',$_POST[allow_plugins_flg]);
			if (empty($rslt)) {
				$_SESSION['allow_plugins_flg'] = $_POST[allow_plugins_flg];
			}
			echo $rslt;
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updatePluginList':
		  $list = $_SESSION['plugin_list'];
		  $aray = explode(',', $list);
		  if ($_POST[allowPlugin] == 'N') {
		    // remove specified id from the list
				$i = array_search($_POST[id],$aray);
				array_splice($aray,$i,1);
			}
			else if ($_POST[allowPlugin] == 'Y') {
				// add id to the list
				$aray[] = $_POST[id];
			}
			else {
				echo "invalid choice: $_POST[allow_plugin]";
			}
			$list = implode($aray, ',');

			require_once(REL(__FILE__, "../model/Settings.php"));
			$ptr = new Settings;
			$rslt = $ptr->setOne_el('plugin_list',$list);
			if (empty($rslt)) {
		  	$_SESSION['plugin_list'] = $list;
			}
			echo $rslt;
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: $_REQUEST[mode]</h4>";
		break;
	}
