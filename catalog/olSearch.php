<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

		#### prepare user search criteria ####
		require_once(REL(__FILE__, '../catalog/olSrchVals.php'));
		
		#### perform the search ####
		$numHosts = $postVars[numHosts];
		//print("will be trying $numHosts host(s)<br />");
		if (function_exists(yaz_connect))
			require_once (REL(__FILE__, '../catalog/olYazSrch.php'));
		else	{
			require_once (REL(__FILE__, '../catalog/olSruSrch.php'));
		}
		
		#### process the results ####
		$initialCond = false;
		$rcd['ttlHits'] = $ttlHits;
		$rcd['maxHits'] = $postVars['maxHits'];
		//echo "ttl hits= $ttlHits<br />";
		
		if ($ttlHits == 0) {
			## TOO FEW 
			/* error encountered */
			for ($i=0; $i<$numHosts; $i++) {
				if ($errMsg[$i] != '') {
					echo $errMsg[$i];
				}
			}

			/* no errors Response format:
  			"{'ttlHits':$ttlHits,'maxHits':$postVars[maxHits],".
				"'msg':'$msg1',".
				"'srch1':{'byName':'$srchByName','lookupVal':'$lookupVal'},".
				"'srch2':{'byName':'$srchByName2','lookupVal':'$lookupVal2'}".
			  "}";
			*/
		  $rcd['msg'] = T("Nothing Found");
		  $srch1['byName'] = $srchByName;
		  $srch1['lookupVal'] = $lookupVal;
		  $rcd['srch1'] = json_encode($srch1);
			if (isset($srchByName2)) {
			  $srch2['byName'] = $srchByName2;
			  $srch2['lookupVal'] = $lookupVal2;
			  $rcd['srch2'] = json_encode($srch2);
			} else {
			  $srch2['byName'] = 0;
			  $srch2['lookupVal'] = '';
			  $rcd['srch2'] = json_encode($srch2);
			}
		  echo json_encode($rcd);
		}

		else if ($ttlHits > $postVars[maxHits]) {
			## TOO MANY
			/* Response format:
			$msg1 = T("lookup_tooManyHits");
			$msg2 = T("lookup_refineSearch");
		  $s =  "{'ttlHits':'$ttlHits','maxHits':'$postVars[maxHits]',".
						"'msg':'$msg1', 'msg2':'$msg2' ".
						"}";
			echo $s;
			*/
		  $rcd['msg'] = T("lookup_tooManyHits");
		  $rcd['msg2'] = T("lookup_refineSearch");
		  echo json_encode($rcd);
		}

		else if ($ttlHits > 0) {
			## GOOD COUNT
			if ($numHosts > 0) {
				$postit = true;
				$rslt = array();
				//$_POST['postVars'] = $postVars; // for debugging
				$xml_parser = xml_parser_create();

				if (function_exists(yaz_connect)) {
					for ($h=0; $h<$numHosts; $h++) {
							$rslt[$h] = doOneHost($h, $hits, $id);
					}
				}		

				else {
					### no PHP_YAZ processor availabpe
					for ($h=0; $h<$numHosts; $h++) {
					  if (!empty($hits[$h])) {
							xml_parse_into_struct($xml_parser, $hits[$h], $hostRecords[$h]);
							list($num_records, $rslt[$h]) = get_marc_fields_from_xml($hostRecords[$h]);
							$msg = $rslt[$h][0]['diagMsg'];
							if (($num_Records == 0) && (!empty($msg))) {
								echo "Host Diagnostic Response: $msg<br />\n";
								echo "----<br />Details...<br />\n";
								echo $qry."<br />\n";
								print_r($rslt);echo "<br />\n";
								exit;
							}
							//echo "host #$h rslt:<br />";print_r($rslt[$h]);echo "<br />---------<br />";
							$ttlHits += $num_records;
						}
					}

				}
				xml_parser_free($xml_parser);
				$_POST['data'] = $rslt;
				$_POST['ttlHits'] = $ttlHits;
				$_POST['numHosts'] = $numHosts;
			}
      echo json_encode($_POST);
		}
	
		//error_reporting($err_level);		## restore original value
		//set_error_handler($err_fnctn);	## restore original handler
?>
