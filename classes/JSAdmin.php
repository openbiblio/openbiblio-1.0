<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document - JSAdmin.php
"use strict"

/* ************************************************************************** */
/* Base class for DB lookup table maintenance
 *   url: data server URL
 *   form: id of html form to use
 *   dbAlias: nickname of server database
 *   opts: js object containing as a minimum: listHdr, editHdr, newHdr
 *
 * @author Fred LaPlante, June 2016
 */

class Admin {
    constructor ( url, form, dbAlias, hdrs, listFlds, opts ) {
    	this.url = url;
    	this.editForm = form;
    	this.dbAlias = dbAlias;
    	this.listHdr = hdrs.listHdr;
    	this.editHdr = hdrs.editHdr;
    	this.newHdr = hdrs.newHdr;
    	this.listFlds = listFlds;
    	this.keyFld = opts.keyFld || 'code';
    	this.focusFld = opts.focusFld || 'description';
    	this.noshows = opts.noshows || [];
    	this.noshows.push(this.keyFld);

        this.delConfirmMsg = <?php echo '"'.T("Are you sure you want to delete ").'"'; ?>;
        this.cancelStr = <?php echo '"'.T("Cancel").'"'?>;

    	this.initWidgets();

	    $('.newBtn').click(function(e) {e.preventDefault();});
    	$('.newBtn').on('click',null,$.proxy(this.doNewFields,this));
    	$('.actnBtns').on('click',null,$.proxy(this.doSubmitFields,this));
    	$('#cnclBtn, .cnclBtn').on('click',null,$.proxy(this.doBackToList,this));

		$('input:visible:first').focus();   // set focus to first input element of each form on page

    	this.fetchList();
    	this.resetForms()
    };

	//------------------------------
    initWidgets () {
    };

    resetForms () {
 		obib.hideMsg('now');
    	$('#editDiv').hide();
    	$('#extraDiv').hide();
        $('#listHdr').html(this.listHdr);
        $('#editHdr').html(this.editHdr);
        $('#cnclBtn').val('Go Back');
    	$('#listDiv').show();
    };

    doBackToList () {
		this.fetchList();
		this.resetForms();
    };
    backHandler (e) {
    	this.resetForms();
    	this.fetchList();
    };

	//------------------------------
    fetchList () {
    	var params = { 'cat':this.dbAlias,
    				   'mode':'getAll_'+this.dbAlias,
    				};
        $.post( this.url, params, $.proxy(this.fetchHandler,this), 'json');
    };

	// construct the initial 'List' screen from database table content
    fetchHandler (dataAray) {
        this.json = dataAray;	// will be re-used later for editing

    	var $theTbl = $('#showList'),
    		$theList = $theTbl.find('tbody'),
    		html = '',
    		item, ident,
    		test = '';

    	$theList.html('');

    	for (var obj in dataAray) {
    		item = dataAray[obj],
    		ident = item[this.keyFld];
    		// these are static in all rows
       	    html  = '<tr>\n';
     		html += '	<td valign="top">\n';
       	    html += this.addFuncBtns( ident );
       	    html += '	</td>\n';

    		// these vary by form in use
    		for (var fld in this.listFlds) {
    			var theClass = this.listFlds[fld];
				//console.log('fld type='+theClass+'; with value='+item[fld]);

    			if (theClass == 'image') {
					if (item[fld] == 'null') item[fld] = 'shim.gif';
    				html += '	<td valign="top">'
    					 +'		<img src="../images/'+item[fld]+'" width="20" height="20" align="middle">'
    					 + 		item[fld] + '</td>\n';
    			}
    			else if (theClass == 'textarea') {
    				html += '	<td valign="top" class="'+theClass+'">'
    					 +  '		<textarea cols="100" readonly>'+item[fld]+'</textarea></td>\n';
    			}
				else if (theClass == 'bool') {
					// does not seem to be working - FL 4 Jun 2017
					let val = (item[fld] == '1')? 'Yes': 'No';
					//consloe.log(val);
    				html += '	<td valign="top" class="'+theClass+'" readonly>'+ val +'</td>\n';
				}
    			else {
    				html += '	<td valign="top" class="'+theClass+'">'+item[fld]+'</td>\n';
    			}
    		}
    		html += '</tr>\n';
    		$theList.append(html);
    		$('#row'+ident).on('click',null,$.proxy(this.doEditFields,this));
    	}

    	var $stripes = $theTbl.find('tbody.striped');
    	$stripes.find('tr:odd td').addClass('altBG');
    	$stripes.find('tr:even td').addClass('altBG2');
    };
    addFuncBtns (ident) {
    	var html = '';
    	html  = '		<input type="button" id="row'+ident+'" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
    	html += '		<input type="hidden" value="'+ident+'"  />\n';
    	return html;
    };
       
