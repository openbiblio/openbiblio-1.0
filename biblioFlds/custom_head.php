<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * JavaScript & CSS Material contained here is unigue to the content of
 * this directory folder. It is incorporated into loading programs via the
 * '../shared/header_top file' and '../shared/header_top file'.
 *
 * Provisions to include material unique to a particular portion is
 * at the end of thie file.
 */
?>

<!-- custom header material for biblioFlds plugin -->
<script language="JavaScript" >
//console.log('setting license data');

//license data object
biblioFlds = {
	version:		'1.0.0',
	author:			'Fred LaPlante',
	date:       '17 June 2009',
	license:		'http://www.gnu.org/licenses/lgpl.txt Lesser GNU Public License',
	copyright:	'2009 All Rights Reserved.',
	email:			'flaplante at flos-inc dot com',
	comment:    'Biblio Fields Maintenance add-on/plugin for OpenBiblio v1.0+'
};
</script>

<!-- custom styling -->
<style type="text/css">
#content .title {
	margin-top:5px; margin-bottom:5px;
	}
#typeChoice fieldset {
	margin: 0; padding: 5px;
	/*width: 500px;*/
	text-align: center;
	}
h4 {
	margin: 0; padding: 0; text-align: left; color: blue;
	}
h5 {
	margin: 0; padding: 0; text-align: left; color: blue;
	}
h5#updateMsg {
	color: red;
	}
label {
	font-weight: bold;
	}
th.colHead {
  text-align: top; white-space: nowrap;
	}
td.lblFld {
  white-space: nowrap;
	}
td.inptFld {
  vertical-align: top;
	}
td.btnFld {
  text-align: center;
	}
.editBtn {
	margin: 0; padding: 0; height: 1.5em; text-align:center;
	}
	
/* for draggable configuration screen */
/*
#configDiv fieldset.configArea {
	width: 300px; float: left;
	}
*/
div#configDiv{
	width: 750px;
	}
#configDiv fieldset#existingSpace {
	display: inline;
	/*width: 255px;*/
	/*float: left;*/
	}
#configDiv fieldset#potentialSpace {
	display: inline;
  /*float: right;*/
	/*width: 350px;*/
	}
#configDiv fieldset#potentialSpace select {
	margin-bottom: 10px;
	}
#configDiv ul#existing,ul#potential {
	list-style-type: none;
	margin: 0; padding: 0; margin-right: 5px;
	}
#configDiv ul#existing li,ul#potential li {
	margin-bottom: 2px;
	width: 270px;
	font-size: 1.2em;
	}
#configDiv ul#existing li {
	color: black; background: #FFFF99;
	}
#configDiv ul#potential li {
	color: black; background: #CCFFFF;
	}
</style>

<?php
/*
$meName = $_SERVER[PHP_SELF];
$meParts = explode('/',$meName);
$fn = $meParts[3];

switch ($fn) {
	case 'flds_form.php':
		require_once(REL(__FILE__, "flds_js.php"));
		break;
		
	default:
	  echo "custom_head.php is unable to load JS file for: $_SERVER[PHP_SELF] ?<br />";
}
*/
?>
