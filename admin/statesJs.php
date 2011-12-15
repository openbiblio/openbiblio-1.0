<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function St ( url, form, dbAlias, hdrs, listFlds, opts ) {
	List.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
St.prototype = inherit(List.prototype);
St.prototype.constructor = St;

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'states';
	var hdrs = {'listHdr':<?php echo '"'.T("List of States & Abreviations").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit State & Abreviation").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New State & Abreviation").'"'; ?>,
						 };
	var listFlds = {'code':'text',
									'description':'text',
									'default_flg':'center',
								 };
	var opts = {};
						 
	var xxxx = new St( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
});
</script>
