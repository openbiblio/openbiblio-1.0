<script language="JavaScript1.1" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
mtl = {
	<?php
	echo 'successMsg 		: "'.T('updateSuccess').'",'."\n";
	echo 'delConfirmMsg : "'.T('confirmDelete').'",'."\n";
	echo 'goBackLbl			: "'.T('Go Back').'",'."\n";
	echo 'cancelLbl			: "'.T('Cancel').'",'."\n";
	echo 'updateLbL			: "'.T('Update').'",'."\n";
	echo 'addNewLbl			: "'.T('Add New').'",'."\n";
	echo 'deleteLbl			: "'.T('Delete').'",'."\n";
	echo 'editLbl				: "'.T('Edit').'",'."\n";
	?>
	
	init: function () {
	  init(); // part of original openbiblio js code
		mtl.initWidgets();

		mtl.url = 'fldsSrvr.php';
		mtl.editForm = $('#workForm');
		
		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#typeList').bind('change',null,mtl.fetchMatlFlds)
		$('#editCnclBtn').bind('click',null,mtl.doBackToList);
		$('#editDeltBtn').bind('click',null,mtl.doDeleteFldset);
		$('#editUpdtBtn').bind('click',null,mtl.doUpdateFldset);

		mtl.fetchMatlTypes();
		mtl.resetForms();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');

	  $('#pageHdr').html(mtl.pageHdr);
		$('#workDiv').hide();
		$('#editDiv').hide();
	  $('#msgDiv').hide();
		$('#updateMsg').hide();
    
		$('#typeList').focus();
	},
	doBackToList: function () {
		$('#workDiv').show();
		$('#editDiv').hide();
	  $('#msgDiv').hide();
		$('#updateMsg').hide();
		$('#editCnclBtn').val(mtl.cancelLbl);
	},
	doReloadList: function () {
		mtl.fetchMatlFlds();
		mtl.doBackToList();
	},
	
	//------------------------------
	fetchMatlTypes: function () {
	  $.getJSON(mtl.url,{mode:'getMtlTypes'}, function(data){
			var html = "<option value=\"0\">Choose One</option>\n";
			for (nTypes in data) {
				html += "<option value="+data[nTypes]['code']+">"+data[nTypes]['description']+"</option>\n";
			}
			$('#typeList').html(html);
		})
	},

	//------------------------------
	fetchMatlFlds: function (e) {
		//console.log ('in showWorkForm()');
		var matl = $('#typeList').val();
		$('#fldSet').empty();
	  $.getJSON(mtl.url,{mode:'getMatlFlds', matlCd: matl}, function(data){
			var html = '';
			if (data.length > 0) {
				for (n in data) {
				  var recId = 'mtl'+data[n]['material_field_id'];
				  var btnId = 'btn'+data[n]['material_field_id'];
	    		html  = '<tr id="'+recId+'">\n';
    			html += '<td valign="top" class="primary">\n';
 					html += '<input type="button" id="'+btnId+'" value="'+mtl.editLbl+'" align="center" class="button editBtn" />\n';
 					html += '<input type="hidden" name="material_field_id" class="fldData" value="'+data[n]['material_field_id']+'" />\n';
					html += '</td>';
					html += '<td>';
    			html += '<input type="text" name="position" class="primary fldData" size="4" value="'+data[n]['position']+'" />\n';
					html += '</td>';
	   			html += '<td valign="top" class="primary">\n';
 					html += '<input type="hidden" name="tag" class="fldData" value="'+data[n]['tag']+'" />\n';
 					html += '<input type="hidden" name="subfield_cd" class="fldData" value="'+data[n]['subfield_cd']+'" />\n';
					html += '<span class="fldData">'+data[n]['tag']+data[n]['subfield_cd']+'</span>';
					html += '</td>';
    			html += '<td valign="top" class="primary">\n';
					html += '<input type="text" name="label" class="primary fldData" size="30" value="'+data[n]['label']+'" />\n';
					html += '</td>';
    			html += '<td valign="top" class="primary">\n';
    			html +=   '<select name="form_type" class="fldData">\n';
    			html +=     '<option value="text" '+(data[n]['form_type']=='text'?'selected':'')+'>Single Line</option>\n';
    			html +=     '<option value="textarea" '+(data[n]['form_type']=='textarea'?'selected':'')+'>Multi Line</option>\n';
    			html +=   '</select>\n';
					html += '</td>';
    			html += '<td valign="top" class="primary">\n';
					html += '<input type="checkbox" name="required" class="primary fldData" size="4" value="1" '+
									(data[n]['required']=='1'?'checked':'')+'" />\n';
					html += '</td>';
    			html += '<td valign="top" class="primary">\n';
					html += '<input type="text"  name="repeatable" class="primary fldData" size="5" value="'+data[n]['repeatable']+'" />\n';
					html += '</td>';
					html += '</tr>\n';
					$('#fldSet').append(html);
				}
				$('table.striped tbody tr:odd td .fldData').addClass('altBG');
				$('.editBtn').bind('click',null,mtl.doEdit);
			}
			else {
 				html = '<h3>'+<?php echo '"'.T('nothingFoundMsg').'"';?>+", <br />"+<?php echo '"'.T('AddNewMtlMsg').'"'; ?>+"</h3>";
				$('#msgArea').html(html);
				$('#msgDiv').show();
			}
				$('#workDiv').show();
		});
	},
	
	doEdit: function (e) {
		$('#workDiv').hide();
		$('#msgDiv').hide();
		$('#addBtn').hide();
		$('#editDiv').show();
		
	  var theTagId = $(this).next().val();
		var mtlId ='mtl'+theTagId;
		var parms = $('#'+mtlId+' .fldData').serializeArray();
		for(n in parms) {
			var fldName = parms[n]['name']
			if ((fldName == 'required') || (fldName == 'form_type')) {
			  // for radio-buttons, checkboxes, selects
				$('#editTbl #'+fldName).val([parms[n]['value']]);
			}
			else {
			  // all else
				$('#editTbl #'+fldName).val(parms[n]['value']);
			}
		}
	},
	
	doUpdateFldset: function () {
	  //if (!mtl.doValidate(e)) return;
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#editMode').val('updateFldSet');

		var parms = $('#editForm').serialize();

		$.post(mtl.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#updateMsg').html(mtl.successMsg);
				$('#updateMsg').show();
				$('#msgArea').html(mtl.successMsg);
				$('#msgDiv').show();
				$('#editCnclBtn').val(mtl.gobackLbl)
			}
		});
	},

	doDeleteFldset: function (e) {
		var msg = mtl.delConfirmMsg+'\n>>> '+$('#editForm tbody #label').val()+' <<<';
	  if (confirm(msg)) {
	  	$.get(mtl.url,
								{	mode:'d-3-L-3-tFld',
									material_field_id:$('#editForm #material_field_id').val()
								},
								function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
			  	mtl.doReloadList();
				}
			});
		}
	},
	
	doNewFldset: function (e) {
	  $('#hostHdr').html(mtl.newHdr);
	  $('#hostForm tfoot #updtBtn').hide();
	  $('#hostForm tfoot #addBtn').show();
	  $('#hostForm tbody #name').focus();

		$('#listDiv').hide();
		$('#editDiv').show();
	},

	doValidate: function () {
console.log('user input validation not available!!!!, see admin/settings_edit');
		return true;
/*
	},
	 
	
	doNewHost: function (e) {
	  $('#hostHdr').html(mtl.newHdr);
	  $('#hostForm tfoot #updtBtn').hide();
	  $('#hostForm tfoot #addBtn').show();
	  $('#hostForm tbody #name').focus();
	  
		$('#listDiv').hide();
		$('#editDiv').show();
	},
	doEdit: function (e) {
	  var theHostId = $(this).next().val();
		//console.log('you wish to edit host #'+theHostId);
		for (nHost in mtl.hostJSON) {
		  if (mtl.hostJSON[nHost]['id'] == theHostId) {
				mtl.showHost(mtl.hostJSON[nHost]);
			}
		}
	},
	
	doValidate: function () {
console.log('user input validation not available!!!!, see admin/settings_edit');
		return true;
	},
	
	doAddHost: function () {
	  if (!mtl.doValidate()) return;

		$('#mode').val('addNewHost');
		var parms = $('#hostForm').serialize();
		//console.log(parms);
		$.post(mtl.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
			  mtl.doBackToList();
			}
		});
	},

	doUpdateHost: function () {
	  if (!mtl.doValidate()) return;

		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateHost');
		var parms = $('#hostForm').serialize();
		//console.log(parms);
		$.post(mtl.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html(mtl.successMsg);
					$('#updateMsg').show();
				}
			  mtl.doBackToList();
			}
		});
	},
	
	doDeleteHost: function (e) {
		var msg = mtl.delConfirmMsg+'\n>>> '+$('#hostForm tbody #name').val()+' <<<';
	  if (confirm(msg)) {
	  	$.get(mtl.url,
								{	mode:'d-3-L-3-tHost',
									id:$('#hostForm tbody #id').val()
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
			  	mtl.doBackToList();
				}
			});
		}
	},

	showHost: function (host) {
		//console.log('showing : '+host['name']);
	  $('#hostHdr').html(mtl.editHdr);
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
*/
	}
};

$(document).ready(mtl.init);
</script>
