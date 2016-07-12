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
        // make a list of all folders with files that might reference another file
		$dirs = array();
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
                ## folowing names are sub-directories
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

	
	function getFnFmPhpSrc($text, $dir) {
		if (stripos($text, '=') <= 6) return '';
		$in = str_replace("'", "\"", $text);
		$in = str_replace('__, "', '__,"', $in);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(src=\")(.*?)(\.)(png|gif)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3].$out[4];
		$rslt = str_replace('"',"",$rslt);
		return trim($rslt);
	}
	
	$files = array();
	
	switch ($_POST['mode']) {
		case 'ck4Orfans':
			$found = array();
			$dirs = getDirList();
			foreach ($dirs as $dir) {
                // get a list of folders whose files are to be searched for image references
				if($detail)echo "<br /><br />---- $dir ----<br />";			
				$dirFiles = getFileList('../'.$dir);
				//echo "dir files: ";print_r($dirFiles);echo "<br />";				
				foreach ($dirFiles as $fileName) {
                    // make a list of files to be searched for image references
					if (is_dir($fileName)) {
						if($detail) echo "--dir-- fn===> $fileName skipped <br />";							
						break;
					} 
					if($detail)echo '<p class="bold">'.$fileName."</p>";			
					$allFiles[] = $fileName;
				    $lines = file($fileName, FILE_SKIP_EMPTY_LINES);
					foreach ($lines as $line_num => $line) {
                        // scan every line in selected files for an image reference
						$line = trim($line);
						if ($line != '') {
                            $txtStart = stripos($line,'<img', 0);
                            $line = substr($line, $txtStart);
							if (stripos($line,'img', 0) >= 1)  {
								if (stripos($line, 'src', 3) >= 1) {
                                    if($detail) echo "image ref found on ln# $line_num <br />";
                                    //if($verbose) echo $line."<br />";
									$fn = getFnFmPhpSrc($line, $dir);
                                    //if($detail) echo $fn."<br />";
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
            //echo "There are ".count($allFiles)." files in ".count($dirs)." directories.<br />";
			//echo count($found)." image files are referenced.<br />";
			ksort($found);
			//sort($allFiles);
            //var_dump($found);

			echo "<p>The following ".count($found)." image files are being used.</p>";
			foreach ($found as $k=>$v) {
                $used[] = $k;
				$file = trim($k);
				echo "$file <br />";
			}
            echo "<br />";
            $availImages = getFileList('../images');
            $nmbr = count($availImages) - count($found);
			echo "<p>The following ".$nmbr." image files are NOT.</p>";
			sort($availImages);
            foreach ($availImages as $imgFile) {
				$file = trim($imgFile);
                if  (!in_array($file, $used)) echo "$file <br />";
            }

			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

?>