	// fill out the contents of the 'edit' screen from data previously downloaded
    doEditFields (e) {
		//console.log('in doEditfields()');
		/* get id from field adjacent to 'edit' button of list screen */
        var code = $(e.target).next().val();
    	var ident = this.keyFld;
		/* now locate data for desired object and build screen with it */
    	for (let n in this.json) {
    		var item = this.json[n];
    	    if (item[ident] == code) {
    			this.showFields(item);
    			this.crnt = code;
    			return false;
    		}
    	}
    	return false;
    };
    showFields (rec) {
		//console.log('process '+item+' in showFields()');
        $('#fieldsHdr').html(this.editHdr);
        $('#addBtn').hide();
        $('#updtBtn').show().enable();
        $('#deltBtn').show().enable();

		/* scan form and set values of fields using corresponding data entries  */
    	$('#editTbl').find('input:not(:button):not(:submit):not(:password), textarea, select').each(function () {
			var $this = $(this);

    		if ($this.is('[type=radio]')) {
    			$this.val([rec[this.name]]);
    		} else {
 				/* despite documentation, this seems to be needed for checkboxes, and works for others too. */
				$this.val([rec[this.id]]);
    		}

            /* key field must be static for updates (marked 'addOnly' in html) */
            var theClass = $this.get(0).className;
            if (theClass == 'addOnly') {
                $this.attr('readOnly',true);
            }
    	});

    	for (var n in this.noshows){
    		$('#'+this.noshows[n]).attr('required',false).hide();
    	};

    	$('#codeReqd').hide();
    	$('#listDiv').hide();
    	$('#editDiv').show();
    };
	
    doNewFields () {
		//console.log('in JSAdmin::doNewFields()');
    	//e.preventDefault();
    	//e.stopPropagation();
		$('#addBtn').enable();
		$('#updtBtn').enable();
		$('#deltBtn').enable();

        document.getElementById('editForm').reset();
        $('#fieldsHdr').html(this.newHdr);
    	for (var n in this.noshows){
    		$('#'+this.noshows[n]).attr('readonly',true).attr('required',false).hide();
    	};
        $('#editTbl').find('input.addOnly').removeAttr('readonly');
    	$('#codeReqd').show();
    	$('#deltBtn').hide();
    	$('#updtBtn').hide();
        $('#addBtn').show();

    	$('#listDiv').hide();
        $('#editForm input:visible:first').focus();
    	$('#editDiv').show();
    	return false;
    };
	
    doSubmitFields (e) {
		//console.log('in JSAdmin::doSubmitFields()');
    	var theBtn = e.target.id;
    	switch (theBtn) {
    		case 'addBtn':	this.doAddBtn(e);	break;
    		case 'updtBtn':	this.doUpdtBtn(e);  break;
    		case 'deltBtn':	this.doDeltBtn(e);  break;
    		default: obib.showError("'"+theBtn+"' is not a valid action button id");
    	}
    };
	doAddBtn (e) {
		//console.log('in JSAdmin::addBtn(): '+e.target.id)
        this.doAddFields(e)
    }
	doUpdtBtn (e) {
        this.doUpdateFields(e)
    }
	doDeltBtn (e) {
        this.doDeleteFields(e);
    }

