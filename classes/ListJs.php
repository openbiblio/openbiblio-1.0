<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict"

/* *****************************************************************************
		from "JavaScript: the definitive guide", 6th ed, p.119
 **************************************************************************** */
function inherit(p) {
	if (p == null) throw TypeError();						// p must be non-null object
	if (Object.create) return Object.create(p);	// use it if you got it!
	// else																			
	var t = typeof p;														// otherwise do more type checking
	if (t!== "object" && t!== "function") throw TypeError();	
	function f() {};														// define a dummy constructor
	f.prototype = p;														// set its prototype to p
	return new f();															// use f() to create an 'heir' of p
}
/* ************************************************************************** */
// Base class for DB lookup table maintenance
//   url: data server URL
//   form: in of html form to use
//   dbAlias: nickname of server database
//   opts: js object containing as a minimum: listHdr, editHdr, newHdr
function List ( url, form, dbAlias, hdrs, listFlds, opts ) {
	this.url = url;
	this.editForm = form;
	this.dbAlias = dbAlias;
	this.listHdr = hdrs.listHdr;
	this.editHdr = hdrs.editHdr;
	this.newHdr = hdrs.newHdr;
	this.listFlds = listFlds;
	this.focusFld = opts.focusFld || 'description';
};

List.prototype.delConfirmMsg = <?php echo '"'.T("Are you sure you want to delete ").'"'; ?>;
List.prototype.cancelStr = <?php echo '"'.T("Cancel").'"'?>;
	
List.prototype.init = function () {
	this.initWidgets();

	$('#reqdNote').css('color','red');
	$('.reqd sup').css('color','red');
	$('#updateMsg').hide();

	$('.newBtn').on('click',null,$.proxy(this.doNewFields,this));
	$('#editForm').on('submit',null,$.proxy(this.doSubmitFields,this));
	$('#cnclBtn').on('click',null,$.proxy(this.resetForms,this));

	this.fetchList();
	this.resetForms()
};
	
	//------------------------------
List.prototype.initWidgets =function () {
};

List.prototype.resetForms = function () {
	$('#editDiv').hide();
  $('#listHdr').html(this.listHdr);
  $('#editHdr').html(this.editHdr);
  $('#cnclBtn').val('Cancel');
	$('#listDiv').show();
};

List.prototype.doBackToList = function () {
	$('#msgDiv').hide(10000);
	this.resetForms();
	this.fetchList();
};
	
	//------------------------------
List.prototype.fetchList = function () {
	var params = { 'cat':this.dbAlias,
								 'mode':'getAll_'+this.dbAlias, 
							 };
  $.getJSON( this.url, params, $.proxy(this.fetchHandler,this));
};
List.prototype.fetchHandler = function(dataAray){
    this.json = dataAray;	// will be re-used later for editing
		var html, test;
		for (var obj in dataAray) {
			var item = dataAray[obj]; 
			// these are common and fixed
    	html += '<tr>\n';
   		html += '	<td valign="top">\n';
			html += '		<input type="button" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
			html += '		<input type="hidden" value="'+item['code']+'"  />\n';
    	html += '	</td>\n';
			// these vary by form in use
			for (var fld in this.listFlds) {
				var theClass = this.listFlds[fld];
				if (theClass == 'image') {
					html += '	<td valign="top">'
							 +'		<img src="../images/'+item[fld]+'" width="20" height="20" align="middle">'
							 + 		item[fld] + '</td>\n';	
				}
				else {
					html += '	<td valign="top" class="'+theClass+'">'+item[fld]+'</td>\n';
				}
			}
		}
		var $theTbl = $('#showList');
		$theTbl.find('tbody').html(html);
		
		var $stripes = $theTbl.find('tbody.striped');
		$stripes.find('tr:odd td').addClass('altBG');
		$stripes.find('tr:even td').addClass('altBG2');
		
		$('.editBtn').on('click',null,$.proxy(this.doEditFields,this));
	};

List.prototype.doEditFields = function (e) {
  var code = $(e.target).next().val();
	for (var n in this.json) {
		var item = this.json[n];
	  if (item['code'] == code) {
			this.showFields(item);
			return;
		}
	}
	return false;
};
	
List.prototype.showFields = function (item) {
  $('#fieldsHdr').html(this.editHdr);
  $('#addBtn').hide();
  $('#updtBtn').show();
  $('#deltBtn').show();
  if ($('#description')) document.getElementById(this.focusFld).focus();

	$('#editTbl').find('input:not(:button):not(:submit), textarea, select').each(function () {
		var tagname = $(this).get(0).tagName;
//console.log(tagname+' with id='+this.id);		
		if (tagname == 'select') {
			$(this).val([item[this.id]]);
		}
		else {
			$(this).val(item[this.id]);
		}
	});
	$('#code').attr('readonly',true).attr('required',false);
		
	$('#codeReqd').hide();
	$('#listDiv').hide();
	$('#editDiv').show();
};
	
List.prototype.doNewFields = function () {
  document.forms['editForm'].reset();
  $('#fieldsHdr').html(this.newHdr);
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
};
	
List.prototype.doSubmitFields = function (e) {
	e.preventDefault();
	e.stopPropagation();
	var theId = $("#editForm").find('input[type="submit"]:focus').attr('id');
	switch (theId) {
		case 'addBtn':	this.doAddFields();	break;
		case 'updtBtn':	this.doUpdateFields();	break;
		case 'deltBtn':	this.doDeleteFields();	break;
	}
};
	
List.prototype.doAddFields = function () {
	$('#mode').val('addNew_'+this.dbAlias);
	var parms = $('#editForm').serialize();
	$.post(this.url, parms, $.proxy(this.addHandler,this));
	return false;
};
List.prototype.addHandler = function(response) {
	if (response.substr(0,1)=='<') {
		//console.log('rcvd error msg from server :<br />'+response);
		$('#msgArea').html(response);
		$('#msgDiv').show();
	}
	else {
		$('#msgArea').html(response);
		$('#msgDiv').show();
	  this.doBackToList();
	}
};

List.prototype.doUpdateFields = function () {
	$('#updateMsg').hide();
	$('#msgDiv').hide();
	$('#mode').val('update_'+this.dbAlias);
	var parms = $('#editForm').serialize();
	$.post(this.url, parms, $.proxy(this.updateHandler, this));
	return false;
};
List.prototype.updateHandler = function(response) {
	if (response.substr(0,1)=='<') {
		//console.log('rcvd error msg from server :<br />'+response);
		$('#msgArea').html(response);
		$('#msgDiv').show();
	}
	else {
		$('#msgArea').html(response);
		$('#msgDiv').show();
	  this.doBackToList();
	}
};
	
List.prototype.doDeleteFields = function (e) {
	var msg = this.delConfirmMsg+'\n>>> '+$('#description').val()+' <<<';
  if (confirm(msg)) {
  	var parms = {	'cat':this.dbAlias, 
									'mode':'d-3-L-3-t_'+this.dbAlias, 
									'code':$('#code').val(), 
									'description':$('#description').val() 
								};
  	$.post(this.url, parms, $.proxy(this.deleteHandler,this));
	}
	return false;
};
List.prototype.deleteHandler = function(response){
	if (($.trim(response)).substr(0,1)=='<') {
		//console.log('rcvd error msg from server :<br />'+response);
		$('#msgArea').html(response);
		$('#msgDiv').show();
	}
	else {
		$('#msgArea').html(response);
		$('#msgDiv').show();
  	this.doBackToList();
	}
};

</script>
