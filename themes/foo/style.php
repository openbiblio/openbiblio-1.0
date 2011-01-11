<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	$fname = "";
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$fname = $_SERVER['SCRIPT_FILENAME'];
	} else if (isset($_SERVER['PATH_TRANSLATED'])) {
		$fname = $_SERVER['PATH_TRANSLATED'];
	}
	$fname = str_replace('\\\\', '\\', $fname);
	if ($fname == __FILE__) {
		$cache = 'private_no_expire';
		require_once("../shared/common.php");
		header('Content-type: text/css');
		
		require_once(REL(__FILE__, '../../model/Themes.php'));
		$themes = new Themes;
		if (isset($_GET['themeid'])) {
			$themeid = $_GET['themeid'];
		} else {
			$themeid = Settings::get('themeid');
		}
		$theme = $themes->getOne($themeid);
	} else {
		if (!isset($theme) or !is_array($theme)) {
			Fatal::internalError('CSS included without setting custom theme');
		}
	}
	
?>
/*********************************************************
 *  Body Style
 *********************************************************/
body {
	background-color: <?php echo $theme['primary_bg'];?>;
	margin: 0;
	padding: 0;
}

form.not_block { margin: 0; padding: 0 }

h1.staff_head {
	text-align: right;
	font-size: 16px;
	border-bottom-style: solid;
	border-bottom-color: <?php echo $theme['border_color'] ?>;
	border-bottom-width: <?php echo $theme['border_width'] ?>px;
	margin: 0;
	padding-right: 4px;
	color: <?php echo $theme['title_font_color'] ?>;
	font-weight: bold;
}

.help_head {
	background: <?php echo $theme['title_bg'] ?>;
	color: <?php echo $theme['title_font_color'] ?>;
	border-bottom-style: solid;
	border-bottom-color: <?php echo $theme['border_color'] ?>;
	border-bottom-width: <?php echo $theme['border_width'] ?>px;
}
.help_head a {
	font-size: smaller;
	color: <?php echo $theme['title_font_color'] ?>;
}

#login  {
	font-size: 12px;
	font-weight: bold;
	font-family: sans-serif;
	line-height: 1.75;
	text-align: right;
	clear: both;
	margin: 0;
	padding: 2px;
	background: <?php echo $theme['alt1_bg'];?>;
	color: <?php echo $theme['alt1_font_color'];?>;
	border-top: solid black 1px;
}
#login form { margin: 0 }
#login input { border: solid black 1px; font-weight: normal }

/* Using list-style-image because IE has an odd circle list-style-type. */
ul.nav_main { margin-left: 0; padding-left: 0; list-style-type: none; }
ul.nav_main li { margin-bottom: .5em; font-weight: bold; white-space: nowrap; }
ul.nav_main > li.nav_selected:before { white-space: pre-wrap; content: "\bb  " }
ul.nav_sub { margin-left: 0; padding-left: 2em; list-style-type: circle; list-style-image: url("../../images/bulleto.png"); }
ul.nav_sub ul.nav_sub { padding-left: 1em }
ul.nav_sub li { margin-bottom: 0; font-weight: normal; }
ul.nav_sub li.nav_selected { font-weight: bold; list-style-type: disc; list-style-image: url("../../images/bulletc.png"); }

li.report_category { margin-bottom: 1em }

