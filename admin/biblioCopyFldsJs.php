<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

/* Note: Much of the functionality of this module is provided by '.../classes/AdminJs.php' */

function Bcf ( url, form, dbAlias, hdrs, listFlds, opts ) {
	Admin.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Bcf.prototype = inherit(Admin.prototype);
Bcf.prototype.constructor = Bcf;
Bcf.prototype.init = function () {
	this.noshows = [];
	Admin.prototype.init.apply( this );
};

$(document).ready(function () {
	var url = '../admin/adminSrvr.php',
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
