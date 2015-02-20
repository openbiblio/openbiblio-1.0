<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Sit ( url, form, dbAlias, hdrs, listFlds, opts ) {
	List.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Sit.prototype = inherit(List.prototype);
Sit.prototype.constructor = Sit;
Sit.prototype.init = function () {
	this.noshows = [];
	List.prototype.init.apply( this );
	//this.noshows.push(this.keyFld);
};

Sit.prototype.fetchStates = function () {
  $.getJSON(this.url,{ 'cat':'states', 'mode':'getAll_states' }, function(data){
		var html = '';
		for (var nstate in data) {
    	html += '<option value="'+data[nstate]['code']+'"';
    	if (data[nstate]['default_flg'] == 'Y') {
    		html += ' selected';
			}
   		html += '>'+data[nstate]['description']+'</option>\n';
		}
		$('#state').html(html);
	});
};
Sit.prototype.fetchCalendars = function () {
  $.getJSON(this.url,{ 'cat':'sites', 'mode':'getAllCalendars' }, function(data){
		var html = '';
		for (var ncal in data) {
    	html += '<option value="'+data[ncal]['code']+'"';
    	if (data[ncal]['default_flg'] == 'Y') {
    		html += ' selected';
			}
   		html += '">'+data[ncal]['description']+'</option>\n';
		}
		$('#calendar').html(html);
	});
};

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'sites';
	var hdrs = {'listHdr':<?php echo '"'.T("List of Sites").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit Site").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New Site").'"'; ?>,
						 };
	var listFlds = {'name': 'text',
									'code': 'text',
									'city':'text',
								 };
	var opts = { 'focusFld':'name', 'keyFld':'siteid' };
						 
	var xxxx = new Sit( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
	//xxxx.fetchSites();
	xxxx.fetchStates();
	xxxx.fetchCalendars();
});
</script>