table#main {
	clear: both;
	border-collapse: separate;
	border-spacing: 0;
	margin: 0;
	padding: 0;
	width: 100%;
}
table#staff_main {
	clear: both;
	border-top: solid 15px <?php echo $theme['alt1_bg'];?>;
	border-collapse: separate;
	border-spacing: 0;
	margin: 0;
	padding: 0;
	width: 100%;
}
td#sidebar {
	vertical-align: top;
	background: <?php echo $theme['alt1_bg'];?>;
	color: <?php echo $theme['alt1_font_color'];?>;
	font-size: <?php echo $theme['alt1_font_size'];?>px;
	font-family: <?php echo $theme['alt1_font_face'];?>;
	border-right: solid 1px <?php echo $theme['border_color'];?>;
	width: 160px;
	margin: 0;
	padding: 5px;
	padding-left: 10px;
}
td#help_sidebar {
	vertical-align: top;
	background: <?php echo $theme['alt1_bg'];?>;
	color: <?php echo $theme['alt1_font_color'];?>;
	font-size: <?php echo $theme['alt1_font_size'];?>px;
	font-family: <?php echo $theme['alt1_font_face'];?>;
	border-right: solid 1px <?php echo $theme['border_color'];?>;
	margin: 0;
	padding: 5px;
	padding-left: 10px;
}
td#content {
	vertical-align: top;
	border-top: solid 1px <?php echo $theme['border_color'];?>;
	margin: 0;
	padding: 10px;
	background: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
}
div#footer {
	clear: both;
	margin-top: 30px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	font-size: 10px;
	color: <?php echo $theme['primary_font_color'];?>;
	text-align: center;
}


/*********************************************************
 *  Font Styles
 *********************************************************/
