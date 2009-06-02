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
    <th colspan="5" valign="top" nowrap="yes" align="left">
      <?php echo $headerWording;?> Theme:
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Theme Name:
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("themeName",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Table Border Color:
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("borderColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Error Color:
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("primaryErrorColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Table Border Width:
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("borderWidth",2,2,$postVars,$pageErrors); ?>px
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Table Cell Padding:
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("tablePadding",2,2,$postVars,$pageErrors); ?>px
    </td>
  </tr>


  <tr>
    <th valign="top">
      &nbsp;
    </td>
    <th valign="top">
      Title
    </td>
    <th valign="top">
      Main Body
    </td>
    <th valign="top">
      Navigation
    </td>
    <th valign="top">
      Tabs
    </td>
  </tr>


  <tr>
    <td nowrap="true" class="primary">
      Background Color:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("titleBg",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("primaryBg",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt1Bg",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt2Bg",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Font Face:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("titleFontFace",10,128,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("primaryFontFace",10,128,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt1FontFace",10,128,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt2FontFace",10,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Font Size:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("titleFontSize",2,2,$postVars,$pageErrors); ?>px
      <input type="checkbox" name="titleFontBold" value="CHECKED"
        <?php if (isset($postVars["titleFontBold"])) echo $postVars["titleFontBold"]; ?> >
      bold
    </td>
    <td valign="top" class="primary">
      <?php printInputText("primaryFontSize",2,2,$postVars,$pageErrors); ?>px
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt1FontSize",2,2,$postVars,$pageErrors); ?>px
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt2FontSize",2,2,$postVars,$pageErrors); ?>px
      <input type="checkbox" name="alt2FontBold" value="CHECKED"
        <?php if (isset($postVars["alt2FontBold"])) echo $postVars["alt2FontBold"]; ?> >
      bold
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Font Color:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("titleFontColor",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("primaryFontColor",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt1FontColor",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt2FontColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Link Color:
    </td>
    <td valign="top" class="primary">
      &nbsp;
    </td>
    <td valign="top" class="primary">
      <?php printInputText("primaryLinkColor",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt1LinkColor",10,20,$postVars,$pageErrors); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt2LinkColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Align:
    </td>
    <td valign="top" class="primary">
      <select name="titleAlign">
        <option value="left"
          <?php if ($postVars["titleAlign"] == "left") echo " selected"; ?>
         >left
        <option value="center"
          <?php if ($postVars["titleAlign"] == "center") echo " selected"; ?>
         >center
        <option value="right"
          <?php if ($postVars["titleAlign"] == "right") echo " selected"; ?>
        >right
      </select>
    </td>
    <td colspan="3" valign="top" class="primary">
      &nbsp;
    </td>
  </tr>

  <tr>
    <td align="center" colspan="5" class="primary">
      <input type="button" onClick="javascript:editTheme()" value="  Submit  " class="button">
      <input type="button" onClick="parent.location='../admin/theme_list.php'" value="  Cancel  " class="button">
    </td>
  </tr>

</table>
