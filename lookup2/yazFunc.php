<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	##-----------
	function extract_marc_fields($ar, $postit, $hit, $host) {
		global $my_callNmbrType;
    $nl = "";
    reset($ar);
		$nHost = "host$host";
		$nHit = "hit$hit";
		$rslt = array();
		
    while(list($key,list($tagpath,$data))=each($ar)) {
      if (ereg("^\(3,([^)]*)\)\(3,([^)]*)\)$",$tagpath,$res)) {
				if (!empty($theTag)) {
					$marcFlds["$theTag"] = $subFlds;	// store previous data
				}
        $theTag = "$res[1]";
        $subFlds = array(); //reset($subFlds);
    	}
    	elseif (ereg("^\(3,([^)]*)\)\(3,([^)]*)\)\(3,([^)]*)\)$",$tagpath,$res)) {
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
