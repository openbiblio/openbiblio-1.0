<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

    require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	require_once(REL(__FILE__, '../model/Hosts.php'));
	require_once(REL(__FILE__, '../model/Opts.php'));
	require_once(REL(__FILE__, '../catalog/olSrvrFuncs.php'));	## general support functions

	## ---------------------------------- ##

	## fetch user options and post to $postVars[] for various later uses
	## --- MUST BE FIRST !!!!! ---

    ## get a set of online options
	$optr = new Opts;
	$opts = $optr->getAll();
	$postVars['opts'] = $opts->fetchAll();
	//print_r($postVars['opts']);

	## prepare list of hosts selected by user
	if (!empty($_POST['srchHost'])) {
		# but first we extract those hosts acceptable to user for THIS search
		$useHosts = array();
		foreach ($_POST as $key => $val ) {
			if (strpos($key,'srchHost') > -1) {
		  	$useHosts[] = $val;
			}
		}
	}

	# now build acceptable list from database
	$hptr = new Hosts;
	$hosts = array();
	$hSet = $hptr->getMatches(array('active'=>'y'), 'seq');
	foreach ($hSet as $row) {
		if (!empty($useHosts)) { // have user selection available
    	  	if (in_array($row['id'], $useHosts)) {
    			$hosts[] = $row;
    		}
    	} else {   // no input from user, use all in db
    		$hosts[] = $row;
    	}
    }
	$postVars['hosts'] = $hosts;
	$postVars['numHosts'] = count($hosts);
	$postVars['session'] = $_SESSION;
	//echo "in onlineServer, above switch() <br />\n";
	//print_r($postVars);echo "<br />\n";

    ## main body of code
    switch ($_POST['mode']){
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
    		echo json_encode($postVars['hosts']);
    		break;

        #-.-.-.-.-.-.-.-.-.-.-.-.-
    	case 'getOpts':
    		echo json_encode($postVars);
    		break;

        #-.-.-.-.-.-.-.-.-.-.-.-.-
    	case 'getCutter':
    		if ($postVars['cutterType'] == 'LoC') {
    			require_once('../catalog/olCutterLoc.php');
    		} elseif ($postVars['cutterType'] == 'CS3') {
    			require_once('../catalog/olCutterCs3.php');
    		} else {
    			echo "Invalid cutter type selection - ".$postVars['cutterType']." <br />";
    		}

    		$temp['cutter'] = getCutter($_POST['author']);
    		echo json_encode($temp);
    		break;

        #-.-.-.-.-.-.-.-.-.-.-.-.-
    	case 'search':
    		require_once(REL(__FILE__,'../catalog/olSearch.php')); ## will respond directly, depending on what is received
    		break;

        #-.-.-.-.-.-.-.-.-.-.-.-.-
    	case 'abandon':
    		for ($n=0; $n<$postVars['numHosts']; $n++) {
    			yaz_close($id[$n]);
    		}
    		break;

        #-.-.-.-.-.-.-.-.-.-.-.-.-
    	default:
    	   echo T("invalid mode").": ".$_POST['mode']." <br />";
    	   break;
    }

