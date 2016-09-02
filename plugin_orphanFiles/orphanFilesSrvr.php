<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

    require_once("../shared/common.php");
    require_once("../functions/supportFuncs.php");

	$verbose = ($_POST['verb'] == 'No')?false:true;
	$detail = ($_POST['detl'] == 'No')?false:true;
	if ($verbose == true) $detail = true;
	
    //global variables
    $allFiles = array();
    $found = array();
    $localeList = getLocaleList();
    global $found, $allfiles, $localeList;

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
            findFileRef($rootFiles);

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
                findFileRef($dirFiles);
			}

            //echo "reporting results";
			sort($allFiles);
			ksort($found);
			//echo count($allFiles) . " ". T("files exist"). "<br />";
			//echo count($found) . " ". T("files are referenced")."<br />";
            if ($verbose) {
                echo "<br />Referenced files: <br />";
                foreach ($found as $k=>$v) {
			     	echo "referenced ===> $k <br />";
                }
            }

            $unused = count($allFiles) - count($found);
			echo "<p>The following files appear to not be in use.</p>";
            foreach($allFiles as $file) {
				$file = trim($file);
		        $file = str_replace('../',"",$file);
		        //$file = str_replace_first('../',"",$file);
		        $file = str_replace('..',"",$file);
		        $file = str_replace('./',"",$file);
                $fileName = '.../OB/'.trim($file, '/');
				if ((array_key_exists($fileName, $found)) && ($found[$fileName] == 'OK')) {
					continue;
				} else {
					echo "unused===> $fileName <br />";
				}
			}
			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

?>
