<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	##-----------
	function verifyLCCN ($lookupVal) {
			## remove "-" and fill with "0" to make 8 char long
			$pos = strPos($lookupVal, "-");
			if ($pos > 0) {
				$lccnLeft = subStr($lookupVal,0,$pos);
				$lccnRight = subStr($lookupVal,$pos+1,6);
				$lccnRight = str_pad($lccnRight,6,"0",STR_PAD_LEFT);
				$lookupVal = $lccnLeft . $lccnRight;
			}
			return $lookupVal;
	}
	
	##-----------
	function verifyISBN($lookupVal,$keepDashes) {
	    global $postVars;
	    
			## remove any "-" char user may have entered
			if ($postVars[keepDashes]=='n') $lookupVal = str_replace("-", "", $lookupVal);
			## remove any space char user may have entered
			$lookupVal = str_replace(" ", "", $lookupVal);

			## test if its a scanned EAN code
			## '978' & '979' per Cristoph Lange of Germany
			if (((substr($lookupVal, 0,3) == "978") ||(substr($lookupVal, 0,3) == "979")) && (strlen($lookupVal) > 12))  {
				## extract the isbn from the scanner jumble
				$isbn = substr($lookupVal, 3,9);
				//echo "raw reader isbn: $isbn <br />";

				$xSum = 0;
				$add = 0;
				for ($i=0; $i<9; $i++) {
					$add = substr($isbn, $i,1);
					$xSum += (10-$i) * $add;
				}
				$xSum %= 11;
				$xSum = 11-$xSum;
				if ($xSum == 10)
					$xSum = "X";
				if ($xSum == 11)
					$xSum = "0";
				//echo 'checksum: ' . $xSum . '<br />';

				$lookupVal = $isbn . $xSum;
			}
			return substr($lookupVal,0,10);
	}
	
	##-----------
	function doOneHost($host, $hits, $id) {
		global $errMsg, $xml_parser;
		$rslt = array();
		
		if ($hits[$host] == 0) {
			$rslt['1'] = array('err'=>$errMsg[$host]);
			return $rslt;
		}
		
		for ($hit = 1; $hit<=$hits[$host]; $hit++) {
			$rcdFmt = yaz_record($id[$host],$hit,"syntax");
			if ($rcdFmt != 'XML') {
				$ar = yaz_record($id[$host],$hit,"array");
				if (! empty($ar)) {
					$rec = yaz_record($id[$host],$hit,'string');
					$rslt[$hit] = get_marc_fields_from_x3950($ar, true, $hit, $host); // an array of hits
				}
			}
			else {
				$data = yaz_record($id[$host],$hit,"xml");
				xml_parse_into_struct($xml_parser, $data, $hostRecords);
				list($num_records, $rslt) = get_marc_fields_from_xml($hostRecords);
			}
		}
		return $rslt;
	}

	##-----------
	function get_marc_fields_from_xml($xml) {
	  $marc = array();
	  $respVersion = '';
	  $recordposition = 0;
	  $subcount = 0;
	  $total_hits = 0;
	  $diagMsg = '';
	  $wantMsg = false;
	  
	  foreach($xml AS $record)   {
	    switch($record['tag'])     {
	    case 'ZS:VERSION':
	      $respVersion = $record['value'];
	      break;
	    case 'ZS:NUMBEROFRECORDS':
	      // Represents total number of records that matched, not actual returned.
	      $total_hits = $record['value'];
	      break;
	    case 'ZS:DIAGNOSTICS':
	      if ($record['type'] == 'open') {
	      	$attributes = $record['attributes'];
	      	$wantMsg = true;
				}
	      break;
	    case 'MESSAGE':
	      if ($wantMsg)  {
					$marc[$recordposition]['diagMsg'] = $record['value'];
	      	$wantMsg = false;
				}
	      break;
	     case 'CONTROLFIELD':
	      $attributes = $record['attributes'];
	      $marc[$recordposition][$attributes['TAG']] = trim($record['value']);
	      break;
	    case 'DATAFIELD':
	      if(isset($record['attributes']))       {
	        $attributes = $record['attributes'];
	        $datafield = $attributes['TAG'];
	      }
	      break;
	    case 'SUBFIELD':
	      $attributes = $record['attributes'];
	      $code = $attributes['CODE'];
	      $value = $record['value'];
	      $indicie = $datafield . $code;
	      $extratrim = '';
	      switch($indicie) {
	      case MARC_ISBN:
	        if (substr($value,0,3) == '978')
	        	$value = substr($value, 0, 13);
					else
	        	$value = substr($value, 0, 10);
	        break;
	      case MARC_TITLE:
	      case MARC_PUBLICATION_PLACE:
	        $extratrim = ':/';
	        break;
	      case MARC_SUBTITLE:
	        $extratrim = '/';
	        break;
	      case MARC_PUBLISHER:
	        $extratrim = ',';
	        break;
	      case MARC_PAGES:
	        $value = (int)($value);
	        break;
	      }
	      if($indicie != MARC_SUBJECT)       {
	        if(isset($marc[$recordposition][$indicie]) && !empty($marc[$recordposition][$indicie])) {
	          $marc[$recordposition][$indicie] .= ', ' . trim($value, ' '.$extratrim);
	        } else {
	          $marc[$recordposition][$indicie] = trim($value, ' '.$extratrim);
	        }
	      } else {
	        if($subcount == 0) {
	          $marc[$recordposition][$indicie] = trim($value, ' '. $extratrim);
	          $subcount++;
	        } else {
	          $marc[$recordposition][$indicie.$subcount] = trim($value, ' ' . $extratrim);
	          $subcount++;
	        }
	      }
	      break;
	    case 'ZS:RECORDPOSITION':
	      $recordposition++;
	      $subcount = 0;
	      break;
	    }
	  }
	
	  /**
	   * The ZS:RECORDPOSITION tag does not occur when only one record is returned.
	   * Update recordposition to indicate 1 record.
	   */
	  if (($recordposition == 0) && ($total_hits > 0))
	    $recordposition = 1;
	
	  return array($recordposition, $marc);
	}

	##-----------
	function get_marc_fields_from_x3950($ar, $postit, $hit, $host) {
		global $my_callNmbrType;
    $nl = "";
    reset($ar);
		$nHost = "host$host";
		$nHit = "hit$hit";
		$rslt = array();

    while(list($key,list($tagpath,$data))=each($ar)) {
      if (preg_match("/^\(3,([^)]*)\)\(3,([^)]*)\)$/",$tagpath,$res)) {
				if (!empty($theTag)) {
					$marcFlds["$theTag"] = $subFlds;	// store previous data
				}
        $theTag = "$res[1]";
        $subFlds = array(); //reset($subFlds);
    	}
    	elseif (preg_match("/^\(3,([^)]*)\)\(3,([^)]*)\)\(3,([^)]*)\)$/",$tagpath,$res)) {
        $subFlds["$res[3]"] = "$data";

        $data = trim(htmlspecialchars($data));
				$fldId = ($theTag . $res[3]);

				// MAB (13Apr2008 - This is a hack to handle the author sometimes returned as 100a
				// and sometimes 700a and sometimes both
				// this assumes 100a is seen before 700a if both returned
				if ($fldId == '100a') 
					$fld100a = true;
				elseif ($fldId == '700a' && !$fld100a) 
					$fldId = '100a';

				if ($postit == false) {
					//echo "special for multi hit choice selection";
					$rsltStr = display_record($fldId, $data, $hit);  //<<<<<<<<<<<<<<<<<
				}
				elseif ($postit == true) {
					//echo "normal processing";
//					if (isset($_POST[$nHost][$nHit][$fldId])) $_POST[$nHost][$nHit][$fldId] .= '; ';
					if (isset($rslt[$fldId])) $rslt[$fldId] .= '; ';
					switch ($theTag) {
					case '538':  ##### Systems Details Note (R)
						if	($res[3] == 'a')
//							$_POST[$nHost][$nHit]['520a'] .= $data; ## Note (NR)
							$rslt['520a'] .= $data; ## Note (NR)
						break;
        	case '650':  ##### Subject Added Entry - Topical Term (R)
        		if     ($res[3] == 'a') {
							if (isset($subjectCnt)) $subjectCnt++; else $subjectCnt = '';
//	      	  		$_POST[$nHost][$nHit]["650a$subjectCnt"] .= $data; // topical term (NR)
	      	  		$rslt["650a$subjectCnt"] .= $data; // topical term (NR)
						}
						else
							#### following line is a patch by Hans van der Weij
//							if (!is_numeric($res[3])) {$_POST[$nHost][$nHit]["650a$subjectCnt"] .= ', ' . $data;}
							if (!is_numeric($res[3])) {$rslt["650a$subjectCnt"] .= ', ' . $data;}
									//        			$_POST["650a$subjectCnt"] .= ', ' . $data; // details (NR)
        		break;
					default:		##### everything else
//						$_POST[$nHost][$nHit][$fldId] .= $data;
						$rslt[$fldId] .= $data;
						break;
					}
				}
      }
    }
    return $rslt;
	}

?>
