<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_POST);echo "<br />";
	$verbose = ($_POST['verb'] == 'No')?false:true;
	$detail = ($_POST['detl'] == 'No')?false:true;
	if ($verbose == true) $detail = true;
	//var_dump($verbose); var_dump($detail); echo "<br />";
	
	function getDirList () {
		$dirs = array();
		$handl = opendir("..");
		while (false !== ($file = readdir($handl))) {
		  if ($file != '.' && $file != '..') {
				if ((is_dir('../'.$file)) && 
						(substr($file,0,1) != '.') && 
						## following directories do not contain project code
						($file != 'images') &&
						($file != 'font') &&
						($file != 'docs') &&
						($file != 'locale') &&
						($file != 'themes') &&
						($file != 'working') &&
						($file != 'photos')
					 ) {
					$dirs[] = $file;
				}
			}
		}
		closedir($handl);
		sort($dirs, SORT_LOCALE_STRING);
		return $dirs;
	};

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

	function getFileList($dir) {
  	    $files = array();
  	    if ($handle = opendir($dir)) {
    	   while (false !== ($file = readdir($handle))) {
				$info = pathInfo($file);
      	        if (($file != ".") && ($file != "..") &&
					 ## folowing sub-directories do not contain project code
					($file != 'sql') &&
					($file != 'legacy') &&
					($file != 'jquery') &&
					($file != '.Thumbnails') &&
					## folowing names are file extensions
					($info['extension'] != 'tran') &&
					## folowing are special filenames
					($info['basename'] != 'custom_head.php')
				) {
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

	function getFnFmPhpReq($text, $dir) {
		if (stripos($text, 'required') >= 1) return '';
		$in = str_replace("'", "\"", $text);
		$in = str_replace('__, "', '__,"', $in);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/((\.\.)?\/|_,\")(.*?)(\?|\"|\.js|\.css)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[3];
		$rslt = str_replace("(","",$rslt);
		$rslt = str_replace(")","",$rslt);
		$rslt = str_replace(";","",$rslt);
		$rslt = str_replace('"',"",$rslt);
		if ($out[1] == '../') $rslt = '../'.$rslt;
		if (!preg_match('/((\.|\.\.)?\/)/',$rslt)) $rslt = '../'.$dir.'/'.$rslt;
		return trim($rslt);
	}
	function getFnFmPhpHref($text, $dir) {
        if (stripos($text, '#') >= 1) return '';
        $in = str_replace("'", "\"", $text);
		$in = str_replace('__, "', '__,"', $in);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(href=\")(.*?)(.php|.css)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	function getFnFmPhpActn($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(action=\")(.*?)(.php)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	function getFnFmPhpArray($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(\"action\"=>\")(.*?)(.php)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	function getFnFmPhpLinkUrl($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(\.\.)?\/(.*?)(.php)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[1].$out[2].$out[3];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	function getFnFmPhpHdr($text, $dir) {
		if (stripos($text, 'age::header') >= 1) return '';
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/((\.\.)?\/)(.*?)(.php)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[1].$out[3].$out[4];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	function getFnFmPhpSrc($text, $dir) {
		if (stripos($text, 'img src') >= 1) return '';
		$in = str_replace("'", "\"", $text);
		$in = str_replace('__, "', '__,"', $in);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(src=\")(.*?)(\.js)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	function getFilenameFmJS($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(\")(.*?)(\.php|\.js)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2];
		if (substr($rslt, 0,3) != '../') $rslt = '../'.$dir.'/'.$rslt;
		if (substr($rslt, -4,4) != '.php') $rslt = $rslt.$out[3];
		return trim($rslt);
	}
	function getFilenameFmMenu($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(\"\.\.\/)(.*?)(\.php)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[0];
		if ($rslt == '') return '';
		$rslt = str_replace('"','',$rslt);
		if (substr($rslt, 0,3) != '../') $rslt = '../'.$dir.'/'.$rslt;
		if (substr($rslt, -4,4) != '.php') $rslt = $rslt.'.php';
		return trim($rslt);
	}
	
	$files = array();
	
	switch ($_POST['mode']) {
		case 'ck4Orfans':
            $allfiles = array();
			$found = array();
			$dirs = getDirList();

            // scan all directories for valid files
			foreach ($dirs as $dir) {
				if($detail)echo "<br /><br />---- $dir ----<br />";			
				$dirFiles = getFileList('../'.$dir);
				//echo "dir files: ";print_r($dirFiles);echo "<br />";

                // select each file in the current directory and add to array
				foreach ($dirFiles as $fileName) {
					if (is_dir($dir.'/'.$fileName)) {
						if($detail) echo "--dir-- fn===> $fileName skipped <br />";
						continue;
					} 
					if($detail)echo '<p class="bold">'.$fileName."</p>";
					$allFiles[] = $fileName;
				    $lines = file($fileName, FILE_SKIP_EMPTY_LINES);

                    // scan every line for reference to another file
					foreach ($lines as $linNum=>$line) {
					    $line = trim($line);

                        // ignore all non-code lines
                        if (($line == '' ) ||
                            (substr($line,0,2) == "/*") ||
							(substr($line,0,2) == "*/") ||
							(substr($line,0,2) == "//") ||
							(substr($line,0,1) == "#")  ||
							(substr($line,0,1) == "<!--")  ||
							//(substr($line,0,1) == "<") ||
							(substr($line,0,1) == "?")  ||
							(substr($line,0,1) == "\n") ||
							(substr($line,0,1) == "*")
                           ){
                            continue;
                        }

                        // for any of following, extract file reference
                        $fn = '';
						if (stripos($line, 'equire', 0) >= 1) {
							$fn = getFnFmPhpReq($line, $dir);
						} elseif (stripos($line, 'nclude', 0) >= 1) {
							$fn = getFnFmPhpReq($line, $dir);
						} elseif (stripos($line, 'href=', 0) >= 1) {
							$fn = getFnFmPhpHref($line, $dir);
						} elseif (stripos($line, "action'=>", 0) >= 1) {
							$fn = getFnFmPhpArray($line, $dir);
						} elseif (stripos($line, 'action=', 0) >= 1) {
							$fn = getFnFmPhpActn($line, $dir);
						} elseif (stripos($line, 'LinkUrl', 0) >= 1) {
							$fn = getFnFmPhpLinkUrl($line, $dir);
						} elseif (stripos($line, 'eader(', 0) >= 1) {
							$fn = getFnFmPhpHdr($line, $dir);
						} elseif (stripos($line, 'src=', 0) >= 1) {
							$fn = getFnFmPhpSrc($line, $dir);
						} elseif (stripos($line, "url = ", 0) >= 1) {
							$fn = getFilenameFmJS($line, $dir);
						} elseif (stripos($line, "av::node", 0) >= 1) {
							$fn = getFilenameFmMenu($line, $dir);
						} else {
                          continue;
						}

                        // normalize filename
						$fn = trim($fn);
                		$fn = str_replace('../',"",$fn);
                		$fn = str_replace('..',"",$fn);

                        // add file reference to array if not a duplicate
                        if ($fn != '') {
    						if (array_key_exists($fn, $found)) {
    							if($detail)echo "--dupe-- fn===> $fn<br />";
    							continue;
    						} else {
    							if($detail)echo "--added-- fn===> $fn<br />";
    							$found[$fn] = 'OK';
    						}
                        }
					}
				} 
			}
			sort($allFiles);
			ksort($found);
			echo count($allFiles) . " ". T("files exist"). "<br />";
			echo count($found) . " ". T("files are referenced")."<br />";
            if ($verbose) {
                foreach ($found as $k=>$v) {
			     	echo "referenced ===> $k <br />";
                }
            }

            $unused = count($allFiles) - count($found);
			echo "<p>The following $unused files appear to not be inuse.</p>";
            foreach($allFiles as $file) {
				$file = trim($file);
		        $file = str_replace('../',"",$file);
		        $file = str_replace('..',"",$file);
				if ((array_key_exists($file, $found)) && ($found[$file] == 'OK')) {
					continue;
				} else {
						echo "unused===> $file <br />";
				}
			}
			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

?>
