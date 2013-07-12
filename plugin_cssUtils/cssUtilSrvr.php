<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_REQUEST);echo "<br />";

	function getClassList() {
		$lines = file("../themes/default/style.css");
		$classes = [];
		foreach ($lines as $lineNum => $line) {
			preg_match('/((\D|\A)\.)(\w+)([\s,])/', $line,$grp);
			if (isset($grp[2])) {
				$classes[($lineNum)+1] = $grp[3];
			}
		}
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
	function array_flat($array) {
		$tmp = [];
	  foreach($array as $a) {
	    if(is_array($a)) {
	      $tmp = array_merge($tmp, array_flat($a));
	    } else {
      	$tmp[] = $a;
    	}
  	}
  	return $tmp;
	}
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

	switch ($_REQUEST['mode']){
	  case 'ck4CssUnused':
			## collct all 'class' selectors in CSS file
			$classes = getClassList();
			echo "there are ".count($classes)." class selectors in the CSS file.";
			## create array of classes with empty values;
			foreach ($classes as $lineNmbr=>$className) {
				$cList[$className] = $lineNmbr;
			}

	  	$modules = moduleList();
	  	$found = [];
			foreach ($modules as $module) {
				$files = getFileList('../'.$module);
				foreach ($files as $file) {
					## capture file suffixfor later use
					$ext = substr($file,-6);
			  	$lines = file("../$module/$file");
			  	foreach ($lines as $lineNum => $line) {
						if ($ext != 'Js.php') {
							preg_match_all('/(class=")(\w(?<!\d)[\w\'-]*)(["])/',$line,$out, PREG_PATTERN_ORDER);
						} else {
							preg_match_all('/(\$\([\"\']\.)(.*?)([\"\'])/',$line,$out, PREG_PATTERN_ORDER);
//print_r($out);echo"<br />";
						}
						foreach ($out[2] as $key) {
							if ((substr($key,-3) == 'Btn') || (substr($key,-4) == 'Btns')) continue;
							if ((substr($key,0,3) == "'.$") || (substr($key,0,3) == "'.H")) continue;
							if (isset($found[$key])) {
								continue;
							} elseif (isset($cList[$key])) {
								$found[$key] = array('note'=>'Used');
							} else {
//echo "$key<br />";
								$found[$key] = array('note'=>'Missing','file'=>$file,'lineNum'=>($lineNum+1));
							}
						}
				  }
				}
			}
			if (count($cList) == count($found)) {
				echo T("All css class entries are being used.");
			}
			echo "<p>The following are not being used and may possibly be removed.</p>";
			foreach ($cList as $className=>$lineNmbr) {
				if ($found[$className]['note'] == 'used') {
					continue;
				} else {
					echo "$className on $lineNmbr<br />";
				}
			}
			echo "<p>The following classes assigned to html elements are not defined in the CSS file.</p>";
			foreach ($found as $className=>$data) {
			  if ($data['note'] == 'Missing') echo "$className at line ".$data['lineNum']." of file ".$data['file']."<br />";
			}
//print_r($found);echo "<br />";
			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}

?>
