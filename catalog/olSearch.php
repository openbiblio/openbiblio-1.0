<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

		#### prepare user search criteria ####
		require_once(REL(__FILE__, 'olSrchVals.php'));
		
		#### perform the search ####
		$numHosts = $postVars[numHosts];
		//print("will be trying $numHosts host(s)<br />");
		require_once (REL(__FILE__, 'olYazSrch.php'));

		#### process the results ####
		$initialCond = false;
		$rcd['ttlHits'] = $ttlHits;
		$rcd['maxHits'] = $postVars['maxHits'];
		//echo "ttl hits= $ttlHits<br />";
		
		if ($ttlHits == 0) {
			## TOO FEW 
			/* Response format:
		  $s =  "{'ttlHits':$ttlHits,'maxHits':$postVars[maxHits],".
						"'msg':'$msg1',".
						"'srch1':{'byName':'$srchByName','lookupVal':'$lookupVal'},".
						"'srch2':{'byName':'$srchByName2','lookupVal':'$lookupVal2'}".
					  "}";
			echo $s;
			*/
		  $rcd['msg'] = T('Nothing Found');
		  $srch['byName'] = $srchByName;
		  $srch['lookupVal'] = $lookupVal;
		  $rcd['srch1'] = json_encode($srch);
		  $srch['byName'] = $srchByName2;
		  $srch['lookupVal'] = $lookupVal2;
		  $rcd['srch2'] = json_encode($srch);
		  echo json_encode($rcd);
		}

		else if ($ttlHits > $postVars[maxHits]) {
			## TOO MANY
			/* Response format:
			$msg1 = T('lookup_tooManyHits');
			$msg2 = T('lookup_refineSearch');
		  $s =  "{'ttlHits':'$ttlHits','maxHits':'$postVars[maxHits]',".
						"'msg':'$msg1', 'msg2':'$msg2' ".
						"}";
			echo $s;
			*/
		  $rcd['msg'] = T('lookup_tooManyHits');
		  $rcd['msg2'] = T('lookup_refineSearch');
		  echo json_encode($rcd);
		}

		else if ($ttlHits > 0) {
			## GOOD COUNT
			if ($numHosts > 0) {
				$postit = true;
				$_POST['ttlHits'] = $ttlHits;
				$_POST['numHosts'] = $numHosts;
				//$_POST['postVars'] = $postVars; // for debugging
				$rslt = array();
				$xml_parser = xml_parser_create();
				for ($h=0; $h<$numHosts; $h++) {
						$rslt[$h] = doOneHost($h, $hits, $id);
				}
				xml_parser_free($xml_parser);
				$_POST[data] = $rslt;
			}
      echo json_encode($_POST);
	}
	
	//error_reporting($err_level);		## restore original value
	//set_error_handler($err_fnctn);	## restore original handler
?>
