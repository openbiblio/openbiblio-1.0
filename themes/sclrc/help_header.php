<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once(REL(__FILE__, "../../classes/Localize.php"));
  $loc = new Localize(Settings::get('locale'),"shared");

// code html tag with language attribute if specified.
echo "<html";
if (Settings::get('html_lang_attr') != "") {
  echo " lang=\"".H(Settings::get('html_lang_attr'))."\"";
}
echo ">\n";

// code character set if specified
if (Settings::get('charset') != "") { ?>
<META http-equiv="content-type" content="text/html; charset=<?php echo H(Settings::get('charset')); ?>">
<?php } ?>

<link rel="stylesheet" type="text/css" href="../css/style.php" />
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo $loc->getText("OpenBiblio Help"); ?></title>


<script type="text/javascript">
<!--
function popSecondaryLarge(url) {
    var SecondaryWin;
    //SecondaryWin = window.open(url,"inet","toolbar=yes,resizable=yes,scrollbars=yes,width=700,height=500");
    SecondaryWin = window.open(url,"inet");
    self.name="main";
}
-->
</script>


</head>
<body onload="self.focus()">


<!-- **************************************************************************************
     * Library Name and hours
     **************************************************************************************-->
<table class="help_head" width="100%">
  <tr>
    <td align="left">
      <?php echo $loc->getText("OpenBiblio Help"); ?>
    </td>
    <td align="right"><a href="javascript:window.close()"><?php echo $loc->getText("helpHeaderCloseWin"); ?></a>&nbsp;&nbsp;</td>
  </tr>
</table>
<!-- **************************************************************************************
     * Left nav
     **************************************************************************************-->
<table id="main" height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td id="help_sidebar">
      <?php if (!isset($_GET["page"])) {
        echo "&raquo; ".T("Contents");
      } else { ?>
        <a href="../shared/help.php" class="alt1"><?php echo $loc->getText("helpHeaderContents"); ?></a>
      <?php } ?>
      <br />
      <a href="javascript:self.print();" class="alt1"><?php echo $loc->getText("Print"); ?></a><br />
    </td>
    <td id="content">
