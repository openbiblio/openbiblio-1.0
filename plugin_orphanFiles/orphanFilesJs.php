<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
?>
<script language="JavaScript">
"use strict";

var orf = {
	init: function () {
		//console.log('initializing orf');	
		orf.url = '../plugin_orphanFiles/orphanFilesSrvr.php';
		
		orf.resetForms();
		orf.initWidgets();

		$('#orfnChkBtn').bind('click',null,orf.findOrfans);
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
		$.post(orf.url, {
											'mode':'ck4Orfans',
											'detl':detl,
											'verb':verb,
										},
						function (response) {
			$('#rslts').html(response);
		});
	},
};

$(document).ready(orf.init);
</script>
