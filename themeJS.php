<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
//	$types = $mattypes->getAllWithStats();

thm = {
	<?php
		echo "crntTheme: '".Settings::get('themeid')."',\n";
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("List of Media Types")."',\n";
		echo "editHdr: '".T("Editing Media")."',\n";
	?>
	
	init: function () {
console.log('in themeJs.php');	
		thm.initWidgets();

		thm.url = 'adminSrvr.php';
		thm.selectForm = $('#selectForm');
		thm.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').bind('click',null,thm.doNewMedia);
		$('#addBtn').bind('click',null,thm.doAddMedia);
		$('#updtBtn').bind('click',null,thm.doUpdateMedia);
		$('#cnclBtn').bind('click',null,thm.resetForms);
		$('#deltBtn').bind('click',null,thm.doDeleteMedia);

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
console.log('fetching');	
	  $.getJSON(thm.url,{mode:'getAllThemes'}, function(dataAray){
console.log(dataAray);
	    thm.json = dataAray;
			var html = '', opts = '';
			// first construct theme dropdown list
console.log(dataAray);
			for (obj in dataAray) {
				var item = dataAray[obj];
console.log('theme='+	item["theme_name"]);
				opts += '<option value="'+item['themeid']+'">';
				opts += item["theme_name"];
				opts += '</option>';
			}
console.log(opts);			
			$('#themeList').html(opts);
/*			
			for (obj in dataAray) {
				var item = dataAray[obj];
	    	html += '<tr>\n';
    		html += '	<td valign="top">\n';
				html += '		<input type="button" class="editBtn" value="edit" />\n';
				html += '		<input type="hidden" value="'+item['code']+'"  />\n';
	    	html += '	</td>\n';
    		html += '	<td valign="top">'+item['description']+'</td>\n';
    		html += '	<td valign="top" class="number">'+item['adult_checkout_limit']+'</td>\n';
    		html += '	<td valign="top" class="number">'+item['juvenile_checkout_limit']+'</td>\n';
				html += '	<td valign="top">'
							 +'		<img src="../images/'+item['image_file']+'" width="20" height="20" align="middle">'
							 + 		item['image_file'] + '</td>\n';							 
				html += ' <td valign="top" class="center">'+item['default_flg']+'</td>\n';
				html += '	<td valign="top"  class="number" >' + item['count'] + '</td>\n';
	    	html += '</tr>\n';
			}
			$('#showList tBody').html(html);

			$('.editBtn').bind('click',null,thm.doEdit);
*/
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
		});
	},

	doEdit: function (e) {
	  var code = $(e.target).next().val();
		//console.log('you wish to edit code: '+code);
		for (n in thm.json) {
		  if (thm.json[n]['code'] == code) {
				thm.showThemes(thm.json[n]);
			}
		}
		return false;
	},
	/*
	doMarcFlds: function (e) {
	  var code = $(e.target).prior().val();
		//console.log('you wish to edit code: '+code);
		for (n in thm.json) {
		  if (thm.json[n]['code'] == code) {
				thm.showThemes(thm.json[n]);
			}
		}
		return false;
	},
	*/
	
	showThemes: function (media) {
		//console.log('showing : '+media['description']);
	  $('#mediaHdr').html(thm.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#deltBtn').show();
	  $('#description').focus();
	  if (media['count'] < 1) 
			$('#deltBtn').show(); 
		else 
			$('#deltBtn').hide();

		$('#description').val(media['description']);
		$('#code').val(media['code']);
		$('#default_flg').val(media['default_flg']);
		$('#adult_checkout_limit').val(media['adult_checkout_limit']);
		$('#juvenile_checkout_limit').val(media['juvenile_checkout_limit']);
		$('#image_file').val(media['image_file']);

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewThemes: function (e) {
	  document.forms['editForm'].reset();
	  $('#mediaHdr').html(thm.newHdr);
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('description').focus();
		return false;
	},
	
	doAddThemes: function () {
		$('#mode').val('addNewThemes');
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

	doUpdateThemes: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateThemes');
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
	
	doDeleteThemes: function (e) {
		var msg = thm.delConfirmMsg+'\n>>> '+$('#description').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	mode:'d-3-L-3-tThemes', code:$('#code').val(), description:$('#description').val() };
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
