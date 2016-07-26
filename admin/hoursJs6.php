<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Hour extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
    		form = $('#editForm'),
    		dbAlias = 'hours';
    	var hdrs = {'listHdr':<?php echo '"'.T("List of Hours").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Edit Hours").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add New Hours").'"'; ?>,
    						 };
    	var listFlds = {'name': 'text',
    					'code': 'text',
    					'city':'text',
    				   };
    	var opts = { 'focusFld':'day', 'keyFld':'hourid' };

	super( url, form, dbAlias, hdrs, listFlds, opts );

    	this.noshows = [];

        list.getSiteList($('#site'));
        list.getDayList($('#day'));
    };

    doNewFields (e) {
        super.doNewFields.apply(this);
    	$('#editDiv').show();
    }
}

$(document).ready(function () {
	var xxxx = new Hour();
});

</script>
