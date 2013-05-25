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
		$('#listChkBtn').focus();
	  $('#rsltsArea').hide();
	  $('#msgDiv').hide();
	},
	
	//------------------------------
	fetchCalendarList: function () {
	  $.getJSON(pdl.url,{mode:'getCalendarList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#calendar_cd').html(html);
		});
	},
	fetchCollectionList: function () {
	  $.getJSON(pdl.url,{mode:'getCollectionList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#collection_cd').html(html);
		});
	},
	fetchMediaList: function () {
	  $.getJSON(pdl.url,{mode:'getMediaList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#material_cd').html(html);
		});
	},
	fetchSiteList: function () {
	  $.getJSON(pdl.url,{mode:'getSiteList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#site_cd').html(html);
		});
	},
	fetchStateList: function () {
	  $.getJSON(pdl.url,{mode:'getStateList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#state_cd').html(html);
		});
	},

	//------------------------------
	showLists: function () {
		$('#pulldowns').html(' ');
		pdl.fetchCalendarList();
		pdl.fetchCollectionList();
		pdl.fetchMediaList();
		pdl.fetchSiteList();
		pdl.fetchStateList();
		$('#rsltsArea').show();
	},
};

$(document).ready(pdl.init);
</script>
