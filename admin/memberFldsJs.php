<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Mbf ( url, form, dbAlias, opts ) {
	List.call( this, url, form, dbAlias, opts );
};
Mbf.prototype = inherit(List.prototype);
Mbf.prototype.constructor = Mbf;

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'mbrFlds';
	var opts = {'listHdr':<?php echo '"'.T("Custom Member Fields").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Editing Custom Fields").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add new custom field").'"'; ?>,
						 };
						 
	var mbrFlds = new Mbf( url, form, dbAlias, opts );
	mbrFlds.init();
});
</script>
