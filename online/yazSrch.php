<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

		require_once (REL(__FILE__, 'yazFunc.php'));	## support functions
		
		$syntax =	"usmarc";
		$srchType = "rpn";
		$hitNmbr = -1;
		$marcFlds = array();
		$subFlds = array();

		# perform a search for wanted material
		$qry = '@attr 1=' . $srchBy . ' ' . $lookupVal;
		if (!empty($lookupVal2))  {
			$qry = '@and ' . $qry . ' @attr 1=' . $srchBy2 . ' ' . $lookupVal2;
		}
		if (!empty($lookupVal3))  {
			$qry = '@and ' . $qry . ' @attr 1=' . $srchBy3 . ' ' . $lookupVal3;
		}
		if (!empty($lookupVal4))  {
			$qry = '@and ' . $qry . ' @attr 1=' . $srchBy4 . ' ' . $lookupVal4;
		}
		if (!empty($lookupVal5))  {
			$qry = '@and ' . $qry . ' @attr 1=' . $srchBy5 . ' ' . $lookupVal5;
		}
		//echo 'Query specification is: ' . htmlspecialchars($qry) . '<br />';

		//showMeComplex('host array',$postVars[hosts]);
		//echo "using $postVars[numHosts] host(s)<br />";
		for ($i=0; $i<$postVars[numHosts]; $i++) {
			//			$ptr = ($useHost == -1)?$i:$useHost;
			$ptr=$i;
			//showMeComplex("using host #$ptr",$postVars[hosts][$ptr][name]);
			//showMeComplex("using host #$ptr",$postVars[hosts][$ptr]);
			//showMeComplex("using host #$ptr",$postVars[hosts]);
			$aHost = $postVars[hosts][$ptr][host];
			$aUser = $postVars[hosts][$ptr][user];
			$aPw   = $postVars[hosts][$ptr][pw];
			//echo "connecting to: $aHost<br />";
			$connOK = yaz_connect($aHost, array("user"=>$aUser,"password"=>$aPw) );
			if (! $connOK) {
				echo 'yaz setup not successful! <br />';
				trigger_error(T("lookup_yaz_setup_failed").$postVars[hosts][$ptr][name]."<br />", E_USER_ERROR);
			} else {
				//echo 'yaz setup successful! <br />';
				$id[$ptr] = $connOK;
				$db = $postVars[hosts][$ptr][db];
				//echo "specifying db: $db<br />";
				yaz_database($id[$ptr], $db);
				yaz_syntax($id[$ptr], $syntax);
				yaz_element($id[$ptr], "F");

				//echo "sending: $qry <br />";
				if (! yaz_search($id[$ptr], $srchType, $qry)) 
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
//			$ptr = ($useHost == -1)?$i:$useHost;
			$ptr = $i;
			$error = yaz_error($id[$ptr]);
			if (!empty($error)) {
			  ## NO
				//trigger_error("Z39.50 error <br />", E_USER_ERROR);
				echo T("lookup_yazError").$error." (";
				echo yaz_errno($id[$ptr]) . ') ' . yaz_addinfo($id[$ptr]);
				echo "<br />";
			} else {
			  ## YES, we got a response!!
				$hits[$ptr] = yaz_hits($id[$ptr]);
				$ttlHits += $hits[$ptr];
				//echo "Host #$ptr {$postVars[hosts][$ptr][name]} result Count: $hits[$ptr] <br />";
			}
		}
		//echo "Total Hits=$ttlHits <br />";
?>
