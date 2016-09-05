<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/******************************************************************************
 * file-related support functions
 *
 *@author Fred LaPlante - July 2016
 */

    $localeList = array();

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

    function fileToArray($dir) {
       $result = array();
       $cdir = scandir($dir);
       foreach ($cdir as $key => $value) {
          if (!in_array($value, array(".", ".."))) {
             if (!is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[] = $value;
             }
          }
       }
       return $result;
    }

    function getLocaleList () {
        global $detail;
        $list = array();
        $locs = scandir("../locale");
        echo "---- locales ----<br />";
        foreach ($locs as $k => $loc) {
            if ((in_array($loc, array(".", "..")))) continue;
            //if (!is_dir($loc)) continue;
            if ($detail) echo "found locale: $loc<br />";
            $list[] = $loc;
        }
        return $list;
    }

	function getDirList ($root) {
        $dirs = array();
        $cdir = scandir($root);
        foreach ($cdir as $key => $value) {
            if ((!in_array($value, array(".", ".."))))  {
				if (is_dir($root . DIRECTORY_SEPARATOR . $value) &&
                    ($value != '.hg') &&
                    ($value != '.hgignore') &&
                    ($value != '.htags') &&
					//($value != 'images') &&
					($value != 'font') &&
					($value != 'docs') &&
					//($value != 'locale') &&
					//($value != 'themes') &&
					($value != 'working') &&
					($value != 'photos') &&
					($value != 'vendor')     ) {
					$dirs[] = $value;
                }
			}
		}
		sort($dirs, SORT_LOCALE_STRING);
		return $dirs;
	};

	function getFileList($dir, $getSubs=false) {
        global $localeList, $Locale, $detail;

  	    $files = array();
        if (!isset($dir)) return;
        try {
            $cdir = scandir($dir);
        } catch (Exception $e) {
            if ($detail) echo $e->getMessage()."<br />";
            // LJ, not sure, this was continue which is not correct.
            return array_flat($files);
        }
    	foreach ($cdir as $key => $file) {
			$info = pathInfo($file);
      	    if (!in_array($file, array(".", ".."))) {
                if ((!is_dir($dir . DIRECTORY_SEPARATOR . $file)) &&
					 ## folowing sub-directories do not contain project code
					($file != '.hg') &&
					($file != 'sql') &&
					//($file != 'legacy') &&
					//($file != 'jquery') &&
					//($file != '.Thumbnails') &&
					## folowing names are file extensions
					($info['extension'] != 'tran') &&
					//($info['extension'] != 'nav') &&
					//($info['extension'] != 'ico') &&
					## folowing are special filenames
					($info['basename'] != '.hgignore') &&
					($info['basename'] != '.hgtags') &&
					($info['basename'] != '.htaccess') &&
					($info['basename'] != 'custom_head.php') &&
					($info['basename'] != 'COPYRIGHT.html') &&
					($info['basename'] != 'install_instructions.html') &&
					($info['basename'] != 'dbParams.php') &&
					($info['basename'] != 'dbParams_deploy.php') &&
					($info['basename'] != 'Development_Guidelines.txt') &&
					($info['basename'] != 'custom_head.php') &&
					($info['basename'] != 'GPL.txt') &&
					($info['basename'] != 'README.md') &&
					($info['basename'] != 'cache.appcache') &&
					($info['basename'] != 'TODOlist.txt')
				) {
          	        $aFile = $dir.'/'.$file;
                    $files[] = $aFile;
                } else if (!is_file($file)) {
                    if (!(in_array($info['basename'], $localeList)) || ($info['basename'] != $Locale)) continue;
                    if ($info['basename'] == '.Thumbnails') continue; //echo "got a Thumbnail ref</br />";
                    if ($info['basename'] == 'jquery') continue; //echo "got a jQuery ref</br />";
                    if ($info['basename'] == 'ajaxFileUpload') continue;
                    if ($getSubs) {
                        global $verbose;
          	            $dir2 = $dir.'/'.$file;
                        if ($verbose) echo "working sub-dir: $dir2<br />";
                        echo "working sub-dir: $dir2<br />";
          	            $files[] = getFileList($dir2);
                    }
                }
    	   }
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
		preg_match('/(href=\")(.*?)(.php|.css|.ico|.png|.gif|.jpg)/', $in, $out);
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
/*
    // originally from orphanFiles plugin
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
*/
    // originally from orphanImages plugin
	function getFnFmPhpSrc($text, $dir) {
		if (stripos($text, '=') <= 6) return '';
		$in = str_replace("'", "\"", $text);
		$in = str_replace('__, "', '__,"', $in);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(src=\")(.*?)(\.)(png|gif|jpg|js)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3].$out[4];
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
	function getFnFmPhpTxt($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(\")(.*?)(\.)(png|gif|jpg)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3].$out[4];
		if (substr($rslt, 0,3) != '../') $rslt = '../'.$dir.'/'.$rslt;
		return trim($rslt);
	}
	function getFnFmCss($text, $dir) {
		$in = str_replace("'", "\"", $text);
		if($verbose) {echo "in===>";echo $in;echo"<br />";}
		preg_match('/(\")(.*?)(\.)(png|gif|jpg)/', $in, $out);
		if($verbose) {echo "out===>";print_r($out);echo"<br />";}
		$rslt = $out[2].$out[3].$out[4];
		if (substr($rslt, 0,3) != '../') $rslt = '../'.$dir.'/'.$rslt;
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

    function str_replace_first($search, $replace, $subject) {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    function findFileRef($fileArray) {
        // select each file in the current directory and add to array
        global $found, $allFiles, $verbose, $detail;

		foreach ($fileArray as $fileName) {
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
					//(substr($line,0,1) == "<!--")  || // needed for html conditionals
					//(substr($line,0,2) == "<?")  ||  // needed for php in-line includes, etc.
					(substr($line,0,2) == "?>")  ||
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
			    } elseif (stripos($line, "avIcon ", 0) >= 1) {
                    $fn = getFnFmPhpTxt($line, $dir);
			    } elseif (stripos($line, ":url = ", 0) >= 1) {
                    $fn = getFnFmCss($line, $dir);
				} elseif (stripos($line, "av::node", 0) >= 1) {
					$fn = getFilenameFmMenu($line, $dir);
				} elseif (stripos($line, "file_get_contents", 0) >= 1) {
					$fn = getFnFmPhpReq($line, $dir);
				} elseif (preg_match('(\.gif|\.png|\.jpg)', $line)) {
					if ($verbose) echo "found a undetected image ref on: $line of $fileName <br />";
				} else {
                  continue;
				}

                // normalize filename
        		$fn = str_replace('../',"",$fn);
        		$fn = str_replace('..',"",$fn);
        		$fn = str_replace_first('./',"",$fn);
                $fn = trim($fn, '/');
				$fn = trim($fn);

                // add file reference to array if not a duplicate
                if ($fn != '') {
                    $fileName = ".../OB/$fn";
					if (array_key_exists($fileName, $found)) {
					//if (in_array($fileName, $found)) {
						if($detail)echo "--dupe-- fn===> $fileName<br />";
						continue;
					} else if ($fileName == '/') {
						if($detail)echo "--skip-- fn===> $fileName<br />";
                        continue;
                    } else {
						$found[$fileName] = 'OK';
						if($detail)echo "--added-- fn===> $fileName<br />";
					}
                }
			}
		}
    }

    /* This function will create a concat string of all file sizes and timestamps,
       and subsequently uses hash() to create a unique represenation which is not too large
    */
    function getOBVersionHash() {
        $ttlStr = "";
        $total = 0;
        $obRoot = "..";
        $obDirs = getDirList($obRoot);
        foreach ($obDirs as $dir) {
            $str = ""; $subttl = 0;
            $dir = '../'.$dir;
            $obFiles = getFileList($dir, true);
            foreach ($obFiles as $file) {   // for all files in current directory
                $subttl += filesize($file); // accumulate file sizes
                $str .= filesize($file) . ":" . filectime($file);   // concatenate sizes and mod dates
            }
            $ttlStr .= "- " . $str;
            $total += $subttl;
        }
        return array(hash("md5", $ttlStr), $total);
    }

