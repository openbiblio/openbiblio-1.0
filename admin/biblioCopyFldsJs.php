<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document

bcf = {
	<?php
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("Custom Copy Fields")."',\n";
		echo "editHdr: '".T("Editing Custom Fields")."',\n";
		echo "newHdr: '".T("Add new custom field")."',\n";
	?>
	
	init: function () {
		bcf.initWidgets();

		bcf.url = 'adminSrvr.php';
		bcf.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').on('click',null,bcf.doNewFields);
		$('#editForm').on('submit',null,bcf.doSubmits);
		$('#cnclBtn').on('click',null,bcf.resetForms);

		bcf.resetForms()
	  $('#msgDiv').hide();
		bcf.fetchFields();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#listHdr').html(bcf.listHdr);
	  $('#editHdr').html(bcf.editHdr);
		$('#editDiv').hide();
		$('#listDiv').show();
    $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		//$('#msgDiv').hide(10000);
		bcf.resetForms();
		bcf.fetchFields();
	},
	
	//------------------------------
	fetchFields: function () {
	  $.getJSON(bcf.url,{cat: 'copyFlds', mode:'getAllCopyFlds'}, function(dataAray){
	    bcf.json = dataAray;
			var html = '';
			for (obj in dataAray) {
				var item = dataAray[obj];
	    	html += '<tr>\n';
    		html += '	<td valign="top">\n';
				html += '		<input type="button" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
				html += '		<input type="hidden" value="'+item['code']+'"  />\n';
	    	html += '	</td>\n';
    		html += '	<td valign="top">'+item['code']+'</td>\n';
    		html += '	<td valign="top">'+item['description']+'</td>\n';
	    	html += '</tr>\n';
			}
			$('#showList tBody').html(html);

			$('.editBtn').on('click',null,bcf.doEdit);
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
		});
	},

	doEdit: function (e) {
	  var code = $(e.target).next().val();
		//console.log('you wish to edit code: '+code);
		for (n in bcf.json) {
		  if (bcf.json[n]['code'] == code) {
				bcf.showFields(bcf.json[n]);
			}
		}
		return false;
	},
	
	showFields: function (fields) {
		//console.log('showing : '+fields['description']);
	  $('#fieldsHdr').html(bcf.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#deltBtn').show();
	  //$('#description').focus();
	  document.getElementById('description').focus();

		$('#code').val(fields['code']).attr('readonly',true).attr('required',false);
		$('#description').val(fields['description']);
		$('#codeReqd').hide();

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewFields: function (e) {
	  document.forms['editForm'].reset();
	  $('#fieldsHdr').html(bcf.newHdr);
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
			case 'addBtn':	bcf.doAddFields();	break;
			case 'updtBtn':	bcf.doUpdateFields();	break;
			case 'deltBtn':	bcf.doDeleteFields();	break;
		}
	},
	
	doAddFields: function () {
		$('#mode').val('addNewCopyFld');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(bcf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			  bcf.doBackToList();
			}
		});
		return false;
	},

	doUpdateFields: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateCopyFld');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(bcf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			  bcf.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteFields: function (e) {
		var msg = bcf.delConfirmMsg+'\n>>> '+$('#description').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	'cat':'copyFlds', 'mode':'d-3-L-3-tCopyFld', 'code':$('#code').val(), 'description':$('#description').val() };
	  	$.post(bcf.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html(response);
					$('#msgDiv').show();
			  	bcf.doBackToList();
				}
			});
		}
		return false;
	},
};

$(document).ready(bcf.init);
</script>
