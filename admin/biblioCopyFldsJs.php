<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Bcf ( url, form, dbAlias, hdrs, listFlds, opts ) {
	List.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Bcf.prototype = inherit(List.prototype);
Bcf.prototype.constructor = Bcf;

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'copyFlds';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Custom Copy Fields").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit Custom Copy Fields").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New Custom Copy Fields").'"'; ?>,
						 };
	var listFlds = {'code':'text',
									'description':'text',
								 };
	var opts = {};
						 
	var xxxx = new Bcf( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
});
</script>
