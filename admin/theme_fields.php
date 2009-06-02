<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

?>

<table class="primary">
  <tr>
    <th colspan="5" valign="top" nowrap="yes" align="left">
      <?php echo $headerWording;?> <?php echo T("Theme"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Theme Name"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("themeName",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Table Border Color:"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("borderColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Error Color:"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("primaryErrorColor",10,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Table Border Width:"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("borderWidth",2,2,$postVars,$pageErrors); ?><?php echo T("px"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Table Cell Padding:"); ?>
    </td>
    <td colspan="4" valign="top" class="primary">
      <?php printInputText("tablePadding",2,2,$postVars,$pageErrors); ?><?php echo T("px"); ?>
    </td>
  </tr>


  <tr>
    <th valign="top">
      &nbsp;
    </td>
    <th valign="top">
      <?php echo T("Title"); ?>
    </td>
    <th valign="top">
      <?php echo T("Main Body"); ?>
    </td>
    <th valign="top">
      <?php echo T("Navigation"); ?>
    </td>
    <th valign="top">
      <?php echo T("Tabs"); ?>
    </td>
  </tr>


  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Background Color:"); ?>
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
      <?php echo T("Font Face:"); ?>
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
      <?php echo T("Font Size:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("titleFontSize",2,2,$postVars,$pageErrors); ?><?php echo T("px"); ?>
      <input type="checkbox" name="titleFontBold" value="CHECKED"
        <?php if (isset($postVars["titleFontBold"])) echo $postVars["titleFontBold"]; ?> />
      <?php echo T("bold");?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("primaryFontSize",2,2,$postVars,$pageErrors); ?><?php echo T("px"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt1FontSize",2,2,$postVars,$pageErrors); ?><?php echo T("px"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("alt2FontSize",2,2,$postVars,$pageErrors); ?><?php echo T("px"); ?>
      <input type="checkbox" name="alt2FontBold" value="CHECKED"
        <?php if (isset($postVars["alt2FontBold"])) echo $postVars["alt2FontBold"]; ?> />
      <?php echo T("bold");?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Font Color:"); ?>
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
      <?php echo T("Link Color:"); ?>
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
      <?php echo T("Align:"); ?>
    </td>
    <td valign="top" class="primary">
      <select name="titleAlign">
        <option value="left"
          <?php if ($postVars["titleAlign"] == "left") echo " selected"; ?>
         ><?php echo T("left"); ?>
        <option value="center"
          <?php if ($postVars["titleAlign"] == "center") echo " selected"; ?>
         ><?php echo T("center"); ?>
        <option value="right"
          <?php if ($postVars["titleAlign"] == "right") echo " selected"; ?>
        ><?php echo T("right"); ?>
      </select>
    </td>
    <td colspan="3" valign="top" class="primary">
      &nbsp;
    </td>
  </tr>

  <tr>
    <td align="center" colspan="5" class="primary">
      <input type="button" onclick="editTheme()" value="<?php echo T("Submit"); ?>" class="button" />
      <input type="button" onclick="parent.location='../admin/theme_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
    </td>
  </tr>

</table>
