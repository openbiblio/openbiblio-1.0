<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "cataloging";
$nav = "upload_csv";

# Big uploads take a while
set_time_limit(120);

require_once(REL(__FILE__, "../functions/marcFuncs.php"));
require_once(REL(__FILE__, "../model/MediaTypes.php"));
require_once(REL(__FILE__, "../model/Collections.php"));
require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/Cart.php"));
require_once(REL(__FILE__, "../model/MarcDBs.php"));
//require_once(REL(__FILE__, "../classes/Marc.php"));

require_once(REL(__FILE__, "../shared/logincheck.php"));

if (count($_FILES) == 0) {
	header("Location: ../catalog/upload_csv_form.php");
	exit();
}

/* input file delimiters */
$recordterminator="\n";
$fieldterminator="\t";

/* get entire input file into an array of records in memory */
$records = explode($recordterminator, file_get_contents($_FILES["csv_data"]["tmp_name"]));

//check the last record if there is content after the delimiter
$record = array_pop($records);
if (trim($record) != "") {
  array_push($records, $record);
}
//echo "raw records: ";var_dump($records); echo "<br /><br />";

// form an array of the column titles as encoded in the header
$record = array_shift($records);

/* -------------------------------------------------------- */
/* begin web page html */
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3 id="searchHdr"><?php echo T('CSVImport'); ?></h3>

<p>
  <?php echo T("CSVRecordsRead").' => '. count($records); ?>.
</p>

<fieldset>
<legend><?php echo T("CSVHeadings"); ?></legend>
<table>
  <tr>
    <th><?php echo T("CSVTargets"); ?></th>
    <th><?php echo T("CSVComments"); ?></th>
  </tr>
  
<?php
	/* print column headings given in the first row of the input file */
	$targets = explode($fieldterminator, $record);
	foreach($targets as $target) {
	  $target = trim($target);
	  echo "<tr>\n";
	  echo "  <td>".$target."</td>\n";
	  switch ($target) {
	  case "barCo":
	    echo "  <td>".T("Barcode Number")."</td>\n";
	    break;
	  case "Coll.":
	    echo "  <td>".T("Collection")."</td>\n";
	    break;
	  case "media":
	    echo "  <td>".T("Media Type")."</td>\n";
	    break;
	  case "opac?":
	    echo "  <td>".T("opacFlag")."</td>\n";
	    break;
	  case "Call1":
	  case "Call2":
	  case "Call3":
	    echo "  <td>".T("CallNmbr")." (".substr($target, 4, 1).")</td>\n";
	    break;
	  default:
	  	echo "  <td>\n";
	    if (preg_match('/^[0-9][0-9]*\$[a-z]$/', $target)) {
	      $tag = explode('$', $target);
				$ptr = new MarcSubfields;
				$params = array('tag' =>$tag[0], 'subfield_cd' =>$tag[1] );
			  $vals = array();
				$rslt = $ptr->getMatches($params, 'subfield_cd');
				while ($row = $rslt->next()) {
				  $vals[] = $row;
				}
				$val = $vals[0]['description'];
	      echo $val;
	    } else {
	      echo T("CSVunknownIgnored");
	    }
	    echo "</td>\n";
	    break;
	  }
	  echo "</tr>\n";
	}
?>
</table>
</fieldset>

<?php
/* --------------------------------------------------------- */
/* process each inport record starting after the heading row */
$cols = new Collections;
$collections = $cols->getSelect();
$dfltColl = $collections[$cols->getDefault()];
//print_r($collections); echo " dflt: $dfltColl<br />";

$meds = new MediaTypes;
$medTypes = $meds->getSelect();
$dfltMed = $medTypes[$meds->getDefault()];
//print_r($medTypes); echo " dflt: $dfltMed<br />";

$biblios = new Biblios();
$cart = new Cart('bibid');

$errorList = array();
$recCount = 0;
$newBarcodes = array();

// TODO: For import of multiple copies, introduce copies array?


