<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Mbc ( url, form, dbAlias, hdrs, listFlds, opts ) {
	Admin.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Mbc.prototype = inherit(Admin.prototype);
Mbc.prototype.constructor = Mbc;

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'mbrTypes';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Member Types").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit Member Type").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New Member Type").'"'; ?>,
						 };
	var listFlds = {'description': 'text',
									'max_fines': 'number',
									'default_flg': 'center',
								 };
	var opts = {};
						 
	var xxxx = new Mbc( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
});
</script>
