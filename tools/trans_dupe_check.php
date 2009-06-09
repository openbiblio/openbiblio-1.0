#!/usr/bin/php
<?php
$arrKeys = array();
$errCount = 0;

if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {


?>

	Usage: ./trans_dupe_check %locale% %file%

	Where:
	%locale% = the two letter abbreviation for the locale (en)
	%file%   = the name of the file to be checked for duplicates


<?php
} else {
	echo "Checking locale: " .$argv[1]."\n";
	echo "Checking file:   " .$argv[2]."\n";
	
	$lines = file("../locale/$argv[1]/$argv[2].php");

	foreach ($lines as $line_num => $line) {
		//echo substr($line,0,1)."\n";

		if (substr($line,0,1) != "#" && substr($line,0,1)!="<" && substr($line,0,1)!="?" && substr($line,0,1)!="\n") {
			if (strpos($line,"]")>1) {
				list($key,$value)=split("]",$line);
				$key = str_replace("\$trans[\"","",$key);
				$key = str_replace("\"","",$key);
				if (in_array($key, $arrKeys, true)) {
					echo "Line #{$line_num} : " . chop($line) . " is a duplicate\n";
					$errCount++;
				} else {				
					$arrKeys[]=$key;
				}
			}
		}
	}
	if ($errCount < 1) {
		Echo "No duplicates found";
	} else { 
		echo "Found $errCount duplicate(s)";
	};
	echo "\n\n";
}
