<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

	if ((empty($_REQUEST[mode]))&& (!empty($_REQUEST[editMode]))) {
    $_REQUEST[mode] = $_REQUEST[editMode];
	}
	
	switch ($_REQUEST[mode]){
	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMtlTypes':
			## prepare list of Material Types
			require_once(REL(__FILE__, "../model/MediaTypes.php"));
			$tptr = new MediaTypes;
		  $matls = array();
			$tSet = $tptr->getAll('code');
			while ($row = $tSet->next()) {
			  $matls[] = $row;
			}
			//print_r($matls);
			echo json_encode($matls);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMatlFlds':
			## prepare list of Material fields in use
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$fptr = new MaterialFields;
			$typeCd = array('material_cd' => $_GET['matlCd']);
		  $flds = array();
			$fSet = $fptr->getMatches($typeCd,'position');
			while ($row = $fSet->next()) {
			  $flds[] = $row;
			}
			//print_r($hosts);
			echo json_encode($flds);
			break;
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updateFldSet':
			## update material fields for a specific material type
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$ptr = new MaterialFields;
			if (empty($_POST[required])) $_POST[required] = '0';
			echo $ptr->update_el($_POST);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'd-3-L-3-tFld':
			## delete Material_fields database entry
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$ptr = new MaterialFields;
			echo $ptr->deleteOne($_GET[material_field_id]);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMarcBlocks':
			## prepare list of MARC Blocks
			require_once(REL(__FILE__, "../model/MarcDBs.php"));
			$ptr = new MarcBlocks;
		  $vals = array();
			$rslt = $ptr->getAll();
			while ($row = $rslt->next('block_nmbr')) {
			  $vals[] = $row;
			}
			//print_r($blks);
			echo json_encode($vals);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMarcTags':
			## prepare list of MARC tags for specified block
			require_once(REL(__FILE__, "../model/MarcDBs.php"));
			$ptr = new MarcTags;
			$params = array('block_nmbr' => $_GET['block_nmbr']);
		  $vals = array();
			$rslt = $ptr->getMatches($params,'tag');
			while ($row = $rslt->next()) {
			  $vals[] = $row;
			}
			echo json_encode($vals);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMarcFields':
			## prepare list of MARC subfields for specified tags
			require_once(REL(__FILE__, "../model/MarcDBs.php"));
			$ptr = new MarcSubfields;
			$params = array('tag' => $_GET['tag']);
		  $vals = array();
			$rslt = $ptr->getMatches($params,'subfield_cd');
			while ($row = $rslt->next()) {
			  $vals[] = $row;
			}
			echo json_encode($vals);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updateMarcFields':
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$fldSet = json_decode($_REQUEST['jsonStr'],true);
			foreach ($fldSet as $line) {
				$ptr = new MaterialFields;
				if (substr($line['id'],0,5) == 'zqzqz') {
				  ## add new entries
    			$line['id'] = NULL;
					$ptr->insert_el($line);
				}
				else {
				  ## update position of existing material
					$line['material_field_id'] = $line['id'];
    			$line['id'] = NULL;
					$ptr->update_el($line);
				}
			}
			break;
			
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: $_REQUEST[mode]</h4><br />";
		break;
	}
