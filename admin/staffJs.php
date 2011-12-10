<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
//	$types = $mattypes->getAllWithStats();
"use strict";

var stf = {
	<?php
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("List of Staff Members")."',\n";
		echo "editHdr: '".T("Edit Staff Member")."',\n";
		echo "newHdr: '".T("New Staff Member")."',\n";
	?>
	
	init: function () {
		stf.initWidgets();

		stf.url = 'adminSrvr.php';
		stf.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').on('click',null,stf.doNewStaff);
		$('#editForm').on('submit',null,stf.doSubmits);
		$('#cnclBtn').on('click',null,stf.resetForms);
		$('#pwdChgForm').on('submit',null,stf.doSetStaffPwd);

		stf.resetForms()
	  $('#msgDiv').hide();
		stf.fetchStaff();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#listHdr').html(stf.listHdr);
	  $('#staffHdr').html(stf.editHdr);
	  $('#pwdFlds').hide();
		$('#pwdDiv').hide();
		$('#editDiv').hide();
		$('#listDiv').show();
    $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		$('#msgDiv').hide(10000);
		stf.resetForms();
		stf.fetchStaff();
	},
	
	//------------------------------
	fetchStaff: function () {
	  $.getJSON(stf.url,{'cat':'staff', 'mode':'getAllStaff'}, function(dataAray){
	    stf.json = dataAray;
			var html = '';
			for (var obj in dataAray) {
				var item = dataAray[obj];
	    	html += '<tr>\n';
    		html += '	<td valign="top">\n';
				html += '		<input type="button" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
				html += '		<input type="hidden" value="'+item['userid']+'"  />\n';
				html += '		<input type="button" class="pwdBtn" value="'+<?php echo "'".T("pwd")."'"; ?>+'" />\n';
	    	html += '	</td>\n';
    		html += '	<td valign="top">'+item['last_name']+'</td>\n';
     		html += '	<td valign="top">'+item['first_name']+'</td>\n';
     		html += '	<td valign="top">'+item['username']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['circ_flg']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['circ_mbr_flg']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['catalog_flg']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['reports_flg']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['admin_flg']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['tools_flg']+'</td>\n';
    		html += '	<td valign="top" class="center">'+item['suspended_flg']+'</td>\n';
	    	html += '</tr>\n';
			}
			$('#showList tBody').html(html);

			$('.editBtn').on('click',null,stf.doEdit);
			$('.pwdBtn').on('click',null,stf.doPwd);
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
		});
	},

	doEdit: function (e) {
	  var code = $(e.target).next().val();
		//console.log('you wish to edit code: '+code);
		for (var n in stf.json) {
		  if (stf.json[n]['userid'] == code) {
				stf.showStaff(stf.json[n]);
				break;
			}
		}
		return false;
	},
	doPwd: function (e) {
	  var code = $(e.target).prev().val();
		//console.log('you wish to change password of user: '+code);
		for (var n in stf.json) {
		  if (stf.json[n]['userid'] == code) {
				stf.crntUser = code;
				$('#pwdDiv fieldset legend span').html(stf.json[n].username);
				$('#pwd').focus();
	  		//document.getElementById('pwd').focus();
				$('#listDiv').hide();
				$('#editDiv').hide();
				$('#pwdDiv').show();
				break;
			}
		}
		return false;
	},
	
	showStaff: function (staff) {
		//console.log('showing : '+staff['description']);
	  $('#staffHdr').html(stf.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#deltBtn').show();
	  $('#last_name').focus();

		$('#last_name').val(staff['last_name']);
		$('#userid').val(staff['userid']);
		$('#first_name').val(staff['first_name']);
		$('#username').val(staff['username']);

		$('#circ_flg').val([staff['circ_flg']]);
		$('#circ_mbr_flg').val([staff['circ_mbr_flg']]);
		$('#catalog_flg').val([staff['catalog_flg']]);
		$('#admin_flg').val([staff['admin_flg']]);
		$('#tools_flg').val([staff['tools_flg']]);
		$('#reports_flg').val([staff['reports_flg']]);
		$('#suspended_flg').val([staff['suspended_flg']]);
		
		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	chkPwds: function (pwd1,pwd2) {
		var pw1 = $('#'+pwd1).val(),
				pw2 = $('#'+pwd2).val(),
				errMsg = '';
		//console.log('pw1='+pw1+'; pw2='+pw2);				
		if ( pw1 != pw2 ) {
			errMsg = <?php echo "'".T("Passwords do not match.")."'"; ?>;
		} else if (!pw1 || !pw2) {
			errMsg = <?php echo "'".T("Passwords may not be empty.")."'"; ?>;
		}
		if (errMsg) {
			$('#msgArea').html(errMsg);
			$('#msgDiv').show();
			return false;
		}
		return true;
	},
	
	doNewStaff: function (e) {
	  document.forms['editForm'].reset();
	  $('#staffHdr').html(stf.newHdr);
	  $('#pwdFlds').show();
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('last_name').focus();
		return false;
	},
	
	doSubmits: function (e) {
		e.preventDefault();
		e.stopPropagation();
		var theId = $("#editForm").find('input[type="submit"]:focus').attr('id');
		switch (theId) {
			case 'addBtn':	stf.doAddStaff();	break;
			case 'updtBtn':	stf.doUpdateStaff();	break;
			case 'deltBtn':	stf.doDeleteStaff();	break;
		}
	},
	
	doAddStaff: function () {
		if (!stf.chkPwds('pwd', 'pwd2')) return false;
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('addNewStaff');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(stf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('Added!');
				$('#msgDiv').show();
			  stf.doBackToList();
			}
		});
		return false;
	},

	doUpdateStaff: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateStaff');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(stf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('response');
				$('#msgDiv').show();
			  stf.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteStaff: function (e) {
		var msg = stf.delConfirmMsg+'\n>>> '+$('#username').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	'cat':'staff', 
										'mode':'d-3-L-3-tStaff', 
										'userid':$('#userid').val(), 
										'description':$('#username').val() 
									};
	  	$.post(stf.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html('response');
					$('#msgDiv').show();
			  	stf.doBackToList();
				}
			});
		}
		return false;
	},
	
	doSetStaffPwd: function () {
		e.preventDefault();
		e.stopPropagation();
		if (!stf.chkPwds('pwdA','pwdB')) return false;
		$('#mode').val('updateStaff');
	  var parms = {	'cat':'staff',
									'mode':'setStaffPwd', 
									'pwd':$('#pwdA').val(), 
									'pwd2':$('#pwdB').val(), 
									'userid':stf.crntUser };
	  $.post(stf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('response');
				$('#msgDiv').show();
		  	stf.doBackToList();
			}
		});
		return false;
	},
};

$(document).ready(stf.init);
</script>
