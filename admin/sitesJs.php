<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
sit = {
	<?php
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("List of Sites")."',\n";
		echo "editHdr: '".T("Edit Site")."',\n";
		echo "newHdr: '".T("Add New Site")."',\n";
	?>
	
	init: function () {
		sit.initWidgets();

		sit.url = 'adminSrvr.php';
		sit.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').bind('click',null,sit.doNewSite);
		$('#addBtn').bind('click',null,sit.doAddSite);
		$('#updtBtn').bind('click',null,sit.doUpdateSite);
		$('#cnclBtn').bind('click',null,sit.resetForms);
		$('#deltBtn').bind('click',null,sit.doDeleteSite);

		sit.resetForms()
	  $('#msgDiv').hide();
		sit.fetchSites();
		sit.fetchStates();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#listHdr').html(sit.listHdr);
	  $('#siteHdr').html(sit.editHdr);
		$('#editDiv').hide();
		$('#listDiv').show();
    $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		$('#msgDiv').hide(10000);
		sit.resetForms();
		sit.fetchSites();
	},
	
	//------------------------------
	fetchSites: function () {
	  $.getJSON(sit.url,{ 'cat':'sites', 'mode':'getAllSites' }, function(data){
	    sit.siteJSON = data;
			var html = '';
			for (nsite in sit.siteJSON) {
				//console.log(data[nsite]);
	    	html += '<tr>\n';
    		html += '<td valign="top">\n';
				html += '	<input type="button" class="editBtn" value="edit" />\n';
				html += '	<input type="hidden" value="'+data[nsite]['siteid']+'"  />\n';
				html += '</td>\n';
    		html += '<td valign="top">'+data[nsite]['name']+'</td>\n';
    		html += '<td valign="top">'+data[nsite]['code']+'</td>\n';
    		//var str = ((data[nsite]['default_flg']=='Y')?'yes':'no');
    		//html += '<td valign="top">'+str+'</td>\n';
    		html += '<td valign="top">'+data[nsite]['city']+'</td>\n';
				html += '</tr>\n';
			}
			$('#showList tBody').html(html);
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
			$('.editBtn').bind('click',null,sit.doEdit);
		});
	},
	fetchStates: function () {
	  $.getJSON(sit.url,{ 'cat':'sites', 'mode':'getAllStates' }, function(data){
			var html = '';
			for (nstate in data) {
				//console.log(data[nstate]);
	    	html += '<option value="'+data[nstate]['code']+'"';
	    	if (data[nstate]['default_flg'] == 'Y') {
	    		html += ' selected';
				}
    		html += '">'+data[nstate]['description']+'</option>\n';
			}
			$('#state').html(html);
		});
	},

	doEdit: function (e) {
	  var siteid = $(e.target).next().val();
		//console.log('you wish to edit code: '+siteid);
		for (nsite in sit.siteJSON) {
		  if (sit.siteJSON[nsite]['siteid'] == siteid) {
				sit.showSite(sit.siteJSON[nsite]);
			}
		}
		return false;
	},
	
	showSite: function (site) {
		//console.log('showing : '+site['name']);
	  $('#editHdr').html(sit.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#deltBtn').show();
	  $('#description').focus();
	  if (site['count'] < 1) 
			$('#deltBtn').hide(); 
		else 
			$('#deltBtn').show();

		$('#siteid').val(site['siteid']);
		$('#name').val(site['name']);
		$('#code').val(site['code']);
		$('#calendar').val(site['calendar']);
		$('#address1').val(site['address1']);
		$('#address2').val(site['address2']);
		$('#city').val(site['city']);
		$('#state').val(site['state']);
		$('#zip').val(site['zip']);
		$('#phone').val(site['phone']);
		$('#fax').val(site['fax']);
		$('#email').val(site['email']);
		$('#delivery_note').val(site['delivery_note']);

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewSite: function (e) {
	  document.forms['editForm'].reset();
	  $('#editHdr').html(sit.newHdr);
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('name').focus();
		return false;
	},
	
	doAddSite: function () {
		$('#mode').val('addNewSite');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(sit.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			  sit.doBackToList();
			}
		});
		return false;
	},

	doUpdateSite: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateSite');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(sit.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html(respomse);
				$('#msgDiv').show();
			  sit.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteSite: function (e) {
		var msg = sit.delConfirmMsg+'\n>>> '+$('#name').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	'cat':'sites', 'mode':'d-3-L-3-tSite', siteid:$('#siteid').val() };
	  	$.post(sit.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html(response);
					$('#msgDiv').show();
			  	sit.doBackToList();
				}
			});
		}
		return false;
	},
};

$(document).ready(sit.init);
</script>
