<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document

mbc = {
	<?php
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("Member Types")."',\n";
		echo "editHdr: '".T("Editing Member Type")."',\n";
		echo "newHdr: '".T("Add new type")."',\n";
	?>
	
	init: function () {
		mbc.initWidgets();

		mbc.url = 'adminSrvr.php';
		mbc.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').bind('click',null,mbc.doNewTypes);
		$('#editForm').bind('submit',null,mbc.doSubmits);
		$('#cnclBtn').bind('click',null,mbc.resetForms);

		mbc.resetForms()
	  $('#msgDiv').hide();
		mbc.fetchTypes();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#listHdr').html(mbc.listHdr);
	  $('#editHdr').html(mbc.editHdr);
		$('#editDiv').hide();
		$('#listDiv').show();
    $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		$('#msgDiv').hide(10000);
		mbc.resetForms();
		mbc.fetchTypes();
	},
	
	//------------------------------
	fetchTypes: function () {
	  $.getJSON(mbc.url,{ 'cat':'mbrTypes', 'mode':'getAllMbrTypes' }, function(dataAray){
	    mbc.json = dataAray;
			var html = '';
			for (obj in dataAray) {
				var item = dataAray[obj];
	    	html += '<tr>\n';
    		html += '	<td>\n';
				html += '		<input type="button" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
				html += '		<input type="hidden" value="'+item['code']+'"  />\n';
	    	html += '	</td>\n';
    		html += '	<td>'+item['description']+'</td>\n';
    		html += '	<td class="number">$'+item['max_fines']+'</td>\n';
    		html += '	<td class="center">'+item['default_flg']+'</td>\n';
	    	html += '</tr>\n';
			}
			$('#showList tBody').html(html);

			$('.editBtn').bind('click',null,mbc.doEdit);
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
		});
	},

	doEdit: function (e) {
	  var code = $(e.target).next().val();
		//console.log('you wish to edit code: '+code);
		for (n in mbc.json) {
		  if (mbc.json[n]['code'] == code) {
				mbc.showTypes(mbc.json[n]);
			}
		}
		return false;
	},
	
	showTypes: function (fields) {
		//console.log('showing : '+fields['description']);
	  $('#fieldsHdr').html(mbc.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#deltBtn').show();
	  document.getElementById('description').focus();

		$('#code').val(fields['code']).attr('readonly',true).attr('required',false);
		$('#description').val(fields['description']);
		$('#max_fines').val(fields['max_fines']);
		$('input[name="default_flg"]').val([fields['default_flg']]);
		$('#codeReqd').hide();

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewTypes: function () {
	  document.forms['editForm'].reset();
	  $('#fieldsHdr').html(mbc.newHdr);
		$('#code').attr('readonly',false)
							.attr('required',true);
		$('#codeReqd').show();
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('code').focus();
		return false;
	},
	
	doSubmits: function (e) {
		e.preventDefault();
		e.stopPropagation();
		var theId = $("#editForm").find('input[type="submit"]:focus').attr('id');
		switch (theId) {
			case 'addBtn':	mbc.doAddTypes();	break;
			case 'updtBtn':	mbc.doUpdateTypes();	break;
			case 'deltBtn':	mbc.doDeleteTypes();	break;
		}
	},
	
	doAddTypes: function () {
		$('#mode').val('addNewMbrType');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(mbc.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			  mbc.doBackToList();
			}
		});
		return false;
	},

	doUpdateTypes: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateMbrType');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(mbc.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			  mbc.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteTypes: function (e) {
		var msg = mbc.delConfirmMsg+'\n>>> '+$('#description').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	'cat':'mbrTypes', 'mode':'d-3-L-3-tMbrType', 'code':$('#code').val(), 'description':$('#description').val() };
	  	$.post(mbc.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html(response);
					$('#msgDiv').show();
			  	mbc.doBackToList();
				}
			});
		}
		return false;
	},
};

$(document).ready(mbc.init);
</script>
