<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
		$hitNmbr = -1;
		$marcFlds = array();
		$subFlds = array();

		#### create z39.50 RPN-style query string
		$zQuery = '@attr 1=' . $srchBy . ' ' . $lookupVal;
		if (!empty($lookupVal2))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy2 . ' ' . $lookupVal2;
		}
		if (!empty($lookupVal3))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy3 . ' ' . $lookupVal3;
		}
		if (!empty($lookupVal4))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy4 . ' ' . $lookupVal4;
		}
		if (!empty($lookupVal5))  {
			$zQuery = '@and ' . $zQuery . ' @attr 1=' . $srchBy5 . ' ' . $lookupVal5;
		}
		//echo 'z39.50 rpn-style query specification is: ' . htmlspecialchars($zQuery) . '<br />';
		
		#### create SRU CQL-style query string
		$sQuery = "$sruQry$lookupVal";
		if (!empty($lookupVal2)) $sQuery .= " and $sruQry2$lookupVal2";
		if (!empty($lookupVal3)) $sQuery .= " and $sruQry3lookupVal3";
		if (!empty($lookupVal4)) $sQuery .= " and $sruQry4$lookupVal4";
		if (!empty($lookupVal5)) $sQuery .= " and $sruQry5$lookupVal5";
		//echo 'SRU rpn-style query specification is: ' . htmlspecialchars($sQuery) . '<br />';

		//showMeComplex('host array',$postVars[hosts]);
		//echo "using $postVars[numHosts] host(s)<br />";
		for ($ptr=0; $ptr<$postVars[numHosts]; $ptr++) {
			//showMeComplex("using host #$ptr",$postVars[hosts][$ptr][name]);
			//showMeComplex("using host #$ptr",$postVars[hosts][$ptr]);
			//showMeComplex("using host #$ptr",$postVars[hosts]);
			$aHost = $postVars[hosts][$ptr][host];
			$aPort = $postVars[hosts][$ptr][port];
			$aUrl  = $aHost.':'.$aPort;
			
			$yazOpts['user'] = $postVars[hosts][$ptr][user];
			$yazOpts['password'] = $postVars[hosts][$ptr][pw];
			$aSvc	 = $postVars[hosts][$ptr][service];
			if ($aSvc != 'Z3950') {
				$yazOpts['sru'] = 'get'; // legal values are get,post,soap
				$srchType = 'cql';
				$query = $sQuery;
			}
			else {
				$yazOpts['sru'] = ''; // not used for z39.50
				$srchType = 'rpn';
				$query = $zQuery;
			}
			
			$connOK = yaz_connect($aUrl, $yazOpts );
			if (! $connOK) {
				echo 'yaz setup not successful! <br />';
				trigger_error(T("lookup_yaz_setup_failed").$postVars[hosts][$ptr][name]."<br />", E_USER_ERROR);
			} else {
				//echo 'yaz setup successful! <br />';
				$id[$ptr] = $connOK;
				yaz_database($id[$ptr], $postVars['hosts'][$ptr]['db']);
				yaz_syntax($id[$ptr], $postVars['hosts'][$ptr]['syntax']);
				yaz_element($id[$ptr], "F");

				//echo "sending: $query <br />";
				if (! yaz_search($id[$ptr], $srchType, $query)) 
					trigger_error(T("lookup_badQuery")."<br />", E_USER_NOTICE);
			}
		}

		$waitOpts = array("timeout"=>$postVars[timeout]);
		//echo "<br /> waiting $waitOpts[timeout] seconds for responses. <br />";
		yaz_wait($waitOpts);
		//yaz_wait();

		$ttlHits = 0;
		//echo "processing rslts for $numHosts host(s)<br />";
		for ($i=0; $i<$numHosts; $i++) {
			## did we make it?
			$error = yaz_error($id[$i]);
			if (!empty($error)) {
				//echo "error response from host.<br />";
				$hits[$i] = 0;
				$errMsg[$i]  = $error." on ".$postVars['hosts'][$i]['name']."<br />";
				$errMsg[$i] .= "(yaz err no. " . yaz_errno($id[$i]) . ') ' . yaz_addinfo($id[$i]) . "<br />";
				$errMsg[$i] .= "Query ==>> ".$query."<br /><br />";
			} else {
				//echo "host responded without error.<br />";
				$hits[$i] = yaz_hits($id[$i]);
				$ttlHits += $hits[$i];
				$errMsg[$i] = '';
				//echo "Host #$i {$postVars[hosts][$i][name]} result Count: $hits[$i] <br />";
			}
		}
		//echo "Total Hits=$ttlHits <br />";
?>
<?php
/*
ALTER TABLE `lookup_hosts` ADD `port` INT UNSIGNED NOT NULL DEFAULT '210' AFTER `host`, 
ADD `service` ENUM( 'Z3950', 'SRU', 'SRW' ) NOT NULL DEFAULT 'Z3950' AFTER `db` ,
ADD `syntax` VARCHAR( 20 ) NOT NULL DEFAULT 'marcxml' AFTER `element` ;
*/
?>
