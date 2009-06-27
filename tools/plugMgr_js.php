<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
?>
<style>
div#listDiv h5{
	text-align: left; margin:0; padding:0;
	}
#listDiv fieldset {
	margin: 0; padding: 0;
	}
#listDiv ul {
	list-style-type: none; text-decoration: none;
	display: block; text-align: left; margin:0; padding:0;
	}
#listDiv label {
	text-align: left; margin:0; padding:0;
	}
#msgDiv fieldset {
		padding: 5px;
	}
#msgArea h4 {
	margin: 0; padding: 2px;
	color: red;
	}
</style>

<script src="../plugins/jQuery.js" type="text/javascript"></script>
<script src="../shared/jsLib.js" type="text/javascript"></script>

<script language="JavaScript1.4" >
plm = {
	
	init: function () {
	  init(); // part of original openbiblio js code
		plm.initWidgets();

		plm.url = 'plugMgrSrvr.php';
		plm.editForm = $('#workForm');

		$('#pluginOK').bind('change',null,plm.doToggleList)

		plm.fetchPluginInfo();
		plm.resetForms();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');

	  $('#msgDiv').hide();
		plm.checkPluginOK();
	},

	//------------------------------
	doToggleList: function (btnId) {
	  var state = ($('#pluginOK:checked').length>0?'Y':'N');
		var parms = 'mode=updatePluginSetting&allow_plugins_flg='+state;
		$.post(plm.url, parms, function(response) {
		  if (response.length > 0) {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			plm.checkPluginOK();
		});
	},
	checkPluginOK: function () {
	  if ($('#pluginOK:checked').length > 0) {
			plm.enableList();
		}
		else {
			plm.disableList();
		}
	},
	enableList: function () {
		$('#listDiv fieldset')
				.css('color','#000000')
				.css('background-color','#ffffff');
		$('#listDiv input.plugins').enable()
	},
	disableList: function () {
			$('#listDiv fieldset')
					.css('color','#888888')
					.css('background-color','#cccccc');
			$('#listDiv input.plugins').disable()
	},

	//------------------------------
	fetchPluginInfo: function () {
	  $.getJSON(plm.url,{mode:'getPluginList'}, function(data){
			$('#pluginList').empty();
	    var html='';
	    if (data.length > 0) {
				for (n in data) {
					html += '<li>';
					html += '<input type="checkbox" class="plugins" id="'+data[n]['name']+'"  '
								+ ' name="'+data[n]['name']+'" value="Y" '
					      + (data[n]['OK'] == 'Y'?'checked':'')+' />';
					html += data[n]['name'];
					html += '</li>\n';
				}
				$('#pluginList').html(html)
				$('#pluginList input').bind('click',null,plm.doUpdateList);
			}
			else {
				html = <h5><?php echo T('No Plugins found'); ?></h5>
			}
			plm.checkPluginOK();
		});
	},
	
	doUpdateList: function (e) {
	  var meId = e.target.id;
	  var state = ($('#'+meId+':checked').length>0?'Y':'N');
		var parms = 'mode=updatePluginList&id='+meId+'&allowPlugin='+state;
		$.post(plm.url, parms, function(response) {
		  if (response.length > 0) {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
		});
/*
	},

	disableBtn: function (btnId) {
		plm.btnColor = $('#'+btnId).css('color');
		$('#'+btnId).css('color', '#888888');
		$('#'+btnId).disable();
	},
	enableBtn: function (btnId) {
	  $('#'+btnId).css('color', plm.btnColor);
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
		$('#editCnclBtn').val(plm.cancelLbl);
		plm.enableBtn('configBtn');
		plm.disableBtn('saveBtn');
		plm.disableBtn('goBackBtn');
		plm.showTopBtns();
	},
	doReloadList: function () {
		plm.doShowForm();
		plm.doBackToList();
	},
	
	//------------------------------
	doShowForm: function () {
		plm.fetchMatlFlds();
		plm.enableBtn('configBtn');
		$('#workDiv').show();
	},

	//------------------------------
	doConfigLayout: function () {
	  $('#typeList').disable();
	  $('#workDiv').hide();
	  $('#msgDiv').hide();
	  $('#marcTags').hide()
	  var matlSet = $('#typeList').text();
	  var matlCd = $('#typeList').val();
	  var matlArray = matlSet.split('\n');
	  var matl = matlArray[matlCd];
	  $('#configName').html("'"+matl+"'");
	  plm.fetchMarcBlocks();
		plm.disableBtn('configBtn');
		plm.enableBtn('saveBtn');
		plm.enableBtn('goBackBtn');
		$('#configDiv').show();
	},
	fetchMarcBlocks: function () {
	  $.getJSON(plm.url,{mode:'getMarcBlocks'}, function(data){
			var html = "<option value=\"0\">Choose a Block</option>\n";
			for (n in data) {
				html += '<option value="'+data[n]['block_nmbr']+'">'
						 +   data[n]['block_nmbr']+' - '+data[n]['description']
						 +  '</option>\n';
			}
			$('#marcBlocks').html(html);
		});
	},
	fetchMarcTags: function () {
	  $('#potential').html('');
	  plm.blockNmbr = $('#marcBlocks').val();
	  $.getJSON(plm.url,{mode:'getMarcTags',block_nmbr:plm.blockNmbr}, function(data){
			var html = "<option value=\"0\">Choose a Tag</option>\n";
			for (n in data) {
				html += '<option value="'+data[n]['tag']+'">'
						 +   data[n]['tag']+' - '+data[n]['description']
						 +  '</option>\n';
			}
			$('#marcTags').html(html).show();
		});
	},
	fetchMarcFields: function () {
	  plm.tagNmbr = $('#marcTags').val();
	  $.getJSON(plm.url,{mode:'getMarcFields',tag:plm.tagNmbr}, function(data){
			var html = '';
			for (n in data) {
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
		//console.debug('received: e-->'+e.target.id+'; ui-->'+ui.item.id);
	},
	doSaveLayout: function () {
		// collect current line data in an array
		var arayd = $('#existing').sortable( 'toArray');
		// now build a JSON structure for server
		var jsonStr = '';
		for (n in arayd) {
			if (($.trim(arayd[n])).substr(0,5) == "zqzqz"){
				// deal with additions
			  var entry = $('#'+arayd[n]);
			  var id = (arayd[n]).substr(5,99);
				var tag = entry.attr('tag');
				var subFld = entry.attr('subFld');
				var label = entry.text();
				jsonStr += '{"id":"'+arayd[n]+'","position":"'+n+'","material_cd":"'+$('#typeList').val()+'"'+
									 ',"tag":"'+tag+'","subfield_cd":"'+subFld+'","label":"'+label+'"},';
			} else {
				// position of holdovers from original layout
		  	// param name & value MUST be in double quotes
				jsonStr += '{"id":"'+arayd[n]+'","position":"'+n+'"},';
			}
		}
		// trailing comma not allowed
		var howLong =(jsonStr.length)-1;
		var outStr = jsonStr.substr(0,howLong);
		// and off to server
		var parms = "mode=updateMarcFields&jsonStr=["+outStr+"]";
		$.post(plm.url, parms, function(response) {
			if (response.length > 0) {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			plm.fetchMatlFlds();
		});
	},
	
	//------------------------------
	fetchMatlFlds: function (e) {
		//console.log ('in showWorkForm()');
		var matl = $('#typeList').val();
		$('#fldSet').empty();
		$('#existing').empty();
		$('#msgArea').hide();
		$('#msgArea').empty();
	  $.getJSON(plm.url,{mode:'getMatlFlds', matlCd: matl}, function(data){
			var html = '';
			var html2 = '';
			if (data.length > 0) {
				for (n in data) {
				  var recId = 'mtl'+data[n]['material_field_id'];
				  var btnId = 'btn'+data[n]['material_field_id'];
	    		html  = '<tr id="'+recId+'">\n';
    			html += '<td valign="top" class="primary">\n';
 					html += '<input type="button" id="'+btnId+'" value="'+plm.editLbl+'" align="center" class="button editBtn" />\n';
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

					html2 += '<li '
							  +  'id="'+data[n]['material_field_id']+'" '
								+	 'tag="'+data[n]['tag']+'" '
							  +	 'subFld="'+data[n]['subfield_cd']+'" '
							  +	 '>'
							  +	 data[n]['tag']+data[n]['subfield_cd']+' - '+data[n]['label']
							  +	 "</li>\n";

				}
				$('#existing').html(html2);
				$('table.striped tbody tr:odd td .fldData').addClass('altBG');
				$('.editBtn').bind('click',null,plm.doEdit);
			}
			else {
 				html = '<h3>'+<?php echo '"'.T('nothingFoundMsg').'"';?>+", <br />"+<?php echo '"'.T('addNewMtlMsg').'"'; ?>+"</h3>";
				$('#msgArea').html(html);
				$('#msgDiv').show();
				$('<li id="waitClass"><?php echo T("waitForServer");?></li>').appendTo('#existing');
			}
		});
	},
	doEdit: function (e) {
		$('#workDiv').hide();
		$('#msgDiv').hide();
		$('#addBtn').hide();
		plm.hideTopBtns();
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
	  //if (!plm.doValidate(e)) return;
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#editMode').val('updateFldSet');

		var parms = $('#editForm').serialize();

		$.post(plm.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
			  plm.fetchMatlFlds();
				$('#updateMsg').html(plm.successMsg);
				$('#updateMsg').show();
				$('#msgArea').html(plm.successMsg);
				$('#msgDiv').show();
				$('#editCnclBtn').val(plm.gobackLbl)
			}
		});
	},
	doDeleteFldset: function (e) {
		var msg = plm.delConfirmMsg+'\n>>> '+$('#editForm tbody #label').val()+' <<<';
	  if (confirm(msg)) {
	  	$.get(plm.url,
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
			  	plm.doReloadList();
				}
			});
		}
	},
	doNewFldset: function (e) {
	  $('#hostHdr').html(plm.newHdr);
	  $('#hostForm tfoot #updtBtn').hide();
	  $('#hostForm tfoot #addBtn').show();
	  $('#hostForm tbody #name').focus();

		$('#listDiv').hide();
		$('#editDiv').show();
	},

	doValidate: function () {
console.log('user input validation not available!!!!, see admin/settings_edit');
		return true;
*/
 }
};

$(document).ready(plm.init);
</script>
