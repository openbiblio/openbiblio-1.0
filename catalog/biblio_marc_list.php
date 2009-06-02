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
  $nav = "editmarc";
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioField.php");
  require_once("../classes/BiblioFieldQuery.php");
  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $bibid = $HTTP_GET_VARS["bibid"];
  if (isset($HTTP_GET_VARS["msg"])) {
    $msg = "<font class=\"error\">".stripslashes($HTTP_GET_VARS["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }
  require_once("../shared/header.php");

  #****************************************************************************
  #*  Loading up an array ($marcArray) with the USMarc tag descriptions.
  #****************************************************************************
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

  #****************************************************************************
  #*  Execute query
  #****************************************************************************
  $fieldQ = new BiblioFieldQuery();
  $fieldQ->connect();
  if ($fieldQ->errorOccurred()) {
    $fieldQ->close();
    displayErrorPage($fieldQ);
  }
  $fieldQ->execSelect($bibid);
  if ($fieldQ->errorOccurred()) {
    $fieldQ->close();
    displayErrorPage($fieldQ);
  }

  function getDescription(&$dm, $key) {
    if (array_key_exists($key, $dm)) {
      return $dm[$key]->getDescription();
    } else {
      return '';
    }
  }
  #****************************************************************************
  #*  Start of body
  #****************************************************************************
  ?>
  
  <a href="../catalog/biblio_marc_new_form.php?bibid=<?php echo $bibid;?>&reset=Y">
    <?php echo $loc->getText("biblioMarcListMarcSelect"); ?></a><br/>

  <!--
   ****************************************************************************
   *  Table header
   ****************************************************************************
  -->
  <h1><?php echo $loc->getText("biblioMarcListHdr"); ?>:</h1>

  <?php echo $msg ?>

  <table class="primary">
    <tr>
      <th colspan="2" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol1"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol2"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol3"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol4"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol5"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol6"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol7"); ?>
      </th>
      <th align="left" nowrap="yes">
        <?php echo $loc->getText("biblioMarcListTbleCol8"); ?>
      </th>
    </tr>

  <?php
  #****************************************************************************
  #*  Display USMarc data
  #****************************************************************************
  if ($fieldQ->getRowCount() == 0) {
    ?>
      <tr>
        <td class="primary" colspan="9">
          <?php echo $loc->getText("biblioMarcListNoRows"); ?>
        </td>
      </tr>
    <?php
  } else {
    $row_class = "primary";
    while ($fld = $fieldQ->fetchField()) {
      $tag = sprintf("%03d",$fld->getTag());
      $subfldIndex = $tag.$fld->getSubfieldCd();
      ?>
    <tr>
      <td valign="top" class="<?php echo $row_class;?>">
        <a href="../catalog/biblio_marc_edit_form.php?bibid=<?php echo $bibid;?>&fieldid=<?php echo $fld->getFieldid();?>&reset=Y">edit</a>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <a href="../catalog/biblio_marc_del_confirm.php?bibid=<?php echo $bibid;?>&fieldid=<?php echo $fld->getFieldid();?>&tag=<?php echo $tag;?>&subfieldCd=<?php echo $fld->getSubfieldCd();?>">del</a>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo $tag; ?>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo getDescription($marcTags, $fld->getTag()); ?>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo $fld->getInd1Cd(); ?>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo $fld->getInd2Cd(); ?>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo $fld->getSubfieldCd(); ?>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo getDescription($marcSubflds, $subfldIndex); ?>
      </td>
      <td valign="top" class="<?php echo $row_class;?>">
        <?php echo $fld->getFieldData(); ?>
      </td>
    </tr>
      <?php
      # swap row color
      if ($row_class == "primary") {
        $row_class = "alt1";
      } else {
        $row_class = "primary";
      }
    }
  }
  $fieldQ->close();
  ?>
  </table>

<?php include("../shared/footer.php"); ?>
