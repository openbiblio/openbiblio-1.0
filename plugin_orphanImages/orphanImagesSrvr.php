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
	
   function getImageList ($dir) {
        $imgs = scandir($dir);
        foreach ($imgs as $k => $img) {
            if (in_array($img, array(".", ".."))) continue;
            $list[] = $img;
        }
		sort($list,SORT_LOCALE_STRING);
        return $list;
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
            // scan all files likely to use an image, and save name of those that do
            $found = array();
            $root = '..';
			$dirs = getDirList($root);
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
                                    if($detail) echo $fn."<br />";
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

            // first make a list of all known image files
			$imgList = getImageList('../images');

            $nmbr = count($imgList) - count($found);
			echo "<p>The following ".$nmbr." image files are NOT.</p>";
            foreach ($imgList as $imgFile) {
				$file = trim($imgFile);
                if  (!in_array($file, $used)) echo "$file <br />";
            }

			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

?>
