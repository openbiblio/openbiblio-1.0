<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * JavaScript portion of the Biblio ItemEditor module
 * @author Fred LaPlante
 */

// Javascript Document - itemEditorJs.php

"use strict";

var ie = {
	init: function (opts) {
		ie.opts = opts;
		ie.url = '../catalog/catalogServer.php';
		ie.urlLookup = '../catalog/onlineServer.php'; //may not exist

	    $('#onlnUpdtBtn').on('click',null,function (){
			//console.log('online data requested');
			$('#onlnDoneBtn').show();
			$('#onlnUpdtBtn').hide();
			$('#itemEditorDiv td.filterable').show();
			ie.fetchOnlnData();
		});
	    $('#onlnDoneBtn').on('click',null,function (){
			$('#itemEditorDiv td.filterable').hide();
			$('#onlnUpdtBtn').show();
			$('#onlnDoneBtn').hide();
		});

		// prepare pull-down lists for later use
        list.getPullDownList('Media', $('#itemMediaTypes'));
        list.getPullDownList('Collection', $('#itemEditColls'));

	},
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
	},

	mkFldSet: function (key, val, mode) {
		var txt='',
			classStr = "marcBiblioFld",
			attrs = {},
			name='';

		/* unique items per mode */
		if (mode == 'editCol') {
			name = 'fields['+key+']';
			txt += '	<td valign="top" >'+"\n";
			var dataFlds = 'subfieldid='+val["subfieldid"]+'&fieldid='+val["fieldid"];
			txt += flos.inptFld('hidden', name+'[codes]', dataFlds)+"\n";
		} else if (mode == 'onlnCol') {
			name = 'onln_'+key;
			txt += '	<td valign="top" class="filterable">';
	    	txt += '		<input type="button" value="<--" id="'+name+'_btn" class="accptBtn" />';
			txt += '	</td>\n<td valign="top" class="filterable">';
		}

		/* common for both modes */
		if (val['repeat'])
		  classStr += " rptd";
		else
		  classStr += " only1";
	    if (mode == 'onlnCol')
	      classStr += " online";
		else
		  classStr += " offline";
		attrs["class"] = classStr;

		attrs['id'] = key;
		if (mode != 'onlncol') {
			if (val['required'] == 1)  attrs['required'] = 'required';
			if (val['validation_cd'] !== null) attrs['validation_cd'] = val['validation_cd'];
		}

		//console.log('in ie.mkFldSet():');
		//console.log(attrs);
		if (!val.value) val.value = ' ';
		if (val['form_type'] == 'textarea') {
			attrs["rows"] = "7"; attrs["cols"] = "48";
			txt += flos.inptFld('textarea', name+'[data]', val['value'], attrs, val['value'])+"\n";
		} else {
		  attrs["size"] = "50"; attrs["maxLength"] = "256";
			txt += flos.inptFld(val['form_type'], name+'[data]', val['value'], attrs)+"\n";
		}
		txt += "</td>\n";
		return txt;
	},

	//------------------------------
	doItemEdit: function (biblio) {
		$('#onlnUpdtBtn').show();
		$('#onlnDoneBtn').hide();
	    $('#biblioDiv').hide();

		var hdr = biblio.hdr,
			marc = biblio.marc;
	    ie.bibid = hdr.bibid;
		$('#editBibid').val(hdr.bibid);

		// fill pre-existing MARC fields with db data on hand
		// each field has, in array 'val':
		// a label, tag & suffix, fieldId, subfieldId, formInputType, displayValue
	    var txt = '';
		$.each(marc, function(key,val) {
			if (val.lbl) {
				var prefix = 'fields_'+key;
				txt += "<tr> \n";
				txt += "	<td valign=\"top\"> \n";
				txt +=  '		<label for="'+key+'">'+val['lbl']+": </label>\n";
                if (val['required'] == 1) {
					txt += '<span class="reqd">*</span>';
				}
				txt +=  "	</td> \n";
				txt += ie.mkFldSet(key, val, 'editCol');	// local edit column
				txt += ie.mkFldSet(key, val, 'onlnCol');  // update on-line column
				txt +=  "</tr> \n";
			}
		});
		$('#marcBody').html(txt);

		// set non-MARC fields
		//console.log('setting pull-downs: media='+hdr.material_cd+'; coll='+hdr.collection_cd);
        $('#itemMediaTypes').val(hdr.material_cd);
        $('#itemEditColls').val(hdr.collection_cd);
		$('#opacFlg').val(hdr.opac_flg);  // using data on hand

		$('#itemEditorDiv fieldset legend').html('<?php echo T("Edit Item Properties"); ?>');
		$('#itemEditorDiv td.filterable').hide();
		obib.reStripe2('biblioFldTbl','odd');

		$('#itemSubmitBtn').enable();
		$('#itemEditorDiv').show();
	},

	/* ====================================== */
	fetchOnlnData: function () {
		if ($('input[id="010$a"]').length > 0) var lccn = $.trim($('input[id="010$a"]').val());
			//console.log('lccn==>'+lccn);
		if ($('input[id="022$a"]').length > 0) var issn = $.trim($('input[id="022$a"]').val()).split(',');
			//console.log('issn==>'+issn);
		if ($('input[id="020$a"]').length > 0) {
		  	var isbnAll  = $('input[id="020$a"]').val().split(';');
		  	for (var i=0; i<isbnAll.length; i++) {
		    	if (isbnAll[i].substr(0,3) == '978') {
		    		var isbn = $.trim(isbnAll[i].substr(0,13));
						//console.log('isbn-13: '+isbn);
		    		break;
				} else {
		    		var isbn = $.trim(isbnAll[i].substr(0,10));
						//console.log('isbn-10: '+isbn);
		    		break;
				}
			}
			//console.log('isbn==>'+isbn);
		}
		if ($('input[id="245$a"]').length > 0) var title =  $.trim($('input[id="245$a"]').val());
			//console.log('title==>'+title);
		if ($('input[id="100$a"]').length > 0) var author = $.trim($('input[id="100$a"]').val().split(',')[0]);
			//console.log('author==>'+author);

		var msgText = '',
			params = '',
			item = '';
	  	if ((lccn != '') && (lccn != undefined) && (typeof lccn !== null)) {
			//console.log('using lccn');
	  		msgText = '<?php T("Searching for LCCN"); ?>'+' '+lccn;
	  		params = "&mode=search&srchBy=9&lookupVal="+lccn;
	  		item = isbn;
	  	} else if ((isbn != '') && (isbn != undefined)) {
			//console.log('using isbn');
	  		msgText = '<?php T("Searching for ISBN"); ?>'+' '+isbn;
	  		params = "&mode=search&srchBy=7&lookupVal="+isbn;
	  		item = isbn;
		} else if ((issn != '') && (issn != undefined)) {
			//console.log('using issn');
	  		msgText = '<?php T("Searching for ISSN"); ?>'+' '+issn;
	  		params = "&mode=search&srchBy=8&lookupVal="+issn;
	  		item = issn;
		} else if (title && author) {
			//console.log('using title & author');
	  		msgText = "Searching for<br />Title: '"+title+"',<br />and "+author;
	  		params = "&mode=search&srchBy=4&lookupVal="+title+"&srchBy2=1004&lookupVal2="+author;
	  		item = '"'+title+'", by '+author;
		} else {
			//console.log('nothing to search by');
			msgText = '<?php T("NotEnoughData"); ?>'
			$('#onlineMsg').html(msgText).show();
			return;
		}
		msgText += '.<br />' + '<?php echo T("this may take a moment.");?>'
		$('#onlineMsg').html(msgText).show();
			//console.log('search params ==>> '+params);

	  	$.post(ie.urlLookup,params,function(response){
			//console.log('params==>'+params)
			var rslts = response,
				numHits = parseInt(rslts.ttlHits),
				maxHits = parseInt(rslts.maxHits);
			if (numHits < 1) {
				$('#onlineMsg').html(rslts.msg+' for '+item);
			}
			else if (numHits >= maxHits) {
			  msgText = '<?php echo T("hits found, too many to process"); ?>';
				$('#onlineMsg').html(numHits+' '+msgText);
			}
			else if (numHits > 1){
				$('#onlineMsg').html(numHits+'hits found, this version can only handle one.');
			}
			else if (rslts.ttlHits == 1){
			  	var data;
				$('#onlineMsg').html('Success!!<br /><br />'+
									 'Click the arrow buton to enter online data,<br />'+
									 'then click "Update" at the bottom of the page.');
				bs.hostData = rslts.data;
				$.each(rslts.data, function(hostIndex,hostData) {
				  	$.each(hostData, function(hitIndex,hitData) {
					  	data = hitData;
				  	}); // .each
				}); // .each
				for (var tag in data) {
					$('#marcBody input.online:text').filter('#'+tag).val(data[tag]);
				}

			} // else

			// this button created dynamicly by server
			$('#marcBody input[type="button"].accptBtn').on('click',null,ie.doFldUpdt);

		},'JSON'); // .post
	},

	doFldUpdt: function (e) {
		var rowNmbr = ((e.target.id).split('_'))[1];
		var srcId = '#marcBody input[name="onln_'+rowNmbr+'[data]"]';
		var text = $(srcId).val();
		//console.log('you clicked btn #'+rowNmbr+' containing "'+text+'" from '+srcId );
		var destId = '#marcBody input[name="fields['+rowNmbr+'][data]"]';
		$(destId).val(text);
	},
};

// this package normally initialized by parent such as .../catalog/srchJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
