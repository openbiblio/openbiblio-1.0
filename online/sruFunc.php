<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

define('MARC_TITLE', '245a');
define('MARC_AUTHOR', '100a');
define('MARC_ISBN', '020a');
define('MARC_SUBTITLE', '245b');
define('MARC_PUBLICATION_PLACE', '260a');
define('MARC_PUBLISHER', '260b');
define('MARC_PUBLICATION_DATE', '260c');
define('MARC_PAGES', '300a');
define('MARC_SUBJECT', '650a');

function get_param($param) {
  if(isset($_GET[$param]))   {
    return $_GET[$param];
  }
  else if (isset($_POST[$param]))   {
    return $_POST[$param];
  }
  return NULL;
}

function print_array($array) {
  echo "<pre>";
  if(is_array($array))   {
    print_r($array);
  }
  else   {
    echo htmlentities($array, ENT_QUOTES);
  }
  echo "</pre><br />\n";
}

function startElement($parser, $name, $attribs) {
//  global $process, $subfields, $subfieldcount;

  echo "&lt;<font color=\"#0000cc\">$name</font>";
  if (count($attribs)) {
      foreach ($attribs as $k => $v) {
          echo " <font color=\"#009900\">$k</font>=\"<font color=\"#990000\">$v</font>\"";
      }
  }
  echo "&gt;";
}

function endElement($parser, $name)
{
   echo "&lt;/<font color=\"#0000cc\">$name</font>&gt;\r";
}

/**
 * Data between tags
 */
function characterData($parser, $data) {
   global $process, $subfields, $subfieldcount;

   echo "<b>$data</b>";
   $process['data'] = $data;
}

function defaultHandler($parser, $data) {
   if (substr($data, 0, 1) == "&" && substr($data, -1, 1) == ";") {
       printf('<font color="#aa00aa">%s</font>',
               htmlspecialchars($data));
   } else {
       printf('<font size="-1">%s</font>',
               htmlspecialchars($data));
   }
}

function showXML($data,$display) {
	if($display) {
		for($i = 0; $i < $postVars[numHosts]; $i++) {

		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");
		xml_set_default_handler($xml_parser, "defaultHandler");

		echo "<pre>";
		if (!xml_parse($xml_parser, $data[$i])) {
			die(sprintf("XML error: %s at line %d\n",
					xml_error_string(xml_get_error_code($xml_parser)),
 					xml_get_current_line_number($xml_parser)));
		}
		echo "</pre>";
		xml_parser_free($xml_parser);
	}
	}
}

