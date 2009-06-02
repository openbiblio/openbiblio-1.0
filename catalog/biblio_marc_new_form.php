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
  $nav = "newmarc";
  $focus_form_name = "newmarcform";
  $focus_form_field = "tag";

  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../catalog/marcFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  if (isset($_GET["reset"])) {
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);
    $bibid = $_GET["bibid"];
    $postVars["bibid"] = $bibid;
    $postVars["tag"] = "";
    $postVars["ind1Cd"] = "";
    $postVars["ind2Cd"] = "";
    $postVars["subfieldCd"] = "";
    $postVars["fieldData"] = "";
    $selectedTag = "";
    $selectedSubfld = "";
    $_SESSION["postVars"] = $postVars;
  } else {
    require_once("../shared/get_form_vars.php");
    $bibid = $postVars["bibid"];
    $selectedTag = $postVars["tag"];
    $selectedSubfld = $postVars["subfieldCd"];
    if (isset($_GET["tag"])) {
      #****************************************************************************
      #*  Retrieving get var from field select page
      #****************************************************************************
      $selectedTag = $_GET["tag"];
      $postVars["tag"] = $selectedTag;
      if (isset($_GET["subfld"])) {
        $selectedSubfld = $_GET["subfld"];
        $postVars["subfieldCd"] = $selectedSubfld;
      } else {
        $selectedSubfld = $postVars["subfieldCd"];
      }
      $_SESSION["postVars"] = $postVars;
    }
  }
    
  require_once("../shared/header.php");

  #****************************************************************************
  #*  Read for field value descriptions
  #****************************************************************************
  if (isset($pageErrors["tag"]) && ($pageErrors["tag"] <> "")){
    $tagDesc = "";
    $subfldDesc = "";
    $ind1Desc = "";
    $ind2Desc = "";
  } else {
    getTagDesc($selectedTag,$selectedSubfld,$tagDesc,$subfldDesc,$ind1Desc,$ind2Desc);
  }

  $formLabel = $loc->getText("biblioMarcNewFormHdr");
  $returnPg = "biblio_marc_new_form.php";
  $fieldid = "";

  #****************************************************************************
  #*  Start of body
  #****************************************************************************
  ?>
  
<form name="newmarcform" method="POST" action="../catalog/biblio_marc_new.php">
<?php include("../catalog/biblio_marc_fields.php"); ?>
<input type="hidden" name="bibid" value="<?php echo $bibid;?>">
</form>
  


<?php include("../shared/footer.php"); ?>
