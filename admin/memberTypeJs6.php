<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Mbc extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
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

	   super( url, form, dbAlias, hdrs, listFlds, opts );
    };
}

$(document).ready(function () {
	var xxxx = new Mbc();
});
</script>
