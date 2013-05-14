<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

function Col ( url, form, dbAlias, hdrs, listFlds, opts ) {
	List.call( this, url, form, dbAlias, hdrs, listFlds, opts );
};
Col.prototype = inherit(List.prototype);
Col.prototype.constructor = Col;
Col.prototype.init = function () {
	List.prototype.init.apply( this );
  $('#type').on('change',null,$.proxy(function () {
  	this.setTypeDisplay();
	},this));
};

Col.prototype.setTypeDisplay = function () {
	var type = $('#type').val();
	if (type == 'Circulated') {
		$('.distOnly').hide().removeAttr('required');
		$('.circOnly').show().attr('required','true');
	}
	else if (type == 'Distributed') {
		$('.circOnly').hide().removeAttr('required');
		$('.distOnly').show().attr('required','true');
	}
	else {
		$('#msgArea').html('Invalid Collection Type');
		$('#msgDiv').show();
	}
};

Col.prototype.fetchTypes = function () {
  $.getJSON(this.url,{ 'cat':'collect', 'mode':'getType_collect' }, $.proxy(this.typeHandler,this));
};
Col.prototype.typeHandler = function(data){
		var html = '';
		for (var item in data) {
			//console.log(data[item]);
    	html += '<option value="'+item+'"';
   		html += '">'+item+'</option>\n';
		}
		$('#type').html(html);
		this.fetchCircList();
};

Col.prototype.fetchCircList = function () {
  $.getJSON(this.url,{ 'cat':'collect', 'mode':'getCirc_collect' }, $.proxy(this.circHandler,this));
};
Col.prototype.circHandler = function(data){
  this.circList = data;
  this.fetchDistList();
};
Col.prototype.getCirc = function (code) {
	for (var item in this.circList) {
		if (this.circList[item]['code'] == code) {
			return this.circList[item];
		}
	}
};

Col.prototype.fetchDistList = function () {
  $.getJSON(this.url,{ 'cat':'collect', 'mode':'getDist_collect' }, $.proxy(this.distHandler,this));
};
Col.prototype.distHandler = function(data){
  	this.distList = data;
};
Col.prototype.getDist = function (code) {
	for (var item in this.distList) {
		if (this.distList[item]['code'] == code) {
			return this.distList[item];
		}
	}
};

Col.prototype.doEditFields = function (e) {
	var lclThis = this;
	List.prototype.doEditFields.apply( this, [e] );
  lclThis.setTypeDisplay();
	var circ = this.getCirc(this.crnt);
	if (circ) {
		$('#days_due_back').val(circ['days_due_back']);
		$('#daily_late_fee').val(circ['daily_late_fee']);
	}
	var dist = this.getDist(this.crnt);
	if (dist) {
		$('#restock_threshold').val(dist['restock_threshold']);
	}
};

$(document).ready(function () {
	var url = 'adminSrvr.php',
			form = $('#editForm'),
			dbAlias = 'collect',
			hdrs = {'listHdr':<?php echo '"'.T("List of Collections").'"'; ?>, 
							'editHdr':<?php echo '"'.T("Edit Collection").'"'; ?>, 
							'newHdr':<?php echo '"'.T("Add New Collection").'"'; ?>,
						 },
			listFlds = {'code': 'number',
									'description':'text',
									'type':'text',
									'count':'number',
									'default_flg':'center',
								 },
			opts = {};
						 
	var xxxx = new Col( url, form, dbAlias, hdrs, listFlds, opts );
	xxxx.init();
	xxxx.fetchTypes();
});
</script>
