<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	require_once(REL(__FILE__, '../model/Online.php'));
	require_once(REL(__FILE__, 'olSrvrFuncs.php'));	## general support functions

	function postNewBiblio() {
		require_once(REL(__FILE__, "../model/Biblios.php"));
		require_once(REL(__FILE__, "../classes/Marc.php"));

	  $nav = "newconfirm";
	  
	  include(REL(__FILE__,'../catalog/biblioChange.php'));
  	$msg = PostBiblioChange($nav);
  	if (is_object($msg)) {
  		$rslt = json_decode($msg);
  		$bibid = $rslt->bibid;
		}
	  echo $msg;
	}

	## fetch user options and post to $postVars
	## --- MUST BE FIRST !!!!! ---
	$optr = new Opts;
	$opts = $optr->getAll();
	$postVars = $opts->next();
	
	## get default collection name
	$cptr = new myColl;
	$coll = $cptr->getDefault();
 	$postVars['defaultCollect'] = $coll['description'];

	## prepare list of hosts
	if (!empty($_POST['srchHost'])) {	
		# but first we extract those hosts acceptable to user for THIS search
		$useHosts = array();
		foreach ($_POST as $key => $val ) {
			if (strpos($key,'srchHost') > -1) {
		  	$useHosts[] = $val;
			}
		}
	}	
	
	# now build acceptable list
	$hptr = new Hosts;
	$hosts = array();
	$hSet = $hptr->getMatches(array('active'=>'y'), 'seq');
	while ($row = $hSet->next()) {
		if (!empty($useHosts)) {	
	  	if (in_array($row['id'], $useHosts)) {
				$hosts[] = $row;
			}
		} else {
				$hosts[] = $row;
		}
	}
	$postVars['hosts'] = $hosts;
	$postVars['numHosts'] = count($hosts);
	$postVars['session'] = $_SESSION;
	//print_r($postVars);

## main body of code
switch ($_REQUEST[mode]){
  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'getBiblioFields':
	  require_once(REL(__FILE__,"../catalog/biblioFields.php"));
	  // above begins execution immediately after loading
	  break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'doInsertBiblio':
	  postNewBiblio();
	  break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'getHosts':
		echo json_encode($postVars[hosts]);
		break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'getOpts':
		echo json_encode($postVars);
		break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'getCutter':
		if ($postVars[cutterType] == 'LoC') {
			require_once('../catalog/olCutterLoc.php');
		}
		elseif ($postVars[cutterType] == 'CS3') {
			require_once('../catalog/olCutterCs3.php');
		}
		else {
			echo "Invalid cutter type selection - '$postVars[cutterType]'. <br />";
			exit;
		}

		$temp['cutter'] = getCutter($_REQUEST['author']);
		echo json_encode($temp);
		break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'search':
		require_once('../catalog/olSearch.php'); ## will respond directly, depending on what is received
		break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'abandon':
		for ($n=0; $n<$postVars[numHosts]; $n++) {
			yaz_close($id[$n]);
		}
		break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	default:
	  echo T("invalid mode").": $_POST[mode] <br />";
		break;
}

?>
