<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Val extends Admin {
    constructor ( url, form, dbAlias, hdrs, listFlds, opts ) {
    	var url = '../tools/toolSrvr.php',
    		form = $('#editForm'),
    		dbAlias = 'validation';
    	var hdrs = {'listHdr':<?php echo '"'.T("List of Input Validation Patterns").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Edit Patterns").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add New Pattern").'"'; ?>,
    			   };
    	var listFlds = {'code':'text',
    					'description':'text',
    					'pattern':'textarea',
    				   };
    	var opts = {};

    	super ( this, url, form, dbAlias, hdrs, listFlds, opts );
	    this.noshows = [];
    };

};

$(document).ready(function () {
	var val = new Val();
});
</script>
