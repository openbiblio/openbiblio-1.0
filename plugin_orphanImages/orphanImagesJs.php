<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document - OrphanImagesJs.php
?>
<script language="JavaScript">
"use strict";

var orim = {
	init: function () {
		//console.log('initializing orim');
		orim.url = '../plugin_orphanImages/orphanImagesSrvr.php';
		
		orim.resetForms();
		orim.initWidgets();

		$('#orfnChkBtn').bind('click',null,orim.findOrfans);
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
		$('#orfnChkBtn').focus();
	    $('#rsltsArea').hide();
	    $('#msgDiv').hide();
	},
	
	//------------------------------
	setDetails: function () {
	},
	setVerbose: function () {
	},
	
	//------------------------------
	findOrfans: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var detl = $('#detl :checked').val(),
			verb = $('#verb :checked').val();
		$.post(orim.url, {
						  'mode':'ck4Orfans',
						  'detl':detl,
						  'verb':verb,
						 },
						function (response) {
			$('#rslts').html(response);
		});
	},
};

$(document).ready(orim.init);
</script>
