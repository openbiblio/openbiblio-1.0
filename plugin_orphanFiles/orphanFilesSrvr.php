<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_REQUEST);echo "<br />";
/*
	if ($_REQUEST['verb'] == 'No') 
		$verbose = false; 
	else 
		$verbose = true;
	if ($_REQUEST['detl'] == 'No') 
		$detail = false; 
	else 
		$detail = true;
*/
	$verbose = ($_REQUEST['verb'] == 'No')?false:true;
	$detail = ($_REQUEST['detl'] == 'No')?false:true;
	if ($verbose == true) $detail = true;
	//var_dump($verbose); var_dump($detail); echo "<br />";
	
	function getDirList () {
		$dirs = [];
		$handl = opendir("..");
		while (false !== ($file = readdir($handl))) {
		  if ($file != '.' && $file != '..') {
				if ((is_dir('../'.$file)) && 
						(substr($file,0,1) != '.') && 
						## folowing names are main directories
						($file != 'images') &&
						($file != 'font') &&
						($file != 'layouts') &&
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
		sort($dirs,SORT_LOCALE_STRING);
		return $dirs;
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
				$info = pathInfo($file);
      	if (($file != ".") && ($file != "..") &&
						 ## folowing names are sub-directories
						($file != 'sql') &&
						($file != 'legacy') &&
						($file != 'jquery') &&
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
	
	$files = [];
	
	switch ($_REQUEST['mode']) {
		case 'ck4Orfans':
			$found = [];
			$dirs = getDirList();
			foreach ($dirs as $dir) {
				if($detail)echo "<br /><br />---- $dir ----<br />";			
				$dirFiles = getFileList('../'.$dir);
				//echo "dir files: ";print_r($dirFiles);echo "<br />";				
				foreach ($dirFiles as $fileName) {
//					$fileName = '../'.$fileName;
					if (is_dir($fileName)) {
						if($detail) echo "--dir-- fn===> $fileName skipped <br />";							
						break;
					} 
					if($detail)echo '<p class="bold">'.$fileName."</p>";			
					$allFiles[] = $fileName;
				  $lines = file($fileName, FILE_SKIP_EMPTY_LINES);
					foreach ($lines as $line_num => $line) {
						$line = trim($line);
						if ($line != '') {
							if ( (substr($line,0,7) == "<script") || 
									 (substr($line,0,5) == "<link") || 
									 (substr($line,0,5) == "<form") || 
									 (substr($line,0,3) == "<a ") ||
									 (
										substr($line,0,2) != "/*" &&
										substr($line,0,2) != "*/" &&
										substr($line,0,2) != "//" &&
										substr($line,0,1) != "#" &&
										//substr($line,0,1) != "<" &&
										substr($line,0,1) != "?" &&
										substr($line,0,1) != "\n" &&
										substr($line,0,1) != "*" ) ){
	
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
									$fn = '';
								}
								$fn = trim($fn);
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
				} 
			}
			echo "There are ".count($allFiles)." files in ".count($dirs)." directories.<br />";
			echo count($found)." files are referenced.<br />";
			ksort($found);
			sort($allFiles);
			//var_dump($found);		
	
//			if (count($allFiles) == count($found)) {
//				echo "All project files are in use.";
//			} elseif (count($allFiles) > count($found)) {
				echo "<p>The following files are not being used and may be removed.</p>";
				for ($i=0; $i<count($allFiles); $i++) {
					$file = trim($allFiles[$i]);	
					if ((array_key_exists($file, $found)) && ($found[$file] == 'OK')) {
						continue;
					} else {
						$info = pathinfo($file);
						if ($info['extension'] != 'nav'){
							echo "unused===> $file <br />";
						}
					}
				}
//			} else {
//				echo (count($found)-count($allFiles))." scanned-in filenames are invalid.<br />";
//				for ($i=0; $i<count($found); $i++) {
//					$file = trim($found[$i]);
//					if (array_key_exists($file, $allFiles)) {
//						continue;
//					} else {
//						echo "invalid===> $file <br />";
//					}
//				}
//			}
			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;".$_REQUEST['mode']."&lt;</h4><br />";
		break;
	}

?>
