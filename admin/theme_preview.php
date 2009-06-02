<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $theme = array(
  "title_bg"=>$_POST["titleBg"],
  "title_font_face"=>$_POST["titleFontFace"],
  "title_font_size"=>$_POST["titleFontSize"],
  "title_font_bold"=>isset($_POST["titleFontBold"]),
  "title_align"=>$_POST["titleAlign"],
  "title_font_color"=>$_POST["titleFontColor"],

  "primary_bg"=>$_POST["primaryBg"],
  "primary_font_face"=>$_POST["primaryFontFace"],
  "primary_font_size"=>$_POST["primaryFontSize"],
  "primary_font_color"=>$_POST["primaryFontColor"],
  "primary_link_color"=>$_POST["primaryLinkColor"],
  "primary_error_color"=>$_POST["primaryErrorColor"],

  "alt1_bg"=>$_POST["alt1Bg"],
  "alt1_font_face"=>$_POST["alt1FontFace"],
  "alt1_font_size"=>$_POST["alt1FontSize"],
  "alt1_font_color"=>$_POST["alt1FontColor"],
  "alt1_link_color"=>$_POST["alt1LinkColor"],

  "alt2_bg"=>$_POST["alt2Bg"],
  "alt2_font_face"=>$_POST["alt2FontFace"],
  "alt2_font_size"=>$_POST["alt2FontSize"],
  "alt2_font_color"=>$_POST["alt2FontColor"],
  "alt2_link_color"=>$_POST["alt2LinkColor"],
  "alt2_font_bold"=>isset($_POST["alt2FontBold"]),

  "border_color"=>$_POST["borderColor"],
  "border_width"=>$_POST["borderWidth"],
  "padding"=>$_POST["tablePadding"],
  );

?>
<html>
<head>
<style type="text/css">
  <?php include(REL(__FILE__, "../css/style.php"));?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo $_POST["themeName"].' '.T("Theme Preview"); ?></title>
</head>
<body onLoad="self.focus()">
<div class="title">
  <div style="float: left">
    <a href="javascript:window.close()"><?php echo T("Close Window"); ?></a>
  </div>
  <h1><?php echo T("Library Title"); ?></h1>
</div>
<hr id="end_title" />
<!-- **************************************************************************************
     * Left nav
     **************************************************************************************-->
<?php
// cellspacing="0" cellpadding="0" works around IE's lack of
// support for CSS2's border-spacing property.
?>
<table id="main" height="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td id="sidebar">
<?php
Nav::node('preview', T("Theme Preview"));
Nav::node('link', T("Sample link"), '#');
Nav::display('preview');
?>
      <p><a href="http://obiblio.sourceforge.net/"><img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0"></a></p>
    </td>
    <td id="content">
      <font class="primary">
      <br />
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->

<?php echo T("This is a preview of the %name% theme.", array('name'=>$_POST["themeName"])); ?>

<h1><?php echo T("Sample List:"); ?></h1>
<table class="primary">
  <tr>
    <th valign="top">
      <?php echo T("Table Heading"); ?>
    </th>
  </tr>
  <tr>
    <td valign="top" class="primary">
      <?php echo T("Sample data row 1"); ?>
    </td>
  </tr>
  <tr>
    <td valign="top" class="alt1">
      <?php echo T("Sample data row 2"); ?>
    </td>
  </tr>
  <tr>
    <td valign="top" class="primary">
      <?php echo T("Sample data row 3"); ?>
    </td>
  </tr>
</table>
<br />
<a href="#"><?php echo T("Sample link"); ?></a><br />
<p class="error"><?php echo T("Sample error"); ?></p>
<br />
<form>
<input type="text" name="sampleInput" size="40" maxlength="40" value="<?php echo T("Sample Input"); ?>" /><br />
<input type="button" value="<?php echo T("Sample Button"); ?>" class="button" />
</form>

<?php

  require_once(REL(__FILE__, '../shared/help_footer.php'));
