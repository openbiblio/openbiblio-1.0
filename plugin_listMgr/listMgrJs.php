<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
?>
<script language="JavaScript">
"use strict";

var pdl = {
	init: function () {
		//console.log('initializing orf');	
		pdl.url = '../shared/listSrvr.php';
		
		pdl.resetForms();
		pdl.initWidgets();

		$('#showListsBtn').bind('click',null,pdl.showLists);
	},

	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
		$('#entrySect').show();
	    $('#rsltsArea').hide();
	    $('#msgDiv').hide();
        $('#showListsBtn').focus();
	},

	//------------------------------
	showLists: function () {
		$('#pulldowns').html(' ');
		pdl.fetchCalendarList();
		pdl.fetchCollectionList();
		pdl.fetchMediaList();
		pdl.fetchMbrTypList();
		pdl.fetchSiteList();
		pdl.fetchStateList();
		pdl.fetchValidationList();
		pdl.fetchDueDateCalculatorList();
		//pdl.fetchInputTypes();

		$('#rsltsArea').show();
		$('#entrySect').hide();
	},

	//------------------------------
	fetchCalendarList: function () {
        list.getPullDownList('Calendar', $('#calendar_cd'));
	},
	fetchCollectionList: function () {
        list.getPullDownList('Collection', $('#collection_cd'));
	},
	fetchMediaList: function () {
        list.getPullDownList('Media', $('#media_cd'));
	},
	fetchMbrTypList: function () {
        list.getPullDownList('MbrTyp', $('#mbrTyp_cd'));
	},
	fetchStateList: function () {
        list.getPullDownList('State', $('#state_cd'));
	},
	fetchSiteList: function () {
        list.getSiteList($('#site_cd'));
	},
	fetchDueDateCalculatorList: function () {
	list.getDueDateCalculatorList($('#calc_cd'));
	},
	fetchValidationList: function () {
        list.getPullDownList('Validation', $('#validation_cd'));
	},
/*
	fetchInputTypes: function () {
	  $.post(pdl.url,{mode:'getInputTypes'}, function(data){
			var partsA = (data.replace(/'/g,"")).split('(');
			var partsB = partsA[1].split(')');
			var list = partsB[0].split(',');
			var html = '';
            for (var n in list) {
				html += '<option value="'+n+'" ';
                var dflt= data[n].default;
                if (dflt == 'Y') html += 'SELECTED ';
                html += '>'+data[n].description+'</option>';
			}
			$('#inptTyp_cd').html(html);
		}, 'json');
	},
*/

};

$(document).ready(pdl.init);
</script>