foreach($records as $record) {
  $record = trim($record);
	//var_dump($record);echo "<br />";
  if ($record == "") {
    continue; // skip over blank records
  }
  $colCount = 0;
  $recCount++;

  $validate = true;
  $localErrors = array();
  $localWarnings = array();
    
  $mandatoryCols = array(
    "Call1" => false,
    '245$a' => false,
    '100$a' => false,
	);
	
	$biblio = array(
		'last_change_userid' => $_SESSION["userid"],
	);
  $biblio["barCo"] = "";
	
	/* now process each field in the record */
  $entries = explode($fieldterminator, $record);
  for($colCount = 0; $colCount < count($targets); $colCount ++) {
    $entry = trim($entries[$colCount]);
    $target = trim($targets[$colCount]);
		//echo "working column $target with entry: $entry <br />";    

    if (isset($mandatoryCols[$target])) $mandatoryCols[$target] = true;
    
    switch ($target) {
    case "barCo":
      $biblio["barCo"] = $entry;
      break;
    case "media":
      if (in_array($entry, $medTypes)) {
        $thisOne = $entry;
      } else {
        array_push($localWarnings,T("CSVCollUnknown").": ".$entry."; using '".$dfltMed."'.");
        $thisOne = $dfltMed;
      }
      $biblio["material_cd"] = array_search($thisOne, $medTypes);
      break;
    case "Coll.":
      if (in_array($entry, $collections)) {
        $thisOne = $entry;
      } else {
        array_push($localWarnings,T("CSVCollUnknown").": ".$entry."; using '".$dfltColl."'.");
        $thisOne = $dfltColl;
      }
      $biblio["collection_cd"] = array_search($thisOne, $collections);
      break;
    case "opac?":
      if (preg_match('/^[yYtT]/', $entry)) {
        $biblio["opac_flg"] = true;
      } else {
        $biblio["opac_flg"] = false;
      }
      break;
    case "Call1":
      $biblio["Call1"] = $entry;
      break;
    case "Call2":
      $biblio["Call2"] = $entry;
      break;
    case "Call3":
      $biblio["Call3"] = $entry;
      break;
    default:
      if (preg_match('/^[0-9][0-9]*\$[a-z]$/', $target)) {
        $tag = explode('$', $target);
				$biblio[$target]["tag"]= $tag[0];
				$biblio[$target]["sf"]= $tag[1];
				$biblio[$target]["data"] = $entry;
      }
      break;
    }
  }
    
  // Check for uniqueness with existing barcodes and new entries read.
  $barcode = $biblio["barCo"];
  if ($barcode != "") {
    if (in_array($barcode, $newBarcodes)) {
      array_push($localErrors, T("biblioCopyQueryErr1"));
      $validate = false;
    }
    // push new barcode into validation array after validation to each the check.
    array_push($newBarcodes, $barcode);
  }

  // Check for mandatory entries
  foreach($mandatoryCols as $col => $seen) {
    if (! $seen) {
      array_push($localErrors, "Missing column entry: '".$col."'");
      $validate = false;
    }
  }
  
  // validate imported data
  if (count($localErrors)) {
    $validate = false;
  }
  if ($validate != true) {
    array_push($errorList, $recCount);
  }

  if (($validate != true) || (($_POST["test"]=="true") && ($_POST["showAll"]=="Y"))) {
  	// Just display the record. Don't keep it in a array due to memory reasons.
    echo "<a name=\"".$recCount."\">\n";
    echo "<fieldset>\n";
    echo "<table>\n";
    echo "  <tr><th>".T("Data Tag")."</th><th>".T("Date Subfield")."</th><th>".T("Data")."</th></tr>\n";
    if (($validate != true) || (count($localWarnings)==0)) {
      echo "  <tr><td>&nbsp;</td><td>&nbsp;</td>\n";
      echo "    <td>";
      
	  	if (count($localWarnings)) {
        echo "			<span class=\"warning\">".T("CSVwarning")."<span>:\n";
        echo "			<ul class=\"warning\">\n";
        foreach($localWarnings as $error) {
          if ($error != "") {
            echo "			<li>Warning: ".$error."</li>\n";
          }
        }
        echo "			</ul>\n";
	  	}
	  	
      if ($validate != true) {
        echo "			<span class=\"error\">".T("CSVerror")."</span>:\n";
        echo "			<ul class=\"error\">\n";
        foreach($localErrors as $error) {
          if ($error != "") {
            echo "<li>Error: ".$error."</li>\n";
          }
        }
        echo "			</ul>\n";
	  	}
      echo "		</td>\n";
      echo "  </tr>\n";
    }

		//print_r($biblio);echo "<br />";
  	foreach($biblio as $key=>$val) {
			//echo "working column $key with value:";print_r($val); echo "<br />";    
      echo "  <tr>\n";
			switch($key) {
    		case 'barCo':
    	  	echo "  	<td>Bar Code</td><td>&nbsp;</td><td>".$val."</td>\n";
   				break;
    		case 'collection_cd':
      		echo "    <td>Collection</td><td>&nbsp;</td><td>".$collections[$val]."</td>\n";
    			break;
    		case 'material_cd':
      		echo "    <td>Media Type</td><td>&nbsp;</td><td>".$medTypes[$val]."</td>\n";
      		break;
    		case 'opac_flg':
      		echo "    <td>Show OPAC</td><td>&nbsp;</td>\n";
      		echo "    <td>".($entry == true?"true":"false")."</td>\n";
      		break;
    		case 'Call1':
      		echo "    <td>Call Nmbr(s)</td><td>&nbsp;</td>\n";
      		echo "    <td>".$val;
      		$entry = $biblio["Call2"];
      		if ((isset($entry)) && ($entry != "")) {
      		  echo " ".$entry;
      		  $entry = $biblio["Call3"];
      		  if ((isset($entry)) && ($entry != "")) {
      		    echo " ".$entry;
      		  }
      		}
      		echo "		</td>\n";
      		break;
      	default:
		      if (preg_match('/^[0-9][0-9]*\$[a-z]$/', $key)) {
	      		echo "    <td>".$val['tag']."</td>\n";
	      		echo "    <td>".$val['sf']."</td>\n";
	      		echo "		<td>".$val['data']."</td>\n";
		      }
	      	break;
    	} //switch()
 		  echo "  </tr>\n";
    } // foreach()

    echo "</table>";
    echo "</fieldset>\n";
  } else {
    // THE import. Re-check - just in case someone changed above code...
  	echo "<fieldset>\n";
  		print_r($biblio); echo "<br /><br />";
/*
    // following is left over from 0.7 patch - not usable except conceptually
    if ($_POST["test"]!="true") {
      $bq = new BiblioQuery();
      $bq->connect();
      if ($bq->errorOccurred()) {
        $bq->close();
        displayErrorPage($bq);
      }
      $bibId = $bq->insert($biblio);
      if (!$bibId) {
        $bq->close();
        displayErrorPage($bq);
      }
      $bq->close();

      if ($barcode != "") {
        $copy->setBibid($bibId);
        $copyQ = new BiblioCopyQuery();
        $copyQ->connect();
        if ($copyQ->errorOccurred()) {
          $copyQ->close();
          displayErrorPage($copyQ);
        }
        if (!$copyQ->insert($copy)) {
          $copyQ->close();
          if ($copyQ->getDbErrno() == "") {
            echo "<font color=red>".T("CSVerror").": ".$copyQ->getError()."</font>\n";
            exit();
          } else {
            displayErrorPage($copyQ);
          }
        }
        $copyQ->close();
      }

      $biblioFlds = $biblio->getBiblioFields();
      if ($_POST["showAll"]=="Y") {
        echo "<a href=\"../shared/biblio_view.php?bibid=".U($bibId)."\">";
        echo T("CSVadded").": <i>".
          H($biblioFlds["245a"]->getFieldData())."</i></a><br>";
      }
    }
*/	
  }

	echo "</fieldset>\n";
  // At the end, update the counter display.
  if ($recCount % 10 == 0) {
    echo "<script type=\"text/javascript\">\n";
    echo "  window.document.Display.Records.value = ".$recCount.";\n";
    echo "</script>\n";
  }

}

