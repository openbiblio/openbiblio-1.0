<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
//	$types = $mattypes->getAllWithStats();
"use strict";

var thm = {
	<?php
		echo "crntTheme: '".Settings::get('themeid')."',\n";
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("List of Media Types")."',\n";
		echo "editHdr: '".T("Editing Media")."',\n";
	?>
	
	init: function () {
		thm.initWidgets();

		thm.url = 'adminSrvr.php';
		thm.selectForm = $('#selectForm');
		thm.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#chngBtn').on('click',null,thm.doChngTheme);
		$('#showForm .newBtn').on('click',null,thm.doNewTheme);
		$('#addBtn').on('click',null,thm.doAddTheme);
		$('#updtBtn').on('click',null,thm.doUpdateTheme);
		$('#cnclBtn').on('click',null,thm.resetForms);
		$('#deltBtn').on('click',null,thm.doDeleteTheme);

		thm.resetForms()
	  $('#msgDiv').hide();
		thm.fetchThemes();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	    $('#listHdr').html(thm.listHdr);
	    $('#mediaHdr').html(thm.editHdr);
		$('#editDiv').hide();
		$('#listDiv').show();
        $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		$('#msgDiv').hide(10000);
		thm.resetForms();
		thm.fetchThemes();
	},
	
	//------------------------------
	fetchThemes: function () {
	  $.post(thm.url,{ 'cat':'themes', 'mode':'getAllThemes' }, function(dataAray){
	        thm.json = dataAray;
			var html = '', opts = '';
			// first construct theme dropdown list
			for (var obj in dataAray) {
				var item = dataAray[obj];
				opts += '<option value="'+item['themeid']+'">';
				opts += item["theme_name"];
				opts += '</option>';
			}
			$('#themeList').html(opts).val(thm.crntTheme);
			
			for (var obj in dataAray) {
				var item = dataAray[obj];
	    	html += '<tr>\n';
    		html += '	<td valign="top">\n';
				html += '		<input type="button" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
				html += '		<input type="hidden" id="code" value="'+item['themeid']+'"  />\n';
				html += '		<input type="button" class="copyBtn" value="'+<?php echo "'".T("copy")."'"; ?>+'" />\n';
	    	html += '	</td>\n';
    		html += '	<td valign="top">'+item['theme_name']+'</td>\n';
    		if (item['themeid'] == thm.crntTheme) {
    			html += '	<td class="inuseFld" valign="top">'+<?php echo "'".T("in use")."'"; ?>+'</td>\n';
    		} else {
    			html += '	<td class="inuseFld" valign="top">&nbsp</td>\n';
    		}
	    	html += '</tr>\n';
			}
			$('#showList tBody').html(html);

			$('.editBtn').on('click',null,thm.doEdit);
			$('.copyBtn').on('click',null,thm.doCopy);
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
		}, 'json');
	},

	doChngTheme: function () {
		var newTheme = $('#themeList').val();
		$.post(thm.url, { 'cat':'themes', 'mode':'setCrntTheme', 'themeid':newTheme }, function (response) {		
			$('.inuseFld').html('&nbsp;');
			$('#showForm #code[value='+newTheme+']').parent().parent().find('td:eq(2)')
					.html(<?php echo "'".T("in use")."'"; ?>);
			thm.crntTheme = newTheme;

			$('#msgArea').html(response);
			$('#msgDiv').show();
			 thm.doBackToList();
		});
		return false;
	},
	
	doEdit: function (e) {
	    var themeid = $(e.target).next().val();
		//console.log('you wish to edit code: '+themeid);
		for (var n in thm.json) {
		  if (thm.json[n]['themeid'] == themeid) {
				thm.showTheme(thm.json[n]);
	  		$('#addBtn').hide();
	  		$('#updtBtn').show();
	  		$('#deltBtn').show();
			}
		}
		return false;
	},
	doCopy: function (e) {
	    var themeid = $(e.target).prev().val();
		//console.log('you wish to copy theme: '+themeid);
		for (var n in thm.json) {
		  if (thm.json[n]['themeid'] == themeid) {
				thm.showTheme(thm.json[n]);
				$('#theme_name').val('');
				$('#themeid').val('');
	  		$('#addBtn').show();
	  		$('#cnclBtn').show();
	  		$('#updtBtn').hide();
	  		$('#deltBtn').hide();
			}
		}
		return false;
	},
	
	showTheme: function (th) {
		//console.log('showing : '+media['description']);
	    $('#themeHdr').html(thm.themeHdr);
	    $('#theme_name').focus();
	    if (th['themeid'] != thm.crntTheme)
			$('#deltBtn').show(); 
		else 
			$('#deltBtn').hide();

		$('#theme_name').val(th['theme_name']);
		$('#themeid').val(th['themeid']);
		$('#border_color').val(th['border_color']);
		$('#primary_error_color').val(th['primary_error_color']);
		$('#border_width').val(th['border_width']);
		$('#table_padding').val(th['table_padding']);

		$('#title_bg').val(th['title_bg']);
		$('#primary_bg').val(th['primary_bg']);
		$('#alt1_bg').val(th['alt1_bg']);
		$('#alt2_bg').val(th['alt2_bg']);

		$('#title_font_face').val(th['title_font_face']);
		$('#primary_font_face').val(th['primary_font_face']);
		$('#alt1_font_face').val(th['alt1_font_face']);
		$('#alt2_font_face').val(th['alt2_font_face']);

		$('#title_font_size').val(th['title_font_size']);
		$('#title_font_bold').val(th['title_font_bold']);
		$('#primary_font_size').val(th['primary_font_size']);
		$('#alt1_font_size').val(th['alt1_font_size']);
		$('#alt2_font_size').val(th['alt2_font_size']);
		$('#alt2_font_bold').val(th['alt2_font_bold']);

		$('#title_font_color').val(th['title_font_color']);
		$('#primary_font_color').val(th['primary_font_color']);
		$('#alt1_font_color').val(th['alt1_font_color']);
		$('#alt2_font_color').val(th['alt2_font_color']);

		$('#primary_link_color').val(th['primary_link_color']);
		$('#alt1_link_color').val(th['alt1_link_color']);
		$('#alt2_link_color').val(th['alt2_link_color']);
		$('#title_align').val(th['title_align']);

		$('table tbody.striped tr:odd td').addClass('altBG');
		$('table tbody.striped tr:even td').addClass('altBG2');

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewTheme: function (e) {
	  document.forms['editForm'].reset();
	  $('#mediaHdr').html(thm.newHdr);
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('theme_name').focus();
		return false;
	},
	
	doAddTheme: function () {
		$('#mode').val('addNewTheme');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(thm.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('Added!');
				$('#msgDiv').show();
			  thm.doBackToList();
			}
		});
		return false;
	},

	doUpdateTheme: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateTheme');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(thm.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('response');
				$('#msgDiv').show();
			  thm.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteTheme: function (e) {
		var msg = thm.delConfirmMsg+'\n>>> '+$('#theme_name').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	'cat':'themes',
										'mode':'d-3-L-3-tTheme', 
										'themeid':$('#themeid').val(), 
										'theme_name':$('#theme_name').val(), 
									};
	  	$.post(thm.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html('response');
					$('#msgDiv').show();
			  	thm.doBackToList();
				}
			});
		}
		return false;
	},
};

$(document).ready(thm.init);
</script>
