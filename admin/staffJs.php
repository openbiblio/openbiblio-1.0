<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Stf ( url, form, dbAlias, hdrs, listFlds, opts ) {
	List.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Stf.prototype = inherit(List.prototype);
Stf.prototype.constructor = Stf;

Stf.prototype.init = function () {
	List.prototype.init.apply( this );
	$('#pwdChgForm').on('submit',null,$.proxy(this.doSetStaffPwd,this));
	$('#pwdCnclBtn').on('click',null,$.proxy(this.resetForms,this));
};
Stf.prototype.resetForms = function () {
	$('#pwdDiv').hide();
	List.prototype.resetForms.apply( this );
};
Stf.prototype.fetchHandler = function(dataAray){
	List.prototype.fetchHandler.apply( this, [dataAray] );
	$('.pwdBtn').on('click',null,$.proxy(this.doPwd,this));
};
Stf.prototype.showFields = function ( item ) {
	$('#pwdFldSet').hide();
	$('.pwdFlds').attr('required',false);
	List.prototype.showFields.apply( this, [item] );
};
Stf.prototype.addFuncBtns = function ( ident ) {
	var html = '';	
	html  = List.prototype.addFuncBtns.apply( this, [ident] );
	html += '		<input type="button" id="pwd'+ident+'" class="pwdBtn" value="'+<?php echo "'".T("pwd")."'"; ?>+'" />\n';
	return html;
};
Stf.prototype.doNewFields = function () {
	$('#pwdFldSet').show();
	$('.pwdFlds').attr('required',true) ;
	List.prototype.doNewFields.apply( this );
};
Stf.prototype.doPwd = function (e) {
	  var code = $(e.target).prev().val();
		for (var n in this.json) {
			var item = this.json[n];
		  if (item['userid'] == code) {
				this.crntUser = code;
				$('#pwdDiv fieldset legend span').html(item.username);
  			$('#pwdChgForm input:visible:first').focus(); 
				$('#listDiv').hide();
				$('#editDiv').hide();
				$('#pwdDiv').show();
				break;
			}
		}
		return false;
};
Stf.prototype.chkPwds = function (pwd1,pwd2) {
		var pw1 = $('#'+pwd1).val(),
				pw2 = $('#'+pwd2).val(),
				errMsg = '';
		if ( pw1 !== pw2 ) {
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
};
Stf.prototype.doSetStaffPwd = function (e  ) {
	e.preventDefault();
	e.stopPropagation();
	if (!this.chkPwds('pwdA','pwdB')) return false;
  var parms = {	'cat':'staff',
								'mode':'setPwd_staff', 
								'pwd':$('#pwdA').val(), 
								'pwd2':$('#pwdB').val(), 
								'userid':this.crntUser };
  $.post(this.url, parms, $.proxy(this.setHandler,this));
	return false;
};
Stf.prototype.setHandler = function(response){
	this.showResponse(response);
};

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'staff';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Staff Members").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit Staff Member").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New Staff Member").'"'; ?>,
						 };
	var listFlds = {'last_name':'text',
									'first_name':'text',
									'username':'text',
									'circ_flg':'center',
									'circ_mbr_flg':'center',
									'catalog_flg':'center',
									'reports_flg':'center',
									'admin_flg':'center',
									'tools_flg':'center',
									'suspended_flg':'center',
								 };
	var opts = { 'keyFld':'userid' };
						 
	var xxxx = new Stf( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
});
</script>
