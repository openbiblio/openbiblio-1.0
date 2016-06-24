<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
"use strict";

class Med extends Admin {
    constructor () {
    	var url = 'adminSrvr.php',
    		form = $('#editForm'),
    		dbAlias = 'media';
    	var hdrs = {'listHdr':<?php echo '"'.T("List of Media Types").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Edit Media").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add New Media").'"'; ?>,
    			   };
    	var listFlds = {'code': 'number',
    					'description': 'text',
    					'adult_checkout_limit': 'number',
    					'juvenile_checkout_limit': 'number',
    					'image_file': 'image',
    					'count': 'number',
    					'srch_disp_lines': 'number',
    					'default_flg': 'center'
    				   };
    	var opts = {};

	    super( url, form, dbAlias, hdrs, listFlds, opts );
    };

}

$(document).ready(function () {
	var xxxx = new Med( );
});
</script>
