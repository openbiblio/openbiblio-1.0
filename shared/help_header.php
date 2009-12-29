<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

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

<!--link rel="stylesheet" type="text/css" href="../css/style.php" /-->
<link rel="stylesheet" type="text/css" href="../shared/base.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $ThemeDirUrl; ?>/style.css" />
<style>
/*************************************************/
div#sidebar {
	position: absolute; top:0; left: .5em;
	width: 7em;
	margin: 0; padding: 10px;
	background: #a0c0c8; /*for development*/
	vertical-align: top;
	border: solid black 2px;
	}
div#content {
	position: absolute; top:0; left: 9.5em;
	vertical-align: top;
	border: solid black 2px;
	background: white;
	padding-left: 5px; padding-right: 5px; padding-bottom: 5px;
	}
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo T("OpenBiblio Help"); ?></title>

<!-- jQuery kernal, needed for all that follows -->
<script src="../shared/jquery/jquery.js" type="text/javascript"></script>
<!-- home-grown add-ons to the jQuery library, feel free to add your own -->
<script src="../shared/jsLib.js" type="text/javascript"></script>
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

<div id="sidebar">
	<?php include(REL(__FILE__, "../shared/help_nav.php")); ?>
</div>

<div id="content">

