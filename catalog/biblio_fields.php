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
      <?php echo $headerWording;?> Bibliography:
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Barcode Number:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Type of Material:
    </td>
    <td valign="top" class="primary">
      <?php printSelect("materialCd","material_type_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Title:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("title",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Subtitle:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("subtitle",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Author:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("author",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Additional Authors:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("addAuthor",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Edition:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("edition",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Collection:
    </td>
    <td valign="top" class="primary">
      <?php printSelect("collectionCd","collection_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Call Number:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("callNmbr",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary">
      International Standard Book Nmbr (ISBN):
    </td>
    <td valign="top" class="primary">
      <?php printInputText("isbnNmbr",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary">
      Library of Congress Cntrl Nmbr (LCCN):
    </td>
    <td valign="top" class="primary">
      <?php printInputText("lccnNmbr",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary">
      Library of Congress Call Nmbr:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("lcCallNmbr",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary">
      Library of Congress Item Nmbr:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("lcItemNmbr",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary">
      Universal Decimal Classification Nmbr:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("udcNmbr",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary">
      Universal Decimal Classification Edition:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("udcEdNmbr",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Name of Publisher:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("publisher",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Date of Publication:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("publicationDt",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Place of Publication:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("publicationLoc",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Summary:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("summary",40,800,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Number of Pages:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("pages",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Other Physical Details:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("physicalDetails",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Dimensions:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("dimensions",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Accompanying Material:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("accompanying",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Purchase Price:
    </td>
    <td valign="top" class="primary">
      $ <?php printInputText("price",5,5,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  Submit  ">
      <input type="button" onClick="parent.location='<?php echo $cancelLocation;?>'" value="  Cancel  ">
    </td>
  </tr>

</table>