echo "<script type=\"text/javascript\">\n";
echo "  window.document.Display.Records.value = ".$recCount.";\n";
echo "</script>\n";

if (count($errorList)){
  echo "<ul>\n";
  foreach($errorList as $error) {
    echo "<li><a href=\"#".$error."\">".
      T("CSVerrorAtRecord")." ".$error."</a></li>\n";
  }
  echo "</ul>\n";
}

echo "<p>".count($errorList)." ".T("CSVerrors").".</p>\n";
/*
  $marcTagDmQ = new UsmarcTagDmQuery();
  $marcTagDmQ->connect();
  if ($marcTagDmQ->errorOccurred()) {
    $marcTagDmQ->close();
    displayErrorPage($marcTagDmQ);
  }
  $marcTagDmQ->execSelect();
  if ($marcTagDmQ->errorOccurred()) {
    $marcTagDmQ->close();
    displayErrorPage($marcTagDmQ);
  }
  $marcTags = $marcTagDmQ->fetchRows();
  $marcTagDmQ->close();

  $marcSubfldDmQ = new UsmarcSubfieldDmQuery();
  $marcSubfldDmQ->connect();
  if ($marcSubfldDmQ->errorOccurred()) {
    $marcSubfldDmQ->close();
    displayErrorPage($marcSubfldDmQ);
  }
  $marcSubfldDmQ->execSelect();
  if ($marcSubfldDmQ->errorOccurred()) {
    $marcSubfldDmQ->close();
    displayErrorPage($marcSubfldDmQ);
  }
  $marcSubflds = $marcSubfldDmQ->fetchRows();
  $marcSubfldDmQ->close();
*/

include("../shared/footer.php");
?>
