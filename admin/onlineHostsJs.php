<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Hed ( url, form, dbAlias, hdrs, listFlds, opts ) {
	Admin.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Hed.prototype = inherit(Admin.prototype);
Hed.prototype.constructor = Hed;

Hed.prototype.fetchServiceList = function () {
		$.getJSON(this.url, {'cat':'hosts', 'mode':'getSvcs_hosts'}, $.proxy(this.serviceHandler,this));
};
Hed.prototype.serviceHandler = function (data) {
	var html = '';
	for (var n in data) {
		html += '<option value="'+data[n]+'">'+data[n]+'</option>\n';
	}
	$('#service').html(html);
};

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'hosts';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Hosts").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit Hosts").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New Host").'"'; ?>,
						 };
	var listFlds = {'seq':'number',
									'active':'center',
									'host':'text',
									'name':'text',
									'db':'text',
									'user':'text',
									'pw':'password',
								 };
	var opts = { 'focusFld':'name', 'keyFld':'id' };
						 
	var xxxx = new Hed( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
	xxxx.fetchServiceList();
});
</script>
