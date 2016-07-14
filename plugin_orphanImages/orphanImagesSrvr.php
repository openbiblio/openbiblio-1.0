<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

    require_once("../shared/common.php");
    require_once("../plugins/supportFuncs.php");

	//print_r($_POST);echo "<br />";
	$verbose = ($_POST['verb'] == 'No')?false:true;
	$detail = ($_POST['detl'] == 'No')?false:true;
	if ($verbose == true) $detail = true;
	//var_dump($verbose); var_dump($detail); echo "<br />";

    //global variables
    $allFiles = array();
    $found = array();


   function getImageList ($dir) {
        $imgs = scandir($dir);
        foreach ($imgs as $k => $img) {
            if (in_array($img, array(".", ".."))) continue;
            $list[] = $img;
        }
		sort($list, SORT_LOCALE_STRING);
        return $list;
    }

    function getImageFmDB () {
		require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$db = new MediaTypes;
		$rslt = $db->getIcons();
        foreach ($rslt as $row) {
		  $list[] = $row['image_file'];
		}
		sort($list, SORT_LOCALE_STRING);
        return $list;
    }

    function findImageRef($fileArray) {
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

            // scan every line for reference an image
        	foreach ($lines as $line_num => $line) {
                // scan every line in selected files for an image reference
        		$line = trim($line);
        		if ($line != '') {
        			if (stripos($line,'img', 0) >= 1) {
                        if (stripos($line, 'src', 3) >= 1) {
                            $txtStart = stripos($line,'<img', 0);
                            $line = substr($line, $txtStart);
        			        $fn = getFnFmPhpSrc($line, $dir);
                        }
				    } elseif (stripos($line, ":url", 0) >= 1) {
                        $fn = getFnFmCss($line, $dir);
    				} elseif (preg_match('(.gif|.png|.jpg)', $line)) {
    					echo "found a undetected image ref on: $line of $fileName <br />";
    				} else {
                        continue;
    				}

                    // normalize filename
            		$fn = str_replace('../',"",$fn);
            		$fn = str_replace('..',"",$fn);
            		$fn = str_replace_first('./',"",$fn);
                    $fn = trim($fn, '/');
    				$fn = trim($fn);

    				if ($fn != '') {
    					if (array_key_exists($fn, $found)) {
    						if($detail)echo "--dupe-- fn===> $fn<br />";
    						continue;
    					} else {
                            $fn = "../$fn";
    						if($detail)echo "--added-- fn===> $fn<br />";
    						$found[$fn] = 'OK';
    					}
    				}
        		}
            }
        }
    }

	$files = array();
	
	switch ($_POST['mode']) {
		case 'ck4Orfans':
            //echo "fetch root directory files<br />";
            $root = '..';
			$rootFiles = getFileList($root);
            if ($detail) {
                echo "<br /><br />---- OpenBiblio ----<br />";
                //foreach ($rootFiles as $file) {
                //    echo "$file <br />";
                //}
            }
            findImageRef($rootFiles);

            //echo "<br />OB main-directory List: <br />";
			$dirs = getDirList($root);
            if ($verbose) {
                echo "<br />OB directory List: <br />";
                foreach ($dirs as $dir) {
                    echo "$dir <br />";
                }
            }

            //echo "fetch main&sub-directory files<br />";
			foreach ($dirs as $dir) {
				if($detail)echo "<br /><br />---- $dir ----<br />";
				$dirFiles = getFileList('../'.$dir, true);
                findImageRef($dirFiles);
			}

            // add in those image filenames stored in database
            $dbList = getImageFmDB();
			if($detail)echo "<br /><br />---- database ----<br />";
            foreach ($dbList as $fn) {
                $fn = "../images/$fn";
				if (array_key_exists($fn, $found)) {
					if($detail)echo "--dupe-- fn===> $fn<br />";
					continue;
				} else {
					if($detail)echo "--added-- fn===> $fn<br />";
					$found[$fn] = 'OK';
				}
            }
            echo "<br /><br />";

            //echo "There are ".count($allFiles)." files in ".count($dirs)." directories.<br />";
			//echo count($found)." image files are referenced.<br />";
			ksort($found);
			//sort($allFiles);

			echo "<p>The following ".count($found)." image files are being used.</p>";
			foreach ($found as $k=>$v) {
                $used[] = $k;
				$file = trim($k);
				echo "$file <br />";
			}
            echo "<br />";

            // first make a list of all known image files
			$imgList = getImageList('../images');

            $nmbr = count($imgList) - count($found);
			echo "<p>The following ".$nmbr." image files are NOT.</p>";
            foreach ($imgList as $imgFile) {
				$file = trim($imgFile);
                $fileName = '../images/'.trim($file, '/');
                if  (!in_array($fileName, $used)) echo "$fileName <br />";
            }

			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

?>
