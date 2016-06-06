<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
"use strict";

var mtl = {
	<?php
	echo 'successMsg 		: "'.T("Update successful").'",'."\n";
	echo 'delConfirmMsg : "'.T("confirmDelete").'",'."\n";
	echo 'goBackLbl			: "'.T("Go Back").'",'."\n";
	echo 'cancelLbl			: "'.T("Cancel").'",'."\n";
	echo 'updateLbL			: "'.T("Update").'",'."\n";
	echo 'addNewLbl			: "'.T("Add New").'",'."\n";
	echo 'deleteLbl			: "'.T("Delete").'",'."\n";
	echo 'editLbl				: "'.T("Edit").'",'."\n";
	?>
	
	init: function () {
		//console.log('initializing mtl');	
		mtl.url = 'biblioFldsSrvr.php';
		mtl.listUrl = '../shared/listSrvr.php';
		mtl.editForm = $('#workForm');
		
		mtl.resetForms();
		mtl.initWidgets();

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		//mtl.btnColor = [];
		mtl.configBtn = $('#configBtn');
		mtl.saveBtn = $('#saveBtn');
		
		$('#typeList').on('change',null,mtl.doShowForm)
		$('#configBtn').on('click',null,mtl.doConfigLayout);
		$('#saveBtn').on('click',null,mtl.doSaveLayout);
		$('#goBackBtn').on('click',null,mtl.doReloadList);
		$('#editCnclBtn').on('click',null,mtl.doBackToList);
		$('#editDeltBtn').on('click',null,mtl.doDeleteFldset);
		$('#editUpdtBtn').on('click',null,mtl.doUpdateFldset);
		$('#marcBlocks').on('change',null,mtl.fetchMarcTags);
		$('#marcTags').on('change',null,mtl.fetchMarcFields);

		mtl.fetchMatlTypes();
		mtl.fetchInputTypes();
		mtl.fetchValidationList();
		mtl.resetForms();
	},
	
	//------------------------------
	initWidgets: function () {
		//console.log('initializing mtl widgets');
		var sortOpts1 = {
		  axis:   'y',
			cursor: 'move'
		};
		var sortOpts2 = {
			connectWith: '#existing',
			dropOnEmpty: true,
			cursor: 'move'
		};

		$("#existing").sortable(sortOpts1).disableSelection();
		$("#potential").sortable(sortOpts2).disableSelection();
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#pageHdr').html(mtl.pageHdr);
		$('#typeChoice').show();
		$('#workDiv').hide();
		$('#configDiv').hide();
		$('#editDiv').hide();
	  $('#msgDiv').hide();
		$('#updateMsg').hide();
		mtl.disableBtn('configBtn');
		mtl.disableBtn('saveBtn');
		mtl.disableBtn('goBackBtn');

		$('#typeList').focus();
	},
	
	disableBtn: function (btnId) {
		mtl.btnColor = $('#'+btnId).css('color');
		$('#'+btnId).css('color', '#888888');
		$('#'+btnId).disable();
	},
	enableBtn: function (btnId) {
	  //$('#'+btnId).css('color', mtl.btnColor);
	  $('#'+btnId).css('color', '#000000');
		$('#'+btnId).enable();
	},
	hideTopBtns: function() {
		$('#configBtn').hide();
		$('#saveBtn').hide();
		$('#goBackBtn').hide();
		$('#topSeperator').hide();
	},
	showTopBtns: function() {
		$('#configBtn').show();
		$('#saveBtn').show();
		$('#goBackBtn').show();
		$('#topSeperator').show();
	},

	doBackToList: function () {
	  $('#typeList').enable();
		$('#workDiv').show();
		$('#configDiv').hide();
		$('#editDiv').hide();
	  $('#msgDiv').hide();
		$('#updateMsg').hide();
		$('#editCnclBtn').val(mtl.cancelLbl);
		mtl.enableBtn('configBtn');
		mtl.disableBtn('saveBtn');
		mtl.disableBtn('goBackBtn');
		mtl.showTopBtns();
	},
	doReloadList: function () {
		mtl.doShowForm();
		mtl.doBackToList();
	},
	
	//------------------------------
	fetchMatlTypes: function () {
	    $.post(mtl.url,{mode:'getMtlTypes'}, function(data){
			var html = "<option value=\"0\">Choose One</option>\n";
			for (var n in data) {
				html += '<option value="'+data[n]['code']+'">'+data[n]['description']+'</option>\n';
			}
			$('#typeList').html(html);
		}, 'json');
	},
	fetchInputTypes: function () {
	    $.post(mtl.listUrl,{mode:'getInputTypes'}, function(data){
			var partsA = (data.replace(/'/g,"")).split('(');
			var partsB = partsA[1].split(')');
			var list = partsB[0].split(',');
			var html = '';
            for (var n in list) {
				html+= '<option value="'+list[n]+'">'+list[n]+'</option>';
			}
			$('#form_type').html(html);
		}, 'json');
	},
	fetchValidationList: function () {
	    $.post(mtl.listUrl,{mode:'getValidations'}, function(data){
			var html = '';
            for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#validation_cd').html(html);
		}, 'json');
	},

	//------------------------------
	// this section is for the drag & drop layout display
	doConfigLayout: function () {
	  $('#typeList').disable();
	  $('#workDiv').hide();
	  $('#msgDiv').hide();
	  $('#marcTags').hide()
		var matl = $('#typeList option:selected').text();
	  $('#configName').html("'"+matl+"'");
	  
	  mtl.fetchMarcBlocks();
	  
		mtl.disableBtn('configBtn');
		mtl.enableBtn('saveBtn');
		mtl.enableBtn('goBackBtn');
		$('#configDiv').show();
	},
	fetchMarcBlocks: function () {
	  $.getJSON(mtl.url,{mode:'getMarcBlocks'}, function(data){
			var html = "<option value=\"0\">Choose a Block</option>\n";
			for (var n in data) {
				html += '<option value="'+data[n]['block_nmbr']+'">'
						 +   data[n]['block_nmbr']+' - '+data[n]['description']
						 +  '</option>\n';
			}
			$('#marcBlocks').html(html);
		});
	},
	fetchMarcTags: function () {
	  $('#potential').html('');
	  mtl.blockNmbr = $('#marcBlocks').val();
	  $.getJSON(mtl.url,{mode:'getMarcTags',block_nmbr:mtl.blockNmbr}, function(data){
			var html = "<option value=\"0\">Choose a Tag</option>\n";
			for (var n in data) {
				html += '<option value="'+data[n]['tag']+'">'
						 +   data[n]['tag']+' - '+data[n]['description']
						 +  '</option>\n';
			}
			$('#marcTags').html(html).show();
		});
	},
	fetchMarcFields: function () {
	  mtl.tagNmbr = $('#marcTags').val();
	  $.getJSON(mtl.url,{mode:'getMarcFields',tag:mtl.tagNmbr}, function(data){
			var html = '';
			for (var n in data) {
			  var id = ('0'+data[n]['tag']).substr(-3,3)+data[n]['subfield_cd'];
				html += '<li id="'+'zqzqz'+id+'" '
						 +  'tag="'+data[n]['tag']+'" '
						 +	'subFld="'+data[n]['subfield_cd']+'" '
						 +	'>'
						 +	id+' - '+data[n]['description']
						 +	"</li>\n";
			}
			$('#potential').html(html);
		});
	},
	receiveMarcFld: function (e,ui){
		console.debug('received: e-->'+e.target.id+'; ui-->'+ui.item.id);
	},
	doSaveLayout: function () {
		// collect current line data in an array
		var arayd = $('#existing').sortable('toArray');
		// now build a JSON structure for server
		var jsonStr = '';
		for (var n in arayd) {
			if (($.trim(arayd[n])).substr(0,5) == "zqzqz"){
				// deal with additions
			  var entry = $('#'+arayd[n]);
			  var id = (arayd[n]).substr(5,99);
				var tag = entry.attr('tag');
				while (tag.length < 3) { tag = '0'+tag; };
				var subFld = entry.attr('subFld');
				var temp = entry.text();
				var label = temp;
				// remove extraneous tag from label
				if (temp.indexOf('-') >= 0) label = $.trim(entry.text().split('-')[1]);
				//if (temp.indexOf(',') >= 0) label = $.trim(entry.text().split(',')[1]);
				jsonStr += '{"id":"'+arayd[n]+'","position":"'+n+'","material_cd":"'+$('#typeList').val()+'"'+
									 ',"tag":"'+tag+'","subfield_cd":"'+subFld+'","label":"'+label+'"'+
									 ',"required":"0","repeatable":"0"},';
			} else {
				// position of hold-overs from original layout
		  	// param name & value MUST be in double quotes
				jsonStr += '{"id":"'+arayd[n]+'","position":"'+n+'"},';
			}
		}
		// trailing comma not allowed
		var howLong =(jsonStr.length)-1;
		var outStr = jsonStr.substr(0,howLong);
		// and off to server
		var parms = "mode=updateMarcFields&jsonStr=["+outStr+"]";
		$.post(mtl.url, parms, function(response) {
			if (response.length > 0) {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			mtl.fetchMatlFlds();
		});
	},
	
	//------------------------------
	// this portion is for the original page layout
	doShowForm: function () {
		mtl.fetchMatlFlds();
		$('#workDiv').show();
	},

	fetchMatlFlds: function (e) {
		//console.log ('in showWorkForm()');
		var matl = $('#typeList').val();
		$('#fldSet').empty();
		$('#existing').empty();
		$('#msgArea').hide();
		$('#msgArea').empty();
	  $.post(mtl.url,{mode:'getMatlFlds', matlCd: matl}, function(data){
			mtl.data = data;	// for later use
			var html = '';
			var html2 = '';
				$('#msgArea').html(html);
				$('#msgDiv').show();
			if ((!data.length) || (data.length == 0)){
				html2  = '<li id="zqzqz099a" tag="099" subfld="a">099a - Call Number</li>\n';
				html2 += '<li id="zqzqz100a" tag="100" subfld="a">100a - Author</li>\n';
				html2 += '<li id="zqzqz245a" tag="245" subfld="a">245a - Title</li>\n';
				$('#existing').html(html2);
			}
			else if (data.length > 0) {
				for (var n in data) {
				    var recId = 'mtl'+data[n]['material_field_id'];
				    var btnId = 'btn'+data[n]['material_field_id'];
	    		    html  = '<tr id="'+recId+'">\n';
                    html += '<td valign="top" class="primary">\n';
                    html += '<input type="button" id="'+btnId+'" value="'+mtl.editLbl+'" align="center" class="button editBtn" />\n';
                    html += '<input type="hidden" name="material_field_id" class="fldData" value="'+data[n]['material_field_id']+'" />\n';
                    html += '</td>';
                    html += '<td>';
                    html += '<span name="position" class="fldData">'+data[n]['position']+'</span>';
                    html += '</td>';
                    html += '<td valign="top" class="primary">\n';
                    html += '<input type="hidden" name="tag" class="fldData" value="'+data[n]['tag']+'" />\n';
                    html += '<input type="hidden" name="subfield_cd" class="fldData" value="'+data[n]['subfield_cd']+'" />\n';
                    html += '<span name="info" class="fldData">'+data[n]['tag']+data[n]['subfield_cd']+'</span>';
                    html += '</td>';
                    html += '<td valign="top" class="primary">\n';
                    html += '<span name="label" class="fldData">'+data[n]['label']+'</span>';
                    html += '</td>';
                    html += '<td valign="top" class="primary">\n';
                    html += '<span name="form_type" class="fldData">'+data[n]['form_type']+'</span>';
                    html += '</td>';
                    html += '<td valign="top" class="primary">\n';
                    html += '<span name="validation_cd" class="fldData">'+data[n]['validation_cd']+'</span>';
                    html += '</td>';
                    html += '<td valign="top" class="primary">\n';
                    html += '<span name="required" class="fldData">'+(data[n]['required']=='1'?'Y':'N')+'</span>';
                    html += '</td>';
                    html += '<td valign="top" class="primary">\n';
                    html += '<span name="repeatable" class="fldData">'+data[n]['repeatable']+'</span>';
                    html += '</td>';
                    html += '</tr>\n';
                    $('#fldSet').append(html);

                    html2 += '<li '
							  +  'id="'+data[n]['material_field_id']+'" '
							  +	 'tag="'+data[n]['tag']+'" '
							  +	 'subFld="'+data[n]['subfield_cd']+'" '
							  +	 '>'
							  +	 data[n]['tag']+data[n]['subfield_cd']+' - '+data[n]['label']
							  +	 "</li>\n";
				}
				$('#existing').html(html2);
				
				obib.reStripe();
				$('.editBtn').on('click',null,mtl.doEdit);
			}
			mtl.enableBtn('configBtn');
		}, 'json');
	},
	
	collectSpanData: function (id) {
	  // collect data from a single line in preparation for editing; return as array of JSON
	  var rslt = '[';
		var entry = '';
	    $('#'+id+' span.fldData').each(function (n) {
			entry = "{'name':'"+$(this).attr('name')+"','value':'"+$(this).html()+"'},";
			rslt += entry;
		});
	    $('#'+id+' input.fldData:hidden').each(function (n) {
			entry = "{'name':'"+$(this).attr('name')+"','value':'"+$(this).val()+"'},";
			rslt += entry;
		});
		return rslt.substr(0,rslt.length-1)+']';
	},
	
	doEdit: function (e) {
	  // come here as result of pressing a line's edit button
		$('#workDiv').hide();
		$('#msgDiv').hide();
		$('#addBtn').hide();
		$('#typeList').disable();
		mtl.hideTopBtns();
		$('#editDiv').show();
		
	  var theTagId = $(this).next().val();
		var mtlId ='mtl'+theTagId;
		var inpt = mtl.collectSpanData(mtlId);
		var parms = eval('('+inpt+')');
		for(var n in parms) {
			var fldName = parms[n]['name'],
					fldVal = parms[n]['value'];
			switch (fldName) {
				case 'required':
//				$('#editTbl #required').val([fldVal=='Y'?'1':'0']);
					$('#editTbl #required').val(fldVal);
					break;
				case 'repeatable':
//				$('#editTbl #repeatable').val([fldVal=='Y'?'1':'0']);
					$('#editTbl #repeatable').val(fldVal);
					break;
				case 'form_type':
					$('#editTbl #form_type').val([fldVal]);
				  break;
				case 'validation_cd':
					$('#editTbl #form_type').val([fldVal]);
				  break;
				default:
					$('#editTbl #'+fldName).val(fldVal);
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
			  mtl.fetchMatlFlds();
				$('#updateMsg').html(mtl.successMsg);
				$('#updateMsg').show();
				$('#msgArea').html(mtl.successMsg);
				$('#msgDiv').show();
				$('#editCnclBtn').val(mtl.goBackLbl)
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
 }
};

$(document).ready(mtl.init);
</script>
