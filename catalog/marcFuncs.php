<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

  #****************************************************************************
  #*  Read for field value descriptions
  #****************************************************************************
  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");

/**********************************************************************************
 * getTagDesc - sets tag, ind, and subfield descriptions
 * @param string $tag input field tag
 * @param string $subfldCd input field subfield code
 * @param string $tagDesc returned tag description
 * @param string $subfldDesc returned subfield description
 * @param string $ind1Desc returned ind1 description
 * @param string $ind2Desc returned ind1 description
 * @return void
 * @access public
 **********************************************************************************
 */
function getTagDesc($tag,$subfldCd,&$tagDesc,&$subfldDesc,&$ind1Desc,&$ind2Desc){
  $tagDesc = "";
  $subfldDesc = "";
  $ind1Desc = "";
  $ind2Desc = "";
  if ($tag != "") {
    $marcTagDmQ = new UsmarcTagDmQuery();
    $marcTagDmQ->connect();
    if ($marcTagDmQ->errorOccurred()) {
      $marcTagDmQ->close();
      displayErrorPage($marcTagDmQ);
    }
    $marcTag = $marcTagDmQ->query($tag);
    if ($marcTagDmQ->errorOccurred()) {
      $marcTagDmQ->close();
      displayErrorPage($marcTagDmQ);
    }
    $marcTagDmQ->close();
    if ($marcTag) {
      $tagDesc = $marcTag->getDescription();
      $ind1Desc = $marcTag->getInd1Description();
      $ind2Desc = $marcTag->getInd2Description();

      # reading for subfield description
      $marcSubfldDmQ = new UsmarcSubfieldDmQuery();
      $marcSubfldDmQ->connect();
      if ($marcSubfldDmQ->errorOccurred()) {
        $marcSubfldDmQ->close();
        displayErrorPage($marcSubfldDmQ);
      }
      $marcSubfld = $marcSubfldDmQ->query($tag, $subfldCd);
      if ($marcSubfldDmQ->errorOccurred()) {
        $marcSubfldDmQ->close();
        displayErrorPage($marcSubfldDmQ);
      }
      $marcSubfldDmQ->close();
      if (!$marcSubfld) {
        $subfldDesc = "";
      } else {
        $subfldDesc = $marcSubfld->getDescription();
      }
    }
  }
  return true;
}
?>
