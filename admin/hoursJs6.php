<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Hour extends Admin {
	constructor () {
        list.getSites(); 					// make simple list, no formatting or placing
        list.getDays(); 					// make simple list, no formatting or placing
    	list.getSiteList($('#siteid'));  	// place options for pull-down select
    	list.getDayList($('#day'));  		// place for pull-down select

		var url = '../admin/adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'hours';
		var hdrs = { 'listHdr':		<?php echo '"'.T("Hours - AddNew").'"'; ?>,
		             'editHdr':		<?php echo '"'.T("Hours - Edit").'"'; ?>,
			         'newHdr':		<?php echo '"'.T("Hours - AddNew").'"'; ?>
					};
		var listFlds = {'siteid':				'text',
						'day': 					'text',
						'start_time':			'text',
						'end_time':				'text',
						'by_appointment':		'text',
						'effective_start_date':	'text',
						'effective_end_date':	'text',
						'public_note':			'text',
						'private_note':			'text'
	    				};

	    var opts = { 'focusFld':'day',
					 'keyFld':'hourid'
					};

	    super( url, form, dbAlias, hdrs, listFlds, opts );

		this.noshows = [];
	};

	doNewFields (e) {
    	super.doNewFields.apply(this);
		$('#editDiv').show();
	};

	doGatherParams () {
		var params = $('#editForm').serializeArray();
		for (var i = 0, len = params.length; i < len; i++) {
			if ((params[i].name === 'start_time') || (params[i].name === 'end_time')) {
				params[i].value = params[i].value.replace(':', '')
    		} else if ((params[i].name === 'by_appointment')) {
				params[i].value = ('on' == params[i].value) ? 1 : 0;
			}
		}
		return params;
	};

    fetchHandler (dataAray) {    // adds functionality to base class Admin
        super.fetchHandler(dataAray);
        $('#showList tbody tr').each(function() {
            var where = $(this).find("td").eq(1);
            var siteid = where.html();
            var siteName = list.sites[siteid];
            //console.log(siteid+' ==> '+siteName);
            where.html(siteName);

            var where = $(this).find("td").eq(2);
            var dayid = where.html();
            var dayName = list.days[dayid];
            console.log(dayid+' ==> '+dayName);
            where.html(dayName);
        });
    };
}

$(document).ready(function () {
	var xxxx = new Hour();
});

</script>