function get_marc_fields($xml) {
  $marc = array();
  $respVersion = '';
  $recordposition = 0;
  $subcount = 0;
  $total_hits = 0;
  $diagMsg = '';
  $wantMsg = false;
  
  foreach($xml AS $record)   {
    switch($record['tag'])     {
    case 'ZS:VERSION':
      $respVersion = $record['value'];
      break;
    case 'ZS:NUMBEROFRECORDS':
      // Represents total number of records that matched, not actual returned.
      $total_hits = $record['value'];
      break;
    case 'ZS:DIAGNOSTICS':
      if ($record['type'] == 'open') {
      	$attributes = $record['attributes'];
      	$wantMsg = true;
			}
      break;
    case 'MESSAGE':
      if ($wantMsg)  {
				$marc[$recordposition]['diagMsg'] = $record['value'];
      	$wantMsg = false;
			}
      break;
     case 'CONTROLFIELD':
      $attributes = $record['attributes'];
      $marc[$recordposition][$attributes['TAG']] = trim($record['value']);
      break;
    case 'DATAFIELD':
      if(isset($record['attributes']))       {
        $attributes = $record['attributes'];
        $datafield = $attributes['TAG'];
      }
      break;
    case 'SUBFIELD':
      $attributes = $record['attributes'];
      $code = $attributes['CODE'];
      $value = $record['value'];
      $indicie = $datafield . $code;
      $extratrim = '';
      switch($indicie) {
      case MARC_ISBN:
        if (substr($value,0,3) == '978')
        	$value = substr($value, 0, 13);
				else
        	$value = substr($value, 0, 10);
        break;
      case MARC_TITLE:
      case MARC_PUBLICATION_PLACE:
        $extratrim = ':/';
        break;
      case MARC_SUBTITLE:
        $extratrim = '/';
        break;
      case MARC_PUBLISHER:
        $extratrim = ',';
        break;
      case MARC_PAGES:
        $value = (int)($value);
        break;
      }
      if($indicie != MARC_SUBJECT)       {
        if(isset($marc[$recordposition][$indicie]) && !empty($marc[$recordposition][$indicie])) {
          $marc[$recordposition][$indicie] .= ', ' . trim($value, ' '.$extratrim);
        } else {
          $marc[$recordposition][$indicie] = trim($value, ' '.$extratrim);
        }
      } else {
        if($subcount == 0) {
          $marc[$recordposition][$indicie] = trim($value, ' '. $extratrim);
          $subcount++;
        } else {
          $marc[$recordposition][$indicie.$subcount] = trim($value, ' ' . $extratrim);
          $subcount++;
        }
      }
      break;
    case 'ZS:RECORDPOSITION':
      $recordposition++;
      $subcount = 0;
      break;
    }
  }

  /**
   * The ZS:RECORDPOSITION tag does not occur when only one record is returned.
   * Update recordposition to indicate 1 record.
   */
  if (($recordposition == 0) && ($total_hits > 0))
    $recordposition = 1;

  return array($recordposition, $marc);
}

function display_records($marc) {
  global $lookLoc;

  echo "<table>\n";
  echo "  <th colspan='2'>Results</th>\n";
  foreach($marc AS $key => $hit)   {
    if(isset($hit['245a'])) { // Book must have a title
      echo "  <tr>\n";
      echo "    <td class='primary'>\n";
      echo "      <b>" . T('locsru_Title') .": </b>" . $hit[MARC_TITLE] . "<br />\n";
      if(isset($hit[MARC_AUTHOR])) {
        echo "      <b>" . T('locsru_Author') .": </b>" . $hit[MARC_AUTHOR] . "<br />\n";
      }
      if(isset($hit['020a'])) {
        echo "      <b>" . T('locsru_ISBN') . ": </b>" . $hit[MARC_ISBN] . "<br />\n";
      }
      if(isset($hit[MARC_PUBLICATION_PLACE]) && isset($hit[MARC_PUBLISHER])) {
        echo '      <b>' . T('locsru_Publication') . ': </b>' . $hit[MARC_PUBLICATION_PLACE] . ': ' .$hit[MARC_PUBLISHER] . "<br />\n";
      }
      else if(isset($hit[MARC_PUBLISHER])) {
        echo '      <b>' . T('locsru_Publisher') . ': </b>' . $hit[MARC_PUBLISHER] . "<br />\n";
      }
      if(isset($hit[MARC_PUBLICATION_DATE]))  {
        echo '      <b>' . T('locsru_PublicationDate') . ': </b>' . $hit[MARC_PUBLICATION_DATE] . "<br />\n";
      }
      echo "    </td>\n";
      echo "    <td valign='top'>\n";
      echo "      <form name=\"form$key\" action=\"$_SERVER[PHP_SELF]\" method=\"POST\"> \n";
      echo "        <input type='submit' name='submit' value='" . TS('lookup_UseThis') . "' class='button' />\n";
      echo "        <input type='hidden' name='mode' value='edit' />\n";
      foreach($hit AS $k => $v)       {
        echo "        <input type='hidden' name='" . $k ."' value='". htmlentities($v, ENT_QUOTES) ."' />\n";
      }
      echo "      </form>\n";
      echo "    </td>\n";
      echo "  </tr>\n";
    }
  }
  echo "</table>";
}
?>
