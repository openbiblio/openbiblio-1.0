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

  session_cache_limiter(null);

  $tab = "cataloging";
  $nav = "editmarcfield";
  $focus_form_name = "editmarcform";
  $focus_form_field = "tag";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../catalog/marcFuncs.php");
  require_once("../classes/BiblioField.php");
  require_once("../classes/BiblioFieldQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  if (isset($HTTP_GET_VARS["reset"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var from edit link
    #****************************************************************************
    $bibid = $HTTP_GET_VARS["bibid"];
    $fieldid = $HTTP_GET_VARS["fieldid"];

    #****************************************************************************
    #*  Reading database for subfield values
    #****************************************************************************
    $fieldQ = new BiblioFieldQuery();
    $fieldQ->connect();
    if ($fieldQ->errorOccurred()) {
      $fieldQ->close();
      displayErrorPage($fieldQ);
    }
    $field = $fieldQ->query($bibid,$fieldid);
    if ($fieldQ->errorOccurred()) {
      $fieldQ->close();
      displayErrorPage($fieldQ);
    }
    $fieldQ->close();

    $postVars["bibid"] = $bibid;
    $postVars["fieldid"] = $bibid;
    $postVars["tag"] = $field->getTag();
    $postVars["ind1Cd"] = $field->getInd1Cd();
    $postVars["ind2Cd"] = $field->getInd2Cd();
    $postVars["subfieldCd"] = $field->getSubfieldCd();
    $postVars["fieldData"] = $field->getFieldData();
    $selectedTag = $field->getTag();
    $selectedSubfld = $field->getSubfieldCd();
    $HTTP_SESSION_VARS["postVars"] = $postVars;

  } else {
    require_once("../shared/get_form_vars.php");
    $bibid = $postVars["bibid"];
    $fieldid = $postVars["fieldid"];
    $selectedTag = $postVars["tag"];
    $selectedSubfld = $postVars["subfieldCd"];
    if (isset($HTTP_GET_VARS["tag"])) {
      #****************************************************************************
      #*  Retrieving get var from field select page
      #****************************************************************************
      $selectedTag = $HTTP_GET_VARS["tag"];
      $postVars["tag"] = $selectedTag;
      if (isset($HTTP_GET_VARS["subfld"])) {
        $selectedSubfld = $HTTP_GET_VARS["subfld"];
        $postVars["subfieldCd"] = $selectedSubfld;
      } else {
        $selectedSubfld = $postVars["subfieldCd"];
      }
      $HTTP_SESSION_VARS["postVars"] = $postVars;
    }
  }

  require_once("../shared/header.php");

  #****************************************************************************
  #*  Read for field value descriptions
  #****************************************************************************
  getTagDesc($selectedTag,$selectedSubfld,$tagDesc,$subfldDesc,$ind1Desc,$ind2Desc);

  $formLabel = $loc->getText("biblioMarcEditFormHdr");
  $returnPg = "biblio_marc_edit_form.php";

  #****************************************************************************
  #*  Start of body
  #****************************************************************************
  ?>
  
<form name="editmarcform" method="POST" action="../catalog/biblio_marc_edit.php">
<?php include("../catalog/biblio_marc_fields.php"); ?>
<input type="hidden" name="bibid" value="<?php echo $bibid;?>">
<input type="hidden" name="fieldid" value="<?php echo $fieldid;?>">
</form>
  


<?php include("../shared/footer.php"); ?>
