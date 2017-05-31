<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

/* Note: Much of the functionality of this module is provided by '.../classes/JSAdmin.php' */
class Bcf extends Admin {
    constructor () {
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

	    super( url, form, dbAlias, hdrs, listFlds, opts );
    	this.noshows = [];
    };
}

$(document).ready(function () {
	var xxxx = new Bcf();
});

</script>
