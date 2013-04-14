<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

	//require_once(REL(__FILE__, "../functions/marcFuncs.php"));
	//require_once(REL(__FILE__, "../model/MediaTypes.php"));
	//require_once(REL(__FILE__, "../model/Collections.php"));
	//require_once(REL(__FILE__, "../model/Biblios.php"));
	//require_once(REL(__FILE__, "../model/Cart.php"));
	require_once(REL(__FILE__, "../model/MarcDBs.php"));
	//require_once(REL(__FILE__, "../classes/Marc.php"));
	//require_once(REL(__FILE__, "../classes/SrchDb.php"));

	# Big uploads take a while
	set_time_limit(120);

	//echo "at server entry==>";print_r($_POST);echo "<br />";
	
	## ---------------------------------------------------------------------- ##

## main body of code
switch ($_REQUEST[mode]){
  #-.-.-.-.-.-.-.-.-.-.-.-.-
  case 'getMarcDesc':
	  $tag = explode('$', $_GET['code']);
		$ptr = new MarcSubfields;
		$params = array('tag' =>$tag[0], 'subfield_cd' =>$tag[1] );
	  $vals = array();
		$rslt = $ptr->getMatches($params, 'subfield_cd');
		while ($row = $rslt->next()) {
		  $vals[] = $row;
		}
		$val = $vals[0]['description'];
	  echo $val;
  	break;
  	
  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'fetchCsvFile': 
		//echo "at fetchCsvFile==>";print_r($_FILES);echo "<br />";
		$fn = $_FILES['imptSrce']['tmp_name'];
		//echo "importing file: '".$_FILES['imptSrce']['name']."'<br />";
		if (is_uploaded_file($fn)) {
			$recordterminator="\n";
			$rows =  explode($recordterminator, file_get_contents($fn));
			//echo "array of lines==>";print_r($rows);echo "<br />";
			echo json_encode($rows);
		} else {
			echo	T("error - file did not load successfully!!")."<br />";
		}
  	break;
  	
  #-.-.-.-.-.-.-.-.-.-.-.-.-
	default:
	  echo "invalid mode: $_POST[mode] <br />";
		break;
}

?>
