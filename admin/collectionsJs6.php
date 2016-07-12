<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Col extends Admin {
    constructor ( ) {
	   var url = '../admin/adminSrvr.php',
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

	    super( url, form, dbAlias, hdrs, listFlds, opts );
    	this.noshows = [];

	    this.fetchTypes();

        $('#type').on('change',null,$.proxy(function () {
      	     this.setTypeDisplay();
        },this));
    };

    setTypeDisplay () {
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

    fetchTypes () {
        $.post(this.url,{ 'cat':'collect', 'mode':'getType_collect' }, $.proxy(this.typeHandler,this), 'json');
    };
    typeHandler (data){
		var html = '';
		for (var item in data) {
			//console.log(data[item]);
    	   html += '<option value="'+item+'"';
   		   html += '">'+item+'</option>\n';
		}
		$('#type').html(html);
    	this.fetchCircList();
    };

    fetchCircList () {
        $.post(this.url,{ 'cat':'collect', 'mode':'getCirc_collect' }, $.proxy(this.circHandler,this), 'json');
    };
    circHandler (data){
        this.circList = data;
        this.fetchDistList();
    };
    getCirc (code) {
    	for (var item in this.circList) {
    		if (this.circList[item]['code'] == code) {
    			return this.circList[item];
    		}
    	}
    };

    fetchDistList () {
        $.post(this.url,{ 'cat':'collect', 'mode':'getDist_collect' }, $.proxy(this.distHandler,this), 'json');
    };
    distHandler (data){
      	this.distList = data;
    };
    getDist(code) {
    	for (var item in this.distList) {
    		if (this.distList[item]['code'] == code) {
    			return this.distList[item];
    		}
    	}
    };

    doEditFields(e) {
    	var lclThis = this;
    	super.doEditFields.apply( this, [e] );
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
}

$(document).ready(function () {
    var xxxx = new Col;
});

</script>