font.primary {
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
}
font.alt1 {
	color: <?php echo $theme['alt1_font_color'];?>;
	font-size: <?php echo $theme['alt1_font_size'];?>px;
	font-family: <?php echo $theme['alt1_font_face'];?>;
}
font.alt1tab {
	color: <?php echo $theme['alt1_font_color'];?>;
	font-size: <?php echo $theme['alt2_font_size'];?>px;
	font-family: <?php echo $theme['alt2_font_face'];?>;
<?php if ($theme['alt2_font_bold'] == 'Y') { ?>
	font-weight: bold;
<?php } else { ?>
	font-weight: normal;
<?php } ?>
}
font.alt2 {
	color: <?php echo $theme['alt2_font_color'];?>;
	font-size: <?php echo $theme['alt2_font_size'];?>px;
	font-family: <?php echo $theme['alt2_font_face'];?>;
<?php if ($theme['alt2_font_bold'] == 'Y') { ?>
	font-weight: bold;
<?php } else { ?>
	font-weight: normal;
<?php } ?>
}
.error {
	color: <?php echo $theme['primary_error_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	font-weight: bold;
}
font.small {
	font-size: 14px;
	font-family: <?php echo $theme['primary_font_face'];?>;
}
h1 {
	font-size: 16px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	font-weight: normal;
}

/*********************************************************
 *  Link Styles
 *********************************************************/
a:link {
	color: <?php echo $theme['primary_link_color'];?>;
}
a:visited {
	color: <?php echo $theme['primary_link_color'];?>;
}
a.primary:link {
	color: <?php echo $theme['primary_link_color'];?>;
}
a.primary:visited {
	color: <?php echo $theme['primary_link_color'];?>;
}
a.alt1:link {
	color: <?php echo $theme['alt1_link_color'];?>;
}
a.alt1:visited {
	color: <?php echo $theme['alt1_link_color'];?>;
}
a.alt2:link {
	color: <?php echo $theme['alt2_link_color'];?>;
}
a.alt2:visited {
	color: <?php echo $theme['alt2_link_color'];?>;
}
a.tab:link {
	color: <?php echo $theme['alt2_link_color'];?>;
	font-size: <?php echo $theme['alt2_font_size'];?>px;
	font-family: <?php echo $theme['alt2_font_face'];?>;
<?php if ($theme['alt2_font_bold'] == 'Y') { ?>
	font-weight: bold;
<?php } else { ?>
	font-weight: normal;
<?php } ?>
	text-decoration: none
}
a.tab:visited {
	color: <?php echo $theme['alt2_link_color'];?>;
	font-size: <?php echo $theme['alt2_font_size'];?>px;
	font-family: <?php echo $theme['alt2_font_face'];?>;
<?php if ($theme['alt2_font_bold'] == 'Y') { ?>
	font-weight: bold;
<?php } else { ?>
	font-weight: normal;
<?php } ?>
	text-decoration: none
}
a.tab:hover {text-decoration: underline}

/*********************************************************
 *  Table Styles
 *********************************************************/
table.primary {
	border-collapse: collapse;
}
table.border {
	border-style: solid;
	border-color: <?php echo $theme['border_color'];?>;
	border-width: <?php echo $theme['border_width'];?>px
}
table.results {
	width: 100%;
	border-collapse: collapse;
}
table.resultshead {
	width: 100%;
	border-collapse: separate;
	border-top: solid <?php echo $theme['alt2_bg'];?> 3px;
	border-bottom: solid <?php echo $theme['alt2_bg'];?> 3px;
	clear: both;
}
table.resultshead th {
	text-align: left;
	color: <?php echo $theme['primary_font_color'];?>;
	border: none;
	background: <?php echo $theme['primary_bg'];?>;
	font-size: 16px;
	font-weight: bold;
	vertical-align: middle;
	padding: 2px;
}
table.resultshead td {
	text-align: right;
}
table.results td.primary { border-top: none; }

table.buttons {
	margin: 0 0 0 auto;
	padding: 0;
	border-collapse: separate;
	background: white;
}
table.buttons td {
	background-color: <?php echo $theme['alt2_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['alt2_bg'];?>;
	border-style: outset;
	border-width: 1px;
	/* End hiding */
	padding: 4px;
	font-weight: bold;
	font-size: 12px;
	text-align: center;
	vertical-align: middle;
}
table.buttons input {
	border: none;
	color: <?php echo $theme['alt2_font_color'];?>;
	background: <?php echo $theme['alt2_bg'];?>;
	padding: 0;
	margin: 0;
	font-weight: bold;
	white-space: normal;
}
table.buttons input:hover { text-decoration: underline; }
table.buttons a {
	color: <?php echo $theme['alt2_font_color'];?>;
	text-decoration: none;
}
table.buttons a:hover { text-decoration: underline; }
table.buttons a:visited { color: <?php echo $theme['alt2_font_color'];?>; }

table.table_display { margin-bottom: 1em }
table.table_display th.title {
	font-weight: bold;
	font-size: 18px;
	border: none;
	border-bottom: solid #006500 2px;
}
table.table_display td.indent {
	width: 10px;
	background: white;
}
table.table_display tr.headings th {
	vertical-align: middle;
	text-align: left;
	font-weight: bold;
	text-decoration: underline;
	padding-right: 20px
	border: none;
}
table.table_display tr.odd { background: #eeeeee }
table.table_display tr.even { background: white }
table.table_display td {
	padding-right: 20px;
}

table.info_display { margin-bottom: 1em }
table.info_display th.title {
	text-align: left;
	font-weight: bold;
	font-size: 18px;
	border: none;
}
table.info_display th { text-align: right; vertical-align: top }
table.info_display td.header {
	border-bottom: solid #006500 2px;
}

table.form { margin-bottom: 1em }
table.form th.title {
	text-align: left;
	font-weight: bold;
	font-size: 18px;
	border: none;
	border-bottom: solid #006500 2px;
}
table.form th { text-align: right; vertical-align: top }
table.form td.error { font-weight: bold; color: red }

table.biblio_view {
	margin-top: 8px;
	margin-bottom: 8px;
}
table.biblio_view td {
	padding: 1px 4px;
}
table.biblio_view td.name {
	white-space: nowrap;
	text-align: right;
	vertical-align: top;
	font-weight: bold;
}
table.biblio_view td.value {
	text-align: left;
	vertical-align: top;
}
table.primary th {
	background-color: <?php echo $theme['alt2_bg'];?>;
	color: <?php echo $theme['alt2_font_color'];?>;
	font-size: <?php echo $theme['alt2_font_size'];?>px;
	font-family: <?php echo $theme['alt2_font_face'];?>;
	padding: <?php echo $theme['table_padding'];?>;
	border-style: solid;
<?php if ($theme['alt2_font_bold'] == 'Y') { ?>
	font-weight: bold;
<?php } else { ?>
	font-weight: normal;
<?php } ?>
	border-color: <?php echo $theme['border_color'];?>;
	border-width: <?php echo $theme['border_width'];?>px;
	height: 1
}
th.rpt {
	background-color: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo ($theme['primary_font_size'] - 2);?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	font-weight: bold;
	padding: <?php echo $theme['table_padding'];?>;
	border-style: solid;
	border-color: <?php echo $theme['border_color'];?>;
	border-width: 1;
	text-align: left;
	vertical-align: bottom;
}
td.primary {
	background-color: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	padding: <?php echo $theme['table_padding'];?>;
	border-style: solid;
	border-color: <?php echo $theme['border_color'];?>;
	border-width: <?php echo $theme['border_width'];?>px
}
tr.subhead td {
	background-color: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size']-4;?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	font-weight: bold;
	padding: 1px;
	border-style: solid;
	border-color: <?php echo $theme['border_color'];?>;
	border-width: <?php echo $theme['border_width'];?>px;
	text-align: center;
	vertical-align: middle;
}
td.rpt {
	background-color: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo ($theme['primary_font_size'] - 2);?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	padding: <?php echo $theme['table_padding'];?>;
	border-top-style: none;
	border-bottom-style: none;
	border-left-style: solid;
	border-left-color: <?php echo $theme['border_color'];?>;
	border-left-width: 1;
	border-right-style: solid;
	border-right-color: <?php echo $theme['border_color'];?>;
	border-right-width: 1;
	text-align: left;
	vertical-align: top;
}
td.primaryNoWrap {
	background-color: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	padding: <?php echo $theme['table_padding'];?>;
	border-style: solid;
	border-color: <?php echo $theme['border_color'];?>;
	border-width: <?php echo $theme['border_width'];?>px;
	white-space: nowrap
}

.title {
	background-color: <?php echo $theme['title_bg'];?>;
	color: <?php echo $theme['title_font_color'];?>;
	padding: <?php echo $theme['table_padding'];?>;
	margin: 0;
	text-align: <?php echo $theme['title_align'];?>;
}
.title .hours { float: left; margin: 0 }
.title dt { font-weight: bold }
.title dd { margin: 0 15px }
.title h1 {
	margin: 0;
	text-align: center;
	font-size: <?php echo $theme['title_font_size'];?>px;
	font-family: <?php echo $theme['title_font_face'];?>;
<?php if ($theme['title_font_bold'] == 'Y') { ?>
	font-weight: bold;
<?php } else { ?>
	font-weight: normal;
<?php } ?>
}
hr#end_title {
	clear: both;
	height: 1px;
	width: 100%;
	background: black;
	border: none;
	padding: 0;
	margin: 0;
}

td.alt1 {
	background-color: <?php echo $theme['alt1_bg'];?>;
	color: <?php echo $theme['alt1_font_color'];?>;
	font-size: <?php echo $theme['alt1_font_size'];?>px;
	font-family: <?php echo $theme['alt1_font_face'];?>;
	padding: <?php echo $theme['table_padding'];?>;
	border-style: solid;
	border-color: <?php echo $theme['border_color'];?>;
	border-width: <?php echo $theme['border_width'];?>px
}
td.noborder {
	background-color: <?php echo $theme['primary_bg'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-family: <?php echo $theme['primary_font_face'];?>;
	padding: <?php echo $theme['table_padding'];?>;
}
/*********************************************************
 *  Form Styles
 *********************************************************/
input { vertical-align: middle }
input.button {
	background-color: <?php echo $theme['alt2_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['alt2_bg'];?> ! important;
	border-style: outset ! important;
	border-width: 1px ! important;
	/* End hiding */
	/* padding: <?php echo $theme['table_padding'];?>; */
	font-family: <?php echo $theme['primary_font_face'];?>;
	font-size: 12px;
	padding: 0px 6px;
	color: <?php echo $theme['alt2_font_color'];?>;
	text-decoration: none;
}
input.navbutton {
	background-color: <?php echo $theme['alt2_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['alt2_bg'];?>;
	border-style: outset;
	border-width: 1px;
	/* End hiding */
	/* padding: <?php echo $theme['table_padding'];?>; */
	font-family: <?php echo $theme['primary_font_face'];?>;
	color: <?php echo $theme['alt2_font_color'];?>;
	text-decoration: none;
}
input.button:hover, input.navbutton:hover { text-decoration: underline }
input {
	background-color: <?php echo $theme['primary_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['primary_bg'];?>;
	/* End hiding */
	padding: 0px;
	scrollbar-base-color: <?php echo $theme['alt1_bg'];?>;
	font-family: <?php echo $theme['primary_font_face'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
}
textarea {
	background-color: <?php echo $theme['primary_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['primary_bg'];?>;
	/* End hiding */
	padding: 0px;
	scrollbar-base-color: <?php echo $theme['alt1_bg'];?>;
	font-family: <?php echo $theme['primary_font_face'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
	font-size: <?php echo $theme['primary_font_size'];?>px;
	font-weight: normal;
	width: 100%;
}
select {
	background-color: <?php echo $theme['primary_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['primary_bg'];?>;
	/* End hiding */
	padding: 0px;
	scrollbar-base-color: <?php echo $theme['alt1_bg'];?>;
	font-family: <?php echo $theme['primary_font_face'];?>;
	color: <?php echo $theme['primary_font_color'];?>;
}

input[disabled] {
	background-color: <?php echo $theme['alt1_bg'];?>;
	color: <?php echo $theme['alt1_font_color'];?>;
}

a.small_button {
	background-color: <?php echo $theme['alt2_bg'];?>;
	/* Hide from IE5/Mac \*/
	border-color: <?php echo $theme['alt2_bg'];?>;
	border-style: outset;
	border-width: 1px;
	/* End hiding */
	/* padding: <?php echo $theme['table_padding'];?>; */
	font-family: <?php echo $theme['primary_font_face'];?>;
	color: <?php echo $theme['alt2_font_color'];?>;
	font-size:12px;
	padding: 1px 6px;
	text-decoration: none;
}
a.small_button:hover { text-decoration: underline; }
a.small_button:visited { color: <?php echo $theme['alt2_font_color'];?>; }

div.current_mbr {
	float: right;
	border: solid 3px <?php echo $theme['alt2_bg'];?>;
	background: <?php echo $theme['alt1_bg'];?>;
	padding: 4px;
	margin: 0 0 4px 4px;
}
div.current_mbr a { font-weight: bold }

div.biblio_images { clear: both; float: right; margin: 4px }
div.biblio_image { text-align: center; margin-bottom: 20px }

/* Booking Calendars */
table.calendar {
	font-family: serif;
	font-size: 12px;
	margin: 0 auto; padding: 0;
	text-align: center; /* for IE */
	background: white;
	color: black;
	width: 95%;
}
table.calendar td { font-size: 14px }
table.calendar td.calendarHeader { font-size: 12px; font-weight: bold; }
table.calendar a.datelink { color: inherit; text-decoration: inherit }
.calendarDayNames { border-bottom: solid black 1px; }
table.calendarMonth { margin: 5px auto; border-collapse: collapse; }
.calendarToday { border: solid black 1px; }
.calendarOpen, .calendarClosed, .calendarUnknown {
	text-align: center;
	vertical-align: middle;
	padding: 2px;
}
.calendarClosed {
	color: #c0c0c0;
}
.bookedSome { font-weight: bold; }
.bookedMany { color: #006500; font-weight: bold; }
.bookedAll { background: #72b05f; }

strong.new_item { color: #cc0000; font-style: italic; font-weight: bold }

/* The border property here works around a bug in Firebird 0.6, 0.7
 * and maybe others.  Without it, the previous cell has no bottom
 * border.
 */
td#searchsubmit { border: solid white 1px; padding-left: 12px }
