<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * This process attempts to implement a query in accordance with
 * SRU 1.1 and CQL 1.1 specifications . As written it uses the
 * 'Dublin Core' (dc) query schema and the 'marcxml' record schema for response.
 */
	require_once (REL(__FILE__, 'olSruFunc.php'));	## support functions
	$displayXML = FALSE;

	$process = array();
	$subfields = array();
	$subfieldcount = 0;

	#### SRU Create query using data from $_POST[] and .../srchVals.php
	$queryStr = "$sruQry$lookupVal";
	if (!empty($lookupVal2)) $queryStr .= " and $sruQry2$lookupVal2";
	if (!empty($lookupVal3)) $queryStr .= " and $sruQry3lookupVal3";
	if (!empty($lookupVal4)) $queryStr .= " and $sruQry4$lookupVal4";
	if (!empty($lookupVal5)) $queryStr .= " and $sruQry5$lookupVal5";
 	$qry ="version=1.1".
	  		"&operation=searchRetrieve".
			 	"&query=".$queryStr.
			 	"&recordPacking=xml".
			 	"&maximumRecords=$postVars[maxHits]".
			 	"&recordSchema=marcxml";
	//echo "query: $qry <br />";

	#### send query to each host in turn and get response
	$resp = array();
	for($i = 0; $i < $postVars[numHosts]; $i++) {
		//echo "host: ".$postVars[hosts][$i][host]."<br />";
		
		$header = "POST ".$postVars['hosts'][$i][db]." HTTP/1.1\r\n".
					 		"HOST ".$postVars['hosts'][$i][host]."\r\n".
  					 	"Content-Type: application/x-www-form-urlencoded; charset=iso-8859-1\r\n".
  					 	"Content-Length: ".strlen($qry)."\r\n\r\n";
		//echo "header: $header <br />";
		$text = $header . $qry;

		### establish a socket for this host
		@list($theURL, $port) = explode(':', $postVars[hosts][$i][host]);
		@list($theHost, $subDir) = explode('/', $theURL);
		if(!isset($port) || empty($port)) $port = 7090;  // default port
		//echo "url: '$theHost:$port'<br />timeout=$postVars[timeout]seconds<br />";
		$fp = fsockopen($theHost, $port, $errNmbr, $errMsg, $postVars[timeout]);
		if(!$fp) {
			echo "<p class=\"error\">you requested:</strong></p>";
			echo "<fieldset>".nl2br($text)."</fieldset>";
			echo "<p class=\"error\">via socket on port $port. </p>";
			echo "<h4>Please verify the correctness of your host URL and "
					."that your server's firewall allows access to the web via the port specified.<br />
					If you have been sucessful recently, it is possible your internet connection 
					is temporarily broken or slower than usual.<h4>";
			//trigger_error("Socket error: $errMsg ($errNmbr)");
			exit;
		}

		### send the query
		fputs($fp, $text);
		
		### Added timeout on the stream itself (also in loop)- LJ
		stream_set_timeout($fp, $postVars[timeout]);
		$info = stream_get_meta_data($fp); 
		
		### fetch the response, if any
		//echo "preparing to read any responses <br />";
		$hitList = '';
    $headerdone = false;
    while(!feof($fp) && (!$info['timed_out'])) {
      $line = fgets($fp, 2048);
	  	$info = stream_get_meta_data($fp);
      if (!line) {
      	echo "Failure while reading response from $theHost <br />";
      	break;
			} else if (strcmp($line, "\r\n") == 0) {
      	// read the header
      	$headerdone = true;
      } else if ($headerdone) {
      	// header has been read. now build the contents
      	$hitList .= $line;
      }
      else {
        // http header lines
        //echo "$line <br />";
			}
  	}
  	if (!empty($hitList)) $resp[$i] = $hitList;
  	fclose($fp);
		//echo "from $theHost:<br />";print_r($resp[$i]);echo "<br />---------<br />";
	}
	//echo "complete SRU raw reply:<br />";print_r($resp);echo "<br />---------<br />";
	//$displayXML=true;
	//showXML($resp,$displayXML); //for debugging purposes

	### parse downloaded XML and create
	$xml_parser = xml_parser_create();
	$ttlHits = 0;
	for($i = 0; $i < $postVars[numHosts]; $i++) {
	  if (!empty($resp[$i])) {
			xml_parse_into_struct($xml_parser, $resp[$i], $hostRecords[$i]);

			//echo "host #$i strucured:<br />";print_r($hostRecords[$i]);echo "<br />---------<br />";

			list($num_records, $marc[$i]) = get_marc_fields($hostRecords[$i]);
			$msg = $marc[$i][0]['diagMsg'];
			if (($num_Records == 0) && (!empty($msg))) {
				echo "Host Diagnostic Response: $msg";
				exit;
			}
			//echo "host #$i marc:<br />";print_r($marc[$i]);echo "<br />---------<br />";
			$ttlHits += $num_records;
		}
	}
	xml_parser_free($xml_parser);

?>
