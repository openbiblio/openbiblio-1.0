<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class St extends Admin {
    constructor ( url, form, dbAlias, hdrs, listFlds, opts ) {
	    super( url, form, dbAlias, hdrs, listFlds, opts );
    	this.noshows = [];
    };
}

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
});

</script>
