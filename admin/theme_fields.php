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
      <? echo $loc->getText("adminTheme_HeaderWording"); ?><? echo $loc->getText("adminTheme_Theme2"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Themename"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("themeName",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Tablebordercolor"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("borderColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Errorcolor"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("primaryErrorColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Tableborderwidth"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("borderWidth",2,2,$postVars,$pageErrors); ?>px
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Tablecellpadding"); ?>
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
      <? echo $loc->getText("adminTheme_Title"); ?>
    </td>
    <th valign="top">
      <? echo $loc->getText("adminTheme_Mainbody"); ?>
    </td>
    <th valign="top">
      <? echo $loc->getText("adminTheme_Navigation"); ?>
    </td>
    <th valign="top">
      <? echo $loc->getText("adminTheme_Tabs"); ?>
    </td>
  </tr>


  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Backgroundcolor"); ?>
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
      <? echo $loc->getText("adminTheme_Fontface"); ?>
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
      <? echo $loc->getText("adminTheme_Fontsize"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("titleFontSize",2,2,$postVars,$pageErrors); ?>px
      <input type="checkbox" name="titleFontBold" value="CHECKED"
        <?php if (isset($postVars["titleFontBold"])) echo $postVars["titleFontBold"]; ?> >
      <? echo $loc->getText("adminTheme_Bold"); ?>
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
      <? echo $loc->getText("adminTheme_Bold"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminTheme_Fontcolor"); ?>
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
      <? echo $loc->getText("adminTheme_Linkcolor"); ?>
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
      <? echo $loc->getText("adminTheme_Align"); ?>
    </td>
    <td valign="top" class="primary">
      <select name="titleAlign">
        <option value="left"
          <?php if ($postVars["titleAlign"] == "left") echo " selected"; ?>
         ><? echo $loc->getText("adminTheme_Left"); ?>
        <option value="center"
          <?php if ($postVars["titleAlign"] == "center") echo " selected"; ?>
         ><? echo $loc->getText("adminTheme_Center"); ?>
        <option value="right"
          <?php if ($postVars["titleAlign"] == "right") echo " selected"; ?>
        ><? echo $loc->getText("adminTheme_Right"); ?>
      </select>
    </td>
    <td colspan="3" valign="top" class="primary">
      &nbsp;
    </td>
  </tr>

  <tr>
    <td align="center" colspan="5" class="primary">
      <input type="button" onClick="javascript:editTheme()" value="  <? echo $loc->getText("adminSubmit"); ?>  " class="button">
      <input type="button" onClick="parent.location='../admin/theme_list.php'" value="  <? echo $loc->getText("adminCancel"); ?>  " class="button">
    </td>
  </tr>

</table>
