<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Mbf extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
    		form = $('#editForm'),
    		dbAlias = 'mbrFlds';
    	var hdrs = {'listHdr':<?php echo '"'.T("Custom Member Fields").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Editing Custom Fields").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add new custom field").'"'; ?>,
    						 };
    	var listFlds = {'code':'text',
    					'description':'text',
//    					'default_flg':'center',
    								 };
    	var opts = {};

	    super( url, form, dbAlias, hdrs, listFlds, opts );
    	this.noshows = [];
    };
}

$(document).ready(function () {
	var xxxx = new Mbf();
});

</script>
