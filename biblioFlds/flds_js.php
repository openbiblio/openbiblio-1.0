<script language="JavaScript1.1" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
mtl = {
	<?php
	echo 'successMsg : "'.T('updateSuccess').'",'."\n";
	?>
	
	init: function () {
	  init(); // part of original openbiblio js code

		mtl.initWidgets();

		mtl.url = 'fldsSrvr.php';
		mtl.editForm = $('#workForm');
		
		$('#typeList').bind('change',null,mtl.fetchMatlFlds)


//		$('#workForm tFoot #newBtn').bind('click',null,mtl.doAddFldset);
		$('#workForm tFoot #updtBtn').bind('click',null,mtl.doUpdateFldset);
//		$('#workForm tFoot #deltBtn').bind('click',null,mtl.doDeleteFldset);

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
	  $('#msgDiv').hide();
		$('#updateMsg').hide();
		//$('#listDiv').show();
    //$('#hostForm tFoot #cnclBtn').val('Cancel');
    
		$('#typeList').focus();
	},
	doBackToList: function () {
		mtl.fetchMatlTypes();
		mtl.resetForms();
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
	    		html  = '<tr id="'+recId+'">\n';
    			html += '<td valign="top" class="primary">\n';
 					html += '<input type="hidden" name="material_field_id" class="fldData" value="'+data[n]['material_field_id']+'" />\n';
					html += '<input type="text" name="position" class="primary fldData" size="4" value="'+data[n]['position']+'" />\n';
					html += '</td>';
    			html += '<td valign="top" class="primary">\n';
 					html += '<input type="hidden" name="tag" class="fldData" value="'+data[n]['tag']+'" />\n';
 					html += '<input type="hidden" name="subfield_cd" class="fldData" value="'+data[n]['subfield_cd']+'" />\n';
					html += data[n]['tag']+data[n]['subfield_cd'];
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
    			//html += '<td valign="top" name="search_results" class="primary">'+data[n]['search_results']+'</td>\n';
					html += '</tr>\n';
					$('#fldSet').append(html);
//					$('#'+recId+' td').find('input,select,checkbox').bind('change',{'recId':recId},mtl.doUpdateFldset);
					$('#'+recId+' td .fldData').bind('change',{'recId':recId},mtl.doUpdateFldset);
				}
				$('#fldSet tr:even td').addClass('altBG');
				$('#fldSet tr:even td input').addClass('altBG');
			}
			else {
 				html = "<h3>Nothing found for this material.<br />Click 'Add New' to create new entries.</h3>";
				$('#msgArea').html(html);
				$('#msgDiv').show();
			}
				$('#workDiv').show();
		});
	},
	
	doUpdateFldset: function (e) {
	  //if (!mtl.doValidate(e)) return;

		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateFldSet');
		var mtlId =e.data['recId'];
		var parms = 'mode=updateFldSet&' + $('#'+mtlId+' .fldData').serialize();
		//console.log(parms);

		$.post(mtl.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
//				if (response.substr(0,1)){
					mtl.fetchMatlFlds();
//					$('#updateMsg').html(mtl.successMsg);
//					$('#updateMsg').show();
//					$('#msgArea').html(mtl.successMsg);
//					$('#msgDiv').show();
//				}
			  //mtl.doBackToStart();
			}
		});

	},

	doDeleteFldset: function (e) {
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
