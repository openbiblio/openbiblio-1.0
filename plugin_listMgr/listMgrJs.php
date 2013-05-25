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
		pdl.url = 'listSrvr.php';
		
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
	fetchSiteList: function () {
	  $.get(bs.url,{mode:'getSiteList'}, function(data){
			$('#copy_site').html(data);
			// Add all for search sites
			data = '<option value="all"  selected="selected">All</option>' + data;
			$('#srchSites').html(data);

			// now ready to begin a search
			bs.doAltStart();
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
		pdl.fetchCollectionList();
		pdl.fetchMediaList();
		$('#pullDowns').append('<br />');
		pdl.fetchStateList();
		$('#rsltsArea').show();
	},
};

$(document).ready(pdl.init);
</script>
