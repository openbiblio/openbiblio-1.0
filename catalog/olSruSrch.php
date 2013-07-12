<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * This process attempts to implement a query in accordance with
 * SRU 1.1 and CQL 1.1 specifications . As written it uses the
 * 'Dublin Core' (dc) query schema and the 'marcxml' record schema for response.
 */

	$displayXML = FALSE;

	$process = array();
	$subfields = array();
	$subfieldcount = 0;
	$sruRcrdSchema = 'marcxml';
//	$sruRcrdSchema = 'mods';
//echo "using SRU module<br />";
	
 	$qry ="operation=searchRetrieve"
 			 ."&version=1.1"
			 ."&query=$sQuery"
		 	 ."&recordPacking=xml"
			 ."&maximumRecords=$postVars[maxHits]"
			 ."&recordSchema=$sruRcrdSchema"
				;
	//echo "query: $qry <br />";
	//print_r($postVars);

	#### send query to each host in turn and get response
	$resp = array();
	for($i = 0; $i < $postVars['numHosts']; $i++) {
		//echo "host: ".$postVars[hosts][$i][host]."<br />";
		if ($postVars['hosts'][$i]['service'] == 'Z3950') {
			echo "<h3>Sorry! You have selected a Z3950 service HOST, but<br />"
					."YAZ is not installed in your server's PHP interpreter.</h3>";
			exit;
		}
		
		$header = "GET /".$postVars['hosts'][$i]['db']." HTTP/1.1\r\n".
					 		"HOST ".$postVars['hosts'][$i]['host']."\r\n".
  					 	"Content-Type: application/x-www-form-urlencoded; charset=iso-8859-1\r\n".
  					 	"Content-Length: ".strlen($qry)."\r\n\r\n";
		//echo "header: $header <br />";

		### establish a socket for this host
		$theHost = $postVars['hosts'][$i]['host'];
		$thePort = $postVars['hosts'][$i]['port'];
		if(!isset($thePort) || empty($thePort)) $thePort = 7090;  // default port
		//echo "url: '$theHost:$thePort'<br />timeout=$postVars[timeout]seconds<br />";
		$fp = fsockopen($theHost, $thePort, $errNmbr, $errMsg, $postVars['timeout']);
		$text = $header . $qry;
		if(!$fp) {
			echo "<p class=\"error\">you requested:</p>";
			echo "<fieldset>".nl2br($text)."</fieldset>";
			echo "<p class=\"error\">via socket on port $thePort. </p>";
			echo "<h4>Please verify the correctness of your host URL and "
					."that your server's firewall allows access to the web via the port specified.<br /><br />
					If you have been sucessful recently, it is possible your internet connection 
					is temporarily broken or slower than usual.<h4>";
			//trigger_error("Socket error: $errMsg ($errNmbr)");
			exit;
		}

		### send the query
		fputs($fp, $text);
echo "to host=>".nl2br($text)." at port "."$thePort<br />";
		
		### Added timeout on the stream itself (also in loop)- LJ
		stream_set_timeout($fp, $postVars[timeout]);
		$info = stream_get_meta_data($fp); 
		
		### fetch the response, if any
echo "preparing to read any responses <br />";
		$hitList = '';
    $headerdone = false;
    while(!feof($fp) && (!$info['timed_out'])) {
      $line = fgets($fp, 2048);
echo "line: <br />";print_r($line);echo "<br />";  	
	  	$info = stream_get_meta_data($fp);
echo "info: <br />";print_r($info);echo "<br />";  	
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
  	if (!empty($hitList)) $hits[$i] = $hitList;
  	fclose($fp);
echo "summary:<br />";print_r($hits[$i]);echo "<br />";  	
	}
	$ttlHits = 0;

/*
	### create and parse downloaded XML
	$xml_parser = xml_parser_create();
	for($i = 0; $i < $postVars[numHosts]; $i++) {
	  if (!empty($hits[$i])) {
			xml_parse_into_struct($xml_parser, $hits[$i], $hostRecords[$i]);

			list($num_records, $marc[$i]) = get_marc_fields_from_xml($hostRecords[$i]);
			$msg = $rslt[$i][0]['diagMsg'];
			if (($num_Records == 0) && (!empty($msg))) {
				echo "Host Diagnostic Response: $msg<br />\n";
				echo "----<br />Details...<br />\n";
				echo $qry."<br />\n";
				print_r($rslt);echo "<br />\n";
				exit;
			}
			//echo "host #$i rslt:<br />";print_r($rslt[$i]);echo "<br />---------<br />";
			$ttlHits += $num_records;
		}
	}
	xml_parser_free($xml_parser);
*/

?>
