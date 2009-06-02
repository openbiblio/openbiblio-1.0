
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


  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../catalog/inputFuncs.php");

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

?>

<font class="small">
<?php echo $loc->getText("catalogFootnote",array("symbol"=>"</font>*<font class=\"small\">")); ?>
</font>

<table class="primary">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php
        echo $headerWording." ";
        echo $loc->getText("biblioFieldsLabel");
      ?>:
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <sup>*</sup> <?php echo $loc->getText("biblioFieldsMaterialTyp"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printSelect("materialCd","material_type_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <sup>*</sup> <?php echo $loc->getText("biblioFieldsCollection"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printSelect("collectionCd","collection_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <sup>*</sup> <?php echo $loc->getText("biblioFieldsCallNmbr"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("callNmbr1",20,20,$postVars,$pageErrors); ?><br>
      <?php printInputText("callNmbr2",20,20,$postVars,$pageErrors); ?><br>
      <?php printInputText("callNmbr3",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioFieldsOpacFlg"); ?>:
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="opacFlg" value="CHECKED"
        <?php if (isset($postVars["opacFlg"])) echo $postVars["opacFlg"]; ?> >
    </td>
  </tr>

  <tr>
    <td colspan="2" nowrap="true" class="primary">
      <b><?php echo $loc->getText("biblioFieldsUsmarcFields"); ?>:</b>
    </td>
  </tr>

  <?php printUsmarcInputText(245,"a",TRUE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(245,"b",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(245,"c",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(100,"a",TRUE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(650,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(650,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"1");?>
  <?php printUsmarcInputText(650,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"2");?>
  <?php printUsmarcInputText(650,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"3");?>
  <?php printUsmarcInputText(650,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"4");?>
  <?php printUsmarcInputText(250,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(20,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(10,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(50,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(50,"b",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(82,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(82,"2",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(260,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(260,"b",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(260,"c",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(520,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXTAREA_CNTRL);?>
  <?php printUsmarcInputText(300,"a",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(300,"b",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(300,"c",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(300,"e",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(20,"c",FALSE,$fieldIds,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>

  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo $loc->getText("catalogSubmit"); ?>" class="button">
      <input type="button" onClick="parent.location='<?php echo $cancelLocation;?>'" value="<?php echo $loc->getText("catalogCancel"); ?>" class="button">
    </td>
  </tr>

</table>
