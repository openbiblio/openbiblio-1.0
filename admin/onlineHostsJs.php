<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
hed = {
	
	init: function () {
		hed.initWidgets();

		hed.url = 'adminSrvr.php';
		hed.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').bind('click',null,hed.doNewHost);
		$('#hostForm').bind('submit',null,hed.doSubmits);
		$('#hostForm tFoot #cnclBtn').bind('click',null,hed.resetForms);

		hed.fetchHosts();
		hed.resetForms()
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');

	  $('#listHdr').html(hed.listHdr);
	  $('#hostHdr').html(hed.editHdr);
		$('#editDiv').hide();
	  $('#msgDiv').hide();
		$('#listDiv').show();
    $('#hostForm tFoot #cnclBtn').val('Cancel');
    
		$('#lookupVal').focus();
	},
	doBackToList: function () {
		hed.fetchHosts();
		hed.resetForms();
	},
	
	//------------------------------
	fetchHosts: function () {
	  $.getJSON(hed.url,{ 'cat':'hosts', 'mode':'getHosts' }, function(data){
	    hed.hostJSON = data;
			var html = '';
			for (nHost in hed.hostJSON) {
				//console.log(data[nHost]);
	    	html += '<tr>\n';
    		html += '<td>\n';
				html += '	<input type="button" value="edit" align="center" class="editBtn" />\n';
				html += '	<input type="hidden" value="'+hed.hostJSON[nHost]['id']+'" />\n';
				html += '</td>\n';
    		html += '<td>'+hed.hostJSON[nHost]['seq']+'</td>\n';
    		var str = ((hed.hostJSON[nHost]['active']=='y')?'yes':'no');
    		html += '<td valign="top" >'+str+'</td>\n';
    		html += '<td valign="top" >'+hed.hostJSON[nHost]['host']+'</td>\n';
    		html += '<td valign="top" >'+hed.hostJSON[nHost]['name']+'</td>\n';
    		html += '<td valign="top" >'+hed.hostJSON[nHost]['db']+'</td>\n';
    		if (hed.hostJSON[nHost]['user'] == null) hed.hostJSON[nHost]['user'] = '';
    		html += '<td valign="top" >'+hed.hostJSON[nHost]['user']+'</td>\n';
    		if (hed.hostJSON[nHost]['pw'] == null) hed.hostJSON[nHost]['pw'] = '';
    		html += '<td valign="top" >'+hed.hostJSON[nHost]['pw']+'</td>\n';
				html += '</tr>\n';
			}
			$('#showList tBody').html(html);
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
			$('.editBtn').bind('click',null,hed.doEdit);
		});
	},
	
	doNewHost: function (e) {
		//console.log('newHost');
	  $('#hostHdr').html(hed.newHdr);
	  document.forms['editForm'].reset();
	  $('#hostForm tfoot #deltBtn').hide();
	  $('#hostForm tfoot #updtBtn').hide();
	  $('#hostForm tfoot #addBtn').show();
	  $('#hostForm tbody #name').focus();
		$('#hostForm #id').val('');
		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doEdit: function (e) {
	  var theHostId = $(this).next().val();
		//console.log('you wish to edit host #'+theHostId);
		for (nHost in hed.hostJSON) {
		  if (hed.hostJSON[nHost]['id'] == theHostId) {
				hed.showHost(hed.hostJSON[nHost]);
			}
		}
	},
	
	doSubmits: function (e) {
		e.preventDefault();
		e.stopPropagation();
		var theId = $("#hostForm").find('input[type="submit"]:focus').attr('id');
		switch (theId) {
			case 'addBtn':	hed.doAddHost();	break;
			case 'updtBtn':	hed.doUpdateHost();	break;
			case 'deltBtn':	hed.doDeleteHost();	break;
		}
console.log('you clicked the '+theId);		
	},
	
	doAddHost: function () {
		$('#mode').val('addNewHost');
		var parms = $('#hostForm').serialize();
console.log(parms);
		$.post(hed.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
			  hed.doBackToList();
			}
		});
	},

	doUpdateHost: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateHost');
		var parms = $('#hostForm').serialize();
		//console.log(parms);
		$.post(hed.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html('<?php echo T('Updated');?>');
					$('#updateMsg').show();
				}
			  hed.doBackToList();
			}
		});
	},
	
	doDeleteHost: function (e) {
		var msg = hed.delConfirmMsg+'\n>>> '+$('#hostForm tbody #name').val()+' <<<';
	  if (confirm(msg)) {
	  	$.get(hed.url,
								{	'cat':'hosts',
									'mode':'d-3-L-3-tHost',
									'id':$('#hostForm tbody #id').val()
								},
								function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
//				else if (($.trim(response))== '') {
//				}
				else {
			  	hed.doBackToList();
				}
			});
		}
	},

	showHost: function (host) {
		//console.log('showing : '+host['name']);
	  $('#hostHdr').html(hed.editHdr);
	  $('#hostForm tfoot #addBtn').hide();
	  $('#hostForm tfoot #updtBtn').show();
	  $('#hostForm tbody #name').focus();

		$('#editTbl td #id').val(host['id']);
		$('#editTbl td #host').val(host['host']);
		$('#editTbl td #name').val(host['name']);
		$('#editTbl td #db').val(host['db']);
		$('#editTbl td #seq').val(host['seq']);
    $('#editTbl td #active').val([host['active']]);
		$('#editTbl td #user').val(host['user']);
		$('#editTbl td #pw').val(host['pw']);

		$('#listDiv').hide();
		$('#editDiv').show();
	}
};

$(document).ready(hed.init);
</script>
