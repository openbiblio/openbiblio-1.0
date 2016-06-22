<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Val ( url, form, dbAlias, hdrs, listFlds, opts ) {
	Admin.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Val.prototype = inherit(Admin.prototype);
Val.prototype.constructor = Val;
Val.prototype.init = function () {
	this.noshows = [];
	Admin.prototype.init.apply( this );
	//this.noshows.push(this.keyFld);
};

$(document).ready(function () {
	var url = 'toolSrvr.php',
			form = $('#editForm'),
			dbAlias = 'validation';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Input Validation Patterns").'"'; ?>,
							'editHdr':<?php echo '"'.T("Edit Patterns").'"'; ?>,
							'newHdr':<?php echo '"'.T("Add New Pattern").'"'; ?>,
						 };
	var listFlds = {'code':'text',
									'description':'text',
									'pattern':'textarea',
								 };
	var opts = {};
						 
	var xxxx = new Val( url, form, dbAlias, hdrs, listFlds, opts );

	xxxx.init();
});
</script>
