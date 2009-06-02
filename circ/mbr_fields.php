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
?>

<table class="primary">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php echo $headerWording;?> <?php print $loc->getText("mbrFldsHeader"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsCardNmbr"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("barcodeNmbr",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsLastName"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("lastName",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsFirstName"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("firstName",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsAddr1"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("address1",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsAddr2"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("address2",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsCity"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("city",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsStateZip"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printSelect("state","state_dm",$postVars); ?>
      <?php printInputText("zip",5,5,$postVars,$pageErrors); ?>-<?php printInputText("zipExt",4,4,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsHomePhone"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("homePhone",15,15,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsWorkPhone"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("workPhone",15,15,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsEmail"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("email",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsClassify"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printSelect("classification","mbr_classify_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsGrade"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("schoolGrade",2,2,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrFldsTeacher"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("schoolTeacher",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php print $loc->getText("mbrFldsSubmit"); ?>" class="button">
      <input type="button" onClick="parent.location='<?php echo $cancelLocation;?>'" value="<?php print $loc->getText("mbrFldsCancel"); ?>" class="button">
    </td>
  </tr>

</table>
