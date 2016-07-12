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

	switch ($_POST['mode']) {
		case 'ck4Orfans':
            $root = '..';
			$dirs = getDirList($root);
            //echo "<br />OB directory List: <br />";
            if ($verbose) {
                echo "<br />OB directory List: <br />";
                foreach ($dirs as $dir) {
                    echo "$dir <br />";
                }
            }

            //echo "looking at root directory<br />";
			$rootFiles = getFileList($root);
            if ($detail) {
                echo "<br /><br />---- OpenBiblio ----<br />";
                //foreach ($rootFiles as $file) {
                //    echo "$file <br />";
                //}
            }
            findFileRef($rootFiles);

            //echo "looking at sub-directories<br />";
			foreach ($dirs as $dir) {
				if($detail)echo "<br /><br />---- $dir ----<br />";
                // first for OB's root directory
				$dirFiles = getFileList('../'.$dir);
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
		        //$file = str_replace('../',"",$file);
		        //$file = str_replace_first('../',"",$file);
		        //$file = str_replace('..',"",$file);
		        //$file = str_replace('./',"",$file);
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
