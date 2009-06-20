<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

		# prepare user search criteria
		require_once(REL(__FILE__, 'srchVals.php'));
		
		$numHosts = $postVars[numHosts];
		//print("will be trying $numHosts host(s)");

	   if ($postVars[protocol] == 'YAZ') {
			//print("using YAZ protocol<br />");
			require_once (REL(__FILE__, 'yazSrch.php'));
		} else if ($postVars[protocol] == 'SRU') {
			//print("using SRU protocol<br />");
			require_once (REL(__FILE__, 'sruSrch.php'));
		} else {
			echo "Invalid protocol specified.<br />";
		}
/*
		##=========================
		## following patch from Christoph Lange of Germany
		##=========================
		//	if ($srchBy == "7") $ttlHits = 1;	// searched by ISBN
		##=========================
*/
		$initialCond = false;

		//echo "ttl hits= $ttlHits<br />";
		## TOO FEW
		if ($ttlHits == 0) {
		  $msg1 = T('lookup_nothingFound');
		  # JSON object follows
		  $s =  "{'ttlHits':$ttlHits,'maxHits':$postVars[maxHits],".
						"'msg':'$msg1',".
						"'srch1':{'byName':'$srchByName','lookupVal':'$lookupVal'},".
						"'srch2':{'byName':'$srchByName2','lookupVal':'$lookupVal2'}".
					  "}";
			echo $s;
		}
		## TOO MANY
		else if ($ttlHits > $postVars[maxHits]) {
			$msg1 = T('lookup_tooManyHits');
			$msg2 = T('lookup_refineSearch');
			 # JSON object follows
		  $s =  "{'ttlHits':'$ttlHits','maxHits':'$postVars[maxHits]',".
						"'msg':'$msg1', 'msg2':'$msg2' ".
						"}";
			echo $s;
		}
		## GOOD COUNT
		else if ($ttlHits > 0) {
			if ($numHosts > 0) {
				$postit = true;
				$_POST['ttlHits'] = $ttlHits;
				$_POST['numHosts'] = $numHosts;
//				$_POST['postVars'] = $postVars; // for debugging
				$rslt = array();
				for ($h=0; $h<$numHosts; $h++) {
					if ($postVars[protocol] == 'YAZ') {
						$rslt[$h] = doOneHost($h, $hits, $id); // build an array of host data
					}
					else if ($postVars[protocol] == 'SRU'){
					  $rslt[$h] = $marc[$h];
					}
					//	$lookupVal = "";
					//	$srchBy = "";
				}
				$_POST[data] = $rslt;
			}
      echo json_encode($_POST);
	}
	
	error_reporting($err_level);		## restore original value
	set_error_handler($err_fnctn);	## restore original handler
?>
