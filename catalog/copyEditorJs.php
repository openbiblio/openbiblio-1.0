<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";

var ced = {
	init: function () {
		ced.url = '../catalog/catalogServer.php';
		ced.initWidgets();
		$('.help').hide();

		$('#barcode_nmbr').on('change',null,ced.chkBarcdForDupe);
		$('#copySubmitBtn').val('<?php echo T("Update"); ?>');
		$('#copySubmitBtn').on('click',null,ced.doCopyUpdate);
		$('#copyCancelBtn').val('<?php echo T("Go Back"); ?>');

		if ($('#autobarco:checked').length > 0) {
			$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').on('change',null,function (){
		  if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
				ced.doGetBarcdNmbr();
				$('#copySubmitBtn').enable(); //.css('color', bs.srchBtnClr);
			}
			else {
				$('#barcode_nmbr').enable();
			}
		});

	},

	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		$('#editRsltMsg').hide();
	},
	//----//
	doGetBarcdNmbr: function () {
		$.getJSON(ced.url,{'mode':'getNewBarcd'}, function(jsonInpt){
		  $('#copyBarcode_nmbr').val(jsonInpt.barcdNmbr)         // e.g. pattern="[0]{10}"
														.attr('pattern','[0]{<?php echo Settings::get('item_barcode_width');?>}' );
		});
	},

	chkBarcdForDupe: function () {
		var barcd = $.trim($('#barcode_nmbr').val());
		barcd = flos.pad(barcd,bs.opts.barcdWidth,'0');
		$('#barcode_nmbr').val(barcd);
		// Set copyId to null if not defined (in case of new item)
		var currCopyId = null;
		if(typeof(bs.crntCopy) != "undefined"){
			currCopyId = bs.crntCopy.copyid;
		}

	  $.get(ced.url,{'mode':'chkBarcdForDupe','barcode_nmbr':barcd,'copyid':currCopyId}, function (response) {
	  	if(response.length > 0){
			$('#copySubmitBtn').disable(); //.css('color', '#888888');
			$('#editRsltMsg').html(response).show();
		} else {
			$('#copySubmitBtn').enable(); //.css('color', bs.srchBtnClr);
			$('#editRsltMsg').html(response).show();
		}
		})
	},
	/* ====================================== */
	doCopyNew: function () {
		if ($('#autobarco:checked').length > 0) {
      ced.doGetBarcdNmbr();
      $('#copyBarcode_nmbr').disable();
		}
		$('#copySite').val(<?php echo Settings::get('library_name');?>);
		$('#copyMode').val('newCopy');

		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').on('click',null,function (e) {
			e.preventDefault();
			e.stopPropagation();
			var params= $('#copyForm').serialize() + '&barcode_nmbr='+$('#copyBarcode_nmbr').val();
			ced.doPostCopy2DB(params);
		});
	  // prevent submit button from firing a 'submit' action
		return false;
	},

	doCopyEdit: function (e) {
		$('#editRsltMsg').html('').hide();
		var copyid = $(this).next().next().val();
		for (var nCopy in idis.copyJSON) {
			idis.crntCopy = eval('('+idis.copyJSON[nCopy]+')')
		  if (idis.crntCopy['copyid'] == copyid) break;
		}
		$('#copyBarcode_nmbr').val(idis.crntCopy.barcode_nmbr);
		$('#copyDesc').val(idis.crntCopy.copy_desc);
		$('#copySite').val([idis.crntCopy.site]);
		$('#copyTbl #status_cd').val(idis.crntCopy.statusCd);
		$('#copyLegend').html("<?php echo T("Edit Copy Properties"); ?>");

  	var crntsite = idis.opts.current_site
		$('#copySite').val(crntsite);

		// custom fields
		for(var nField in idis.crntCopy.custFields){
			$('#copyCustom_'+idis.crntCopy.custFields[nField].code).val(idis.crntCopy.custFields[nField].data);
		}

		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').on('click',null,function (e) {
			ced.doCopyUpdate(e);
			return false;
		});

		// Set 'update' button to enabled in case it wasn't from a previous edit
		$('#copySubmitBtn').enable();

	  // prevent submit button from firing a 'submit' action
		return false;
	},
	doCopyUpdate: function (e) {
		e.stopPropagation();
		e.preventDefault();
	  var barcdNmbr = $('#copyBarcode_nmbr').val();

	  // serialize() ignores disabled fields, so cant reliably use it in this case
	  var copyDesc = $('#copyDesc').val();
	  var statusCd = $('#copyTbl #status_cd').val();
	  var siteid = $('#copySite').val();
		var params = "&mode=updateCopy&bibid="+idis.theBiblio.bibid+"&copyid="+idis.crntCopy.copyid
					 		 + "&barcode_nmbr="+barcdNmbr+"&copy_desc="+copyDesc
					 		 + "&status_cd="+statusCd+"&siteid="+siteid;

		// Custom fields
		for(var nField in idis.crntCopy.custFields){
			// Only add if has a value, or changed from a value to nothing
			if($('#copyCustom_'+idis.crntCopy.custFields[nField].code).val() != idis.crntCopy.custFields[nField].data ||  $('#copyTbl #custom_'+idis.crntCopy.custFields[nField].code).val() != ""){
				params = params + '&custom_'+idis.crntCopy.custFields[nField].code+'='+$('#copyCustom_'+idis.crntCopy.custFields[nField].code).val();
			}
		}

		// post to DB
		ced.doPostCopy2DB(params);
		return false;
	},
	doPostCopy2DB: function (parms) {
	  $.post(ced.url,parms, function(response){
	  	if(response == '!!success!!') {
				$('#copyCancelBtn').val("Go Back");
				$('#editRsltMsg').html('Copy updated successfully!').show();
			} else {
				$('#editRsltMsg').html(response).show();
			}
	  });
	},
	doCopyDelete: function (e) {
	  $(this).parent().parent().addClass('hilite');
	  if (confirm('<?php echo T("Are you sure you want to delete this copy?"); ?>')) {
			var copyid = $(this).next().val();
	    var params = "&mode=deleteCopy&bibid="+idis.theBiblio.bibid+"&copyid="+copyid;
	  	$.post(ced.url,params, function(response){
	  	  $('#editRsltMsg').html(response).show();
	  		idis.fetchCopyInfo(); // refresh copy display
	  	});
		};
	  $(this).parent().parent().removeClass('hilite');
	}
}
$(document).ready(ced.init);
</script>
