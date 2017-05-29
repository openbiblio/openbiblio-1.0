<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Stf extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
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
						'start_page':'center',
    				   };
    	var opts = { 'keyFld':'userid', 'focusFld':'last_name' };

    	super ( url, form, dbAlias, hdrs, listFlds, opts );

    	$('#pwdChgForm').on('submit',null,$.proxy(this.doSetStaffPwd,this));
    	$('#pwdCnclBtn').on('click',null,$.proxy(this.resetForms,this));
    };

    resetForms () {
        super.resetForms();
    	$('#pwdDiv').hide();
        $('#pwdCnclBtn').val(<?php echo "'".T("Cancel")."'"; ?>);
    };
    fetchHandler (dataAray){
    	super.fetchHandler(dataAray);
    	$('.pwdBtn').on('click',null,$.proxy(this.doPwd,this));
    };
    showFields ( item ) {
    	super.showFields(item);
    	$('#pwdFldSet').hide();
    	$('.pwdFlds').attr('required',false);
    };
    addFuncBtns ( ident ) {
    	var html = '';
    	html  = super.addFuncBtns(ident);
    	html += '		<input type="button" id="pwd'+ident+'" class="pwdBtn" value="'+<?php echo "'".T("pwd")."'"; ?>+'" />\n';
    	return html;
    };

    doNewFields () {
    	super.doNewFields();
    	$('#pwdFldSet').show();
    	$('.pwdFlds').attr('required',true) ;
    };

    doAddBtn () {
console.log('in staffJs6::doAddBtn()');
	    //console.log('got "addBtn"');
		let pw1 = $('#xpwd1').val();
		let pw2 = $('#xpwd2').val();
console.log("pw1=<"+pw1+">");
console.log("pw2=<"+pw2+">");
        var pwOk = this.chkPwds(pw1, pw2);
        var rolesOk = this.chkRoles();
		//console.log('rolesOk = '+rolesOk);
        if (rolesOk && pwOk) {
            this.doAddFields();
        } else {
console.log('failed a validation chk')
            return false;
        }
    };

    chkRoles () {
        var roles = $('.roles').is(':checked');
        console.log('in staffJs6::chkRoles()');
        if (!roles) {
            var errMsg = '<?php echo T("Role MUST be selected"); ?>';
    		$('#userMsg').html(errMsg).show();
    		$('#msgDiv').show();
    	}
    	return roles;
    }

    chkPwds (pw1, pw2) {
		var errMsg = '';
		if ( pw1 != pw2 ) {
			errMsg = <?php echo "'".T("Passwords do not match.")."'"; ?>;
		} else if (!pw1 || !pw2) {
			errMsg = <?php echo "'".T("Passwords may not be empty.")."'"; ?>;
		}
		if (errMsg != '') {
            console.log(errMsg);
			alert(errMsg);
			$('#userMsg').html(errMsg).show();
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
        $('#pwdCnclBtn').val(<?php echo "'".T("Go Back")."'"; ?>);
    };
}

$(document).ready(function () {
	var xxxx = new Stf();
});
</script>
