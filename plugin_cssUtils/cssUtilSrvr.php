<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  	require_once("../shared/common.php");
	//print_r($_POST);echo "<br />";

	$cssFile = "../shared/style.css";

/*
	function array_flat($array) {
		$tmp = array();
	  foreach($array as $a) {
	    if(is_array($a)) {
	      $tmp = array_merge($tmp, array_flat($a));
	    } else {
      	$tmp[] = $a;
    	}
  	}
  	return $tmp;
	}
*/
/*
	function getFileList($dir) {
	  	$files = array();
	  	if ($handle = opendir($dir)) {
	    	while (false !== ($file = readdir($handle))) {
	      	if ($file != "." && $file != ".." &&
							$file != 'sql' &&
							$file != 'legacy' &&
							$file != 'defs' &&
							$file != 'themes') {
	          if(is_dir($dir.'/'.$file)) {
	          	$dir2 = $dir.'/'.$file;
	          	$files[] = getFileList($dir2);
	          } else {
	          	$aFile = $dir.'/'.$file;
	            $files[] = $aFile;
	          }
	        }
	    	}
	    	closedir($handle);
	  	}
	  	return array_flat($files);
	}
*/
	function getClassList($theFile) {
		//Strip out everything between { and }
		$pattern_one = '/(?<=\{)(.*?)(?=\})/s';
		//Match class selectors (and pseudos)
		$pattern_two = '/[\.][\w]([:\w]+?)+/';
		//Match any and all selectors (and pseudos)
		$pattern_twoA = '/[\.|#][\w]([:\w]+?)+/';

		$lines = file($theFile);
		$classes = array();
		$grp = [];
		foreach ($lines as $lineNum => $line) {
			//echo "line ==>> $line<br />";
			// skip empty lines and comments
			$line = trim($line);
			if ($line == '') continue;
			if (substr($line,0,2) == '/*') continue;
			if (substr($line,0,2) == '*/') continue;
			if (substr($line,0,1) == '*') continue;

			//remove rules between '{}'
			$rslt = preg_replace($pattern_one, '', $line);
			//echo "rslt ==>> $rslt<br />";

			// extract classes and save to array
			$matches = preg_match($pattern_two, $rslt,$grp);
			//echo "grp ==>> $grp[0]<br />";
			if (isset($grp[0])) {
				//$classes[($lineNum)+1] = $grp[0];
				//echo "line $lineNum ==>> $grp[0]<br />";
				$className = substr($grp[0], 1);
				$classes[$className] = $lineNum+1;
			}
		}
        ksort($classes);
		return $classes;
	}

	function moduleList () {
		$handl = opendir("..");
		while (false !== ($file = readdir($handl))) {
		  if ($file != '.' && $file != '..') {
				if ((is_dir('../'.$file)) && 
						(substr($file,0,1) != '.') && 
						($file != 'images') && 
						($file != 'font') && 
						($file != 'photos')
					 ) {
					$mods[] = $file;
				}
			}
		}
		closedir($handl);
		sort($mods,SORT_LOCALE_STRING);
		return $mods;
	};

	switch ($_POST['mode']){
	  	case 'ck4CssUnused':
			$clist = [];
			## collct all 'class' selectors in CSS file
			$cList = getClassList($cssFile);
			echo "<br />there are ".count($cList)." class selectors in the CSS file: .".$cssFile."<br />";
//			$classes = getClassList($cssFile);
//			echo "<br />there are ".count($classes)." class/id selectors in the CSS file: .".$cssFile."<br />";
//			## create array of classes with empty values;
//			foreach ($classes as $lineNmbr=>$className) {
//				$cList[$className] = $lineNmbr;
//			}

	  		$modules = moduleList();
	  		$found = array();
			// make a list of all functional folders
			foreach ($modules as $module) {
				$files = getFileList('../'.$module);
				// make a list of all files in those folders
				foreach ($files as $file) {
					## capture file suffixfor later use
					$ext = substr($file,-6);
			  		$lines = file("../$module/$file");
					// scan each file for class references
			  		foreach ($lines as $lineNum => $line) {
						if (($ext != 'Js.php') || ($ext != 'Js6.php')) {
							preg_match_all('/(class=")(\w(?<!\d)[\w\'-]*)(["])/',$line,$out, PREG_PATTERN_ORDER);
						} else {
							preg_match_all('/(\$\([\"\']\.)(.*?)([\"\'])/',$line,$out, PREG_PATTERN_ORDER);
//print_r($out);echo"<br />";
						}
						// test class name useage for all entries in this line
						foreach ($out[2] as $key) {
							// ignore special cases
							if ((substr($key,-3) == 'Btn') || (substr($key,-4) == 'Btns')) continue;
							if ((substr($key,0,3) == "'.$") || (substr($key,0,3) == "'.H")) continue;

							if (isset($found[$key])) {
								// duplicate in use
								continue;
							} elseif (isset($cList[$key])) {
								// new entry is in css file
								$found[$key] = array('note'=>'Used');
							} else {
//echo "$key<br />";
								// not in css file
								$found[$key] = array('note'=>'Missing','file'=>$file,'lineNum'=>($lineNum+1));
							}
						}
				  }
				}
			}
			if (count($cList) == count($found)) {
				echo T("All css class entries are being used.");
			}

			echo "<br /><p>The following are not being used and may possibly be removed.</p>";
			//ksort($clist);
			foreach ($cList as $className=>$lineNmbr) {
				if ($found[$className]['note'] == 'used') {
					continue;
				} else {
					echo "$className on $lineNmbr<br />";
				}
			}

			echo "<br /><p>The following classes assigned to html elements are not defined in the CSS file.</p>";
			ksort($found);
			foreach ($found as $className=>$data) {
			  if ($data['note'] == 'Missing') echo "$className at line ".$data['lineNum']." of file ".$data['file']."<br />";
			}
//print_r($found);echo "<br />";
			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;$_POST[mode]&lt;</h4><br />";
		break;
	}


