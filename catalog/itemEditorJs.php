<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * JavaScript portion of the Biblio ItemEditor module
 * @author Fred LaPlante
 */
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

//console.log('mkFldSet():');
//console.log(attrs);
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

		// set non-MARC fields using data on hand
		$('#itemMediaTypes').val(hdr.material_cd);
		$('#itemEditColls').val(hdr.collection_cd);
		$('#opacFlg').val(hdr.opac_flg);

		// fill MARC fields with data on hand
		// each field has, in array 'val', a:
		//  label, tag & suffix, fieldId, subfieldId, formInputType, displayValue
	  var txt = '';
		$.each(marc, function(key,val) {
//console.log('doItemEdit(): key='+key);
//console.log(val);
			if (val.lbl) {
				var prefix = 'fields_'+key;
				txt += "<tr> \n";
				txt += "	<td valign=\"top\"> \n";
				txt +=  '		<label for="'+key+'">'+val['lbl']+": </label>\n";
				txt +=  "	</td> \n";
				txt += ie.mkFldSet(key, val, 'editCol');	// local edit column
				txt += ie.mkFldSet(key, val, 'onlnCol');  // update on-line column
				txt +=  "</tr> \n";
			}
		});
		$('#marcBody').html(txt);

		$('#itemEditorDiv fieldset legend').html('<?php echo T("Edit Item Properties"); ?>');
		$('#itemEditorDiv td.filterable').hide();
		obib.reStripe2('biblioFldTbl','odd');

		$('#itemSubmitBtn').enable();
		$('#itemEditorDiv').show();
	},

	/* ====================================== */
	fetchOnlnData: function () {
		if ($('input[id="245$a"]').length > 0) var title =  $('input[id="245$a"]').val();
			//console.log('title==>'+title);
		if ($('input[id="100$a"]').length > 0) var author= $('input[id="100$a"]').val().split(',')[0];
			//console.log('author==>'+author);
		if ($('input[id="020$a"]').length > 0) {
		  var isbn  = $('input[id="020$a"]').val().split(',');
		  for (var i=0; i<isbn.length; i++) {
		    if (!((isbn[i].substr(0,3) == '978') && (isbn[i].length == 10))) {
		    	var ISBN = isbn[i];
		    	break;
				}
			}
			//console.log('isbn==>'+isbn);
		}
		if ($('input[id="022$a"]').length > 0) var issn  = ($('input[id="022$a"]').val()).split(',');
			//console.log('issn==>'+issn);

		var msgText = '',
				params = '',
				item = '';
	  if ((isbn != '') && (isbn != undefined)) {
	  	msgText = '<?php T("Searching for ISBN"); ?>'+' '+isbn,
	  	params = "&mode=search&srchBy=7&lookupVal="+isbn,
	  	item = isbn;
		} else if ((issn != '') && (issn != undefined)) {
	  	msgText = '<?php T("Searching for ISSN"); ?>'+' '+issn;
	  	params = "&mode=search&srchBy=7&lookupVal="+issn;
	  	item = issn;
		} else if (title && author) {
	  	msgText = "Searching for<br />Title: '"+title+"',<br />by "+author;
	  	params = "&mode=search&srchBy=4&lookupVal="+title+"&srchBy2=1004&lookupVal2="+author;
	  	item = '"'+title+'", by '+author;
		} else {
			$('#onlineMsg').html('<?php T("NotEnoughtData"); ?>').show();
			return;
		}

	  msgText += '.<br />' + '<?php echo T("this may take a moment.");?>'
		$('#onlineMsg').html(msgText).show();

	  $.post(ie.urlLookup,params,function(response){
			//console.log('params==>'+params)
			var rslts = JSON.parse(response),
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

				// this button created dynamicly by server
				$('#marcBody input.accptBtn').on('click',null,bs.doFldUpdt);
			} // else
		}); // .post
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
