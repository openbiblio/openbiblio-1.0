<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Sit ( url, form, dbAlias, hdrs, listFlds, opts ) {
	Admin.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Sit.prototype = inherit(Admin.prototype);
Sit.prototype.constructor = Sit;
Sit.prototype.init = function () {
	this.noshows = [];
	Admin.prototype.init.apply( this );
	//this.noshows.push(this.keyFld);
  $('#country').val('xxxx');
};

Sit.prototype.fetchStates = function () {
    list.getPullDownList('State', $('#state'));
};
Sit.prototype.fetchCalendars = function () {
    list.getPullDownList('Calendar', $('#calendar'));
};
Sit.prototype.doNewFields = function (e) {
    var localeCntry = navigator.language.slice(-2);
console.log("country is "+localeCntry);

    Admin.prototype.doNewFields.apply(this);
console.log('got here');
    $('#country').val(localeCntry);
	$('#editDiv').show();
}

$(document).ready(function () {
	var url = 'adminSrvr.php',
		form = $('#editForm'),
		dbAlias = 'sites';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Sites").'"'; ?>, 
				'editHdr':<?php echo '"'.T("Edit Site").'"'; ?>, 
				'newHdr':<?php echo '"'.T("Add New Site").'"'; ?>,
						 };
	var listFlds = {'name': 'text',
					'code': 'text',
					'city':'text',
				   };
	var opts = { 'focusFld':'name', 'keyFld':'siteid' };
						 
	var xxxx = new Sit( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
	xxxx.fetchStates();
	xxxx.fetchCalendars();
});
</script>
