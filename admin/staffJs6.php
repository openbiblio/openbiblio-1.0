<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Stf extends Admin {
    constructor () {
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

    	super ( url, form, dbAlias, hdrs, listFlds, opts );

    	$('#pwdChgForm').on('submit',null,$.proxy(this.doSetStaffPwd,this));
    	$('#pwdCnclBtn').on('click',null,$.proxy(this.resetForms,this));
    	$('.actnBtns').on('click',null,$.proxy(this.doSubmitFlds,this));
    };

    resetForms () {
    	$('#pwdDiv').hide();
    	Admin.prototype.resetForms.apply( this );
    };
    fetchHandler (dataAray){
    	Admin.prototype.fetchHandler.apply( this, [dataAray] );
    	$('.pwdBtn').on('click',null,$.proxy(this.doPwd,this));
    };
    showFields ( item ) {
    	$('#pwdFldSet').hide();
    	$('.pwdFlds').attr('required',false);
    	Admin.prototype.showFields.apply( this, [item] );
    };
    addFuncBtns ( ident ) {
    	var html = '';
    	html  = Admin.prototype.addFuncBtns.apply( this, [ident] );
    	html += '		<input type="button" id="pwd'+ident+'" class="pwdBtn" value="'+<?php echo "'".T("pwd")."'"; ?>+'" />\n';
    	return html;
    };

    doNewFields () {
    	$('#pwdFldSet').show();
    	$('.pwdFlds').attr('required',true) ;
    	Admin.prototype.doNewFields.apply( this );
    };

    doSubmitFlds (e) {
console.log('in staffJs6::doSubmitFields()');
        //console.log(e);
    	e.preventDefault();
    	e.stopPropagation();
    	var theId = e.target.id;
        if (theId == 'addBtn') {
console.log('got "addBtn"');
    		var pw1 = $('#pwd1').val(),
    			pw2 = $('#pwd2').val();
//console.log('in submitFlds: pw1='+pw1+'; pw2='+pw2);
            var pwOk = this.chkPwds(pw1, pw2);
            var rolesOk = this.chkRoles();
            if (pwOk && rolesOk) {
                super.doAddFields(e);
            } else {
                return false;
            }
    	}
    };

    chkRoles () {
        var roles = $('.roles').is(':checked');
console.log(roles);
    }


    chkPwds (pw1, pw2) {
    		var errMsg = '';
//console.log('in chkPwds: pw1='+pw1+'; pw2='+pw2);
    		if ( pw1 !== pw2 ) {
    			errMsg = <?php echo "'".T("Passwords do not match.")."'"; ?>;
    		} else if (!pw1 || !pw2) {
    			errMsg = <?php echo "'".T("Passwords may not be empty.")."'"; ?>;
    		}
    		if (errMsg != '') {
console.log(errMag);
    			$('#msgArea').html(errMsg).show();
    			$('#msgDiv').show();
    			return false;
    		}
    		return true;
    };

    doPwd (e) {
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

    doSetStaffPwd (e) {
    	e.preventDefault();
    	e.stopPropagation();
		var pwd1 = $('#pwdA').val(),
			pwd2 = $('#pwdB').val();
    	if (!this.chkPwds(pwd1, pwd2))
            return false;

        var parms = {'cat': 'staff',
    				 'mode': 'setPwd_staff',
    				 'pwd': pwd1,
    				 'pwd2': pwd2,
    				 'userid':this.crntUser };
        $.post(this.url, parms, $.proxy(this.setHandler,this));
    	return false;
    };
    setHandler (response){
    	this.showResponse(response);
    };
}

$(document).ready(function () {
	var xxxx = new Stf();
});
</script>
