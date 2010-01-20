<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
/*
	##-----------
	function showMeSimple ($prefix,$stuff) {
		echo "<fieldset>$prefix:<br />";
		print($stuff);
		echo "</fieldset>";
	}

	##-----------
	function showMeComplex ($prefix,$stuff) {
		echo "<fieldset>$prefix:<br />";
		print_r($stuff);
		echo "</fieldset>";
	}
*/
	##-----------
	function verifyLCCN () {
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
	function lkup_err_Handler($err, $err_str, $fn, $ln, $sym) {
		$badOnes = (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
		if (error_reporting() == $badOnes) {
			echo "<p>The error '<b>$err_str</b>' occurred at line $ln of file <i>$fn</i>.</p>";
    	header("Location: ../catalog/biblio_new_form.php");
			error_reporting($err_level);		// restore original value
			set_error_handler($err_fnctn);	// restore original handler
			exit();
		}
	}

	##-----------
	function doOneHost($host, $hits, $id) {
/*
		global $gotISBN;
		global $gotLCCN;
		global $gotLoc;
		global $gotPub;
		global $gotDate;
		global $lookLoc;
		global $postVars;
		$rslt = array();
*/
		for ($hit = 1; $hit<=$hits[$host]; $hit++) {
			//print "handling record #$hit of $hits[$host] for host #$host <br />";
			//print_r($postVars[hosts][$host]);echo "<br />";
			$ar = yaz_record($id[$host],$hit,"array");
			//if ($host > 0) {
			//	echo "ar:<br />";print_r($ar); echo "<br />";
			//}
			if (! empty($ar)) {
				$formName = "hitForm".$host."_".$hit;
				$rec = yaz_record($id[$host],$hit,'string');

				## make sense of the received MARC records
				$rslt[$hit] = extract_marc_fields($ar, true, $hit, $host); // an array of hits
				
				//## deal with local opts like call numbers, etc.
				//$rslt[$hit] = postProcess($rslt[$hit]);
			}
/*
			## clear flags for a repeated use
			unset ($gotISBN[$hit]);
			unset ($gotLCCN[$hit]);
			unset ($gotPub[$hit]);
			unset ($gotLoc[$hit]);
			unset ($gotDate[$hit]);
*/		}
		return $rslt;
	}
	##-----------

?>
