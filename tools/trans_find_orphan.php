#!/usr/bin/php
<?php
$arrKeys = array();
$errCount = 0;
$files = array();
$key = "";
$file = "";
$line = "";
$linenum = "";
$linenum = array();
$i =0;
$verbosity = 1;

if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {


?>

	Usage: ./trans_dupe_check %locale% %module%

	Where:
				%locale% = the two letter abbreviation for the locale (en)
	%module% = the name of the module directory to scan for orphans.

<?php
} else {
				echo "Checking locale: " .$argv[1]."\n";
				echo "Checking module:   " .$argv[2]."\n";
	echo "Loading $argv[1] files";
	require(REL(__FILE__, "../locale/$argv[1]/trans.php"));


	$handler = opendir("../$argv[2]");
	while ($file = readdir($handler)) {

	        if ($file != '.' && $file != '..')
			$files[] = $file;
			}
	closedir($handler);
	
	foreach ($files as $file) {
		if ($verbosity == 1) {
			echo "Checking ".$file."\n";
		};

	        $lines = file("../$argv[2]/$file");
		
	        foreach ($lines as $line_num => $line) {
			preg_match_all("|T\((.*)\)|U",$line,$out, PREG_PATTERN_ORDER);
			#print_r($out[1]);
			foreach ($out[1] as $key) {
				$key = str_replace("\"","",$key);
				$key = str_replace("\'","",$key);
				if ($verbosity==1) {
					echo ".... Checking key: ".$key."\n";
				};
				if (!array_key_exists($key, $trans)) {
					$linenum[$errCount]['key']=$key;		
					$linenum[$errCount]['filename']=$file;
					$linenum[$errCount]['linenum']=$line_num;
					$errCount++;
				}
			}	

	        }
	}
	echo "Complete!!!\n\n\n\n";
	if ($errCount > 0){
		echo "The following errors were found:\n";
		while ($i < $errCount) {
			echo $linenum[$i]['filename']." - ";
			echo $linenum[$i]['linenum']." - ";
			echo $linenum[$i]['key']. "\n";
			$i++;
		}
	} else {
		echo "All trans call in $argv[2] are translated in $argv[1]";
	}
	echo "\n\n";
}

