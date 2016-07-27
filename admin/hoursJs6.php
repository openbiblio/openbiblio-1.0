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
    		var listFlds = {'site': 'text',
    			'day': 'text',
    			'start_time':'text',
    			'end_time':'text',
    			'by_appointment':'text',
    			'effective_start_date':'text',
    			'effective_end_date':'text',
    			'public_note':'text',
    			'private_note':'text',
		};
		var opts = { 'focusFld':'day', 'keyFld':'hourid' };

		super( url, form, dbAlias, hdrs, listFlds, opts );

    		this.noshows = [];

        	list.getSiteList($('#siteid'));
        	list.getDayList($('#day'));
    	};

    	doNewFields (e) {
        	super.doNewFields.apply(this);
    		$('#editDiv').show();
    	}

    	doGatherParams () {
		var params = $('#editForm').serializeArray();
		for (var i = 0, len = params.length; i < len; i++) {
			if ((params[i].name === 'start_time') || (params[i].name === 'end_time')) {
				params[i].value = params[i].value.replace(':', '')
    			} else if ((params[i].name === 'by_appointment')) {
				params[i].value = ('on' == params[i].value) ? 1 : 0;
			}
		}
		return jQuery.param(params);
    	}
}

$(document).ready(function () {
	var xxxx = new Hour();
});

</script>