    doGatherParams () {
        return $('#editForm').serializeArray();
    };
    doAssembleParams (params) {
		var numParams = params.length-1;
        for (var i=0; i<numParams; i++) {
			if ((typeof params[i] !== "undefined") && (typeof params[i].value !== "undefined")) {
	            if (params[i].value.length < 1) {
	                 params.splice(i, 1);
            	}
			}
        }
		return jQuery.param(params);
    };

    doAddFields (e) {
		//console.log('in JSAdmin::doAddFields(): '+e.target.id)
		let f = document.getElementById('editForm');
		if(f.reportValidity()) {
			//console.log('all validations pass');
    		obib.hideMsg('now');
		} else {
			//console.log('some validation(s) fail');
			obib.showError('some validation(s) fail');
			return;
		}
    	e.preventDefault();
    	e.stopPropagation();

    	$('#mode').val('addNew_'+this.dbAlias);
    	$('#cat').val(this.dbAlias);
    	var parms = this.doGatherParams();
		parms = this.doAssembleParams(parms)
    	$.post(this.url, parms, $.proxy(this.addHandler, this), 'json');
		$('#addBtn').enable();
		return false;
    };
	addHandler (response) {
		$('#addBtn').disable();
		this.showResponse(response);
	};

    doUpdateFields (e) {
		$('#updtBtn').enable();
		obib.hideMsg();
    	$('#mode').val('update_'+this.dbAlias);
    	$('#cat').val(this.dbAlias);
		var parms = this.doGatherParams();
    	if ($('#newImageFile').val() != '') {
    		parms.push($('#newImageFile').serializeArray());
		}
    	$.post(this.url, this.doAssembleParams(parms), $.proxy(this.updateHandler, this), 'json');
    	e.preventDefault();
    	e.stopPropagation();
    	return false;
    };
    updateHandler (response) {
		$('#updtBtn').disable();
   		this.showResponse(response);
    };
	
    doDeleteFields (e) {
		$('#deltBtn').enable();
    	let msg = this.delConfirmMsg+'\n>>> '+$('#'+this.focusFld).val()+' <<<';
        if (confirm(msg)) {
      	   let parms = {'cat':this.dbAlias,
    					'mode':'d-3-L-3-t_'+this.dbAlias,
    					'code':$('#'+this.keyFld).val(),
    					'description':$('#description').val(),
    					};
    		parms[this.keyFld] = $('#'+this.keyFld).val();
      	    $.post(this.url, parms, $.proxy(this.deleteHandler,this));
    		e.preventDefault();
    		e.stopPropagation();
    	}
    	return false;
    };
    deleteHandler (response) {
		var svrResponse = '';
		if ((($.trim(response)).indexOf('ompleted') > -1) || (($.trim(response)).indexOf('eleted') > -1)) {
			svrResponse = 'Success - '+response;
		}
        $('#deltBtn').disable();
		$('#updtBtn').disable();
    	this.showResponse(svrResponse);
    };

    showResponse (response) {
console.log('in JSAdmin::showResponse(): response='+response);
		var userMsg = '';
		var	seqNum = 0;
		//console.log('initial userMsg= '+userMsg);

		if (response.indexOf(',') >= 0) {
console.log('got a CSV thing');
			//let parts = response.split(',');
			//userMsg = parts[1];
			userMsg = response;
		} else if ( $.isArray(response) ) {
console.log('got an array')
			seqNum = response[0];
			userMsg = response[1];
		} else if (typeof response === 'object') {
console.log('got an object')
			userMsg = JSON.stringify(response);
		} else {
console.log("don't know what I got")
			userMsg = response;
		}
		//console.log('in JSAdmin::showResponse(): userMsg='+userMsg);

		if (userMsg.indexOf('uccess') >= -1 ) {
			//console.log('found success; userMsg= ' + userMsg);
    		obib.showMsg(userMsg);
			//this.doBackToList();  // this will cause user message to be removed before it can be read
		} else if (userMsg.indexOf('rror') >= -1 ) {
			//console.log('found error; userMsg= ' + userMsg);
    	    obib.showError(userMsg);
    	} else {
			//console.log('using default; userMsg= '+userMsg);
    	    obib.showError(userMsg);
        }
        return
    };
}
</script>
