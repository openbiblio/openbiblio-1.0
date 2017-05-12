<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document - copyEditorJs.php
"use strict";

var ced = {
	init: function () {
		ced.catalogSrvr = '../catalog/catalogServer.php';
        ced.listSrvr = '../shared/listSrvr.php';
		ced.initWidgets();
		ced.resetForm();

		$('#copyBarcode_nmbr').on('change',null,ced.chkBarcdForDupe);
		$('#copySubmitBtn').val('<?php echo T("Update"); ?>');
		$('#copySubmitBtn').on('click',null,ced.doCopyUpdate);
		$('#copyCancelBtn').val('<?php echo T("Go Back"); ?>');

//        ced.fetchStatusCds();

		if ($('#autobarco:checked').length > 0) {
			$('#copyBarcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').on('change',null,function (){
		  if ($('#autobarco:checked').length > 0) {
				$('#copyBarcode_nmbr').disable();
				ced.doGetBarcdNmbr();
				$('#copySubmitBtn').enable(); //.css('color', bs.srchBtnClr);
			}
			else {
				$('#copyBarcode_nmbr').enable();
			}
		});

		$('#status_cd > option[value=out]').prop('disabled',true);
		$('#status_cd > option[value=hld]').prop('disabled',true);
	},

	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		$('#editRsltMsg').hide();
		$('#crntStatus').hide();
		$('.help').hide();
	},
	//----//
	doGetBarcdNmbr: function () {
		$.post(ced.catalogSrvr,{'mode':'getNewBarcd'}, function(jsonInpt){
		    $('#copyBarcode_nmbr').val(jsonInpt.barcdNmbr)
                     // e.g. pattern="[0]{10}"
			     .attr('pattern','[0]{<?php echo Settings::get('item_barcode_width');?>}' );
		}, 'json');
	},
	//----//
	fetchStatusCds: function () {
        list.getStatusCds($('#status_cd'));
        // LJ: this is included in the function (where cv
        //$('#status_cd').html(statusList);
//        ced.fetchSiteList();
	},
	//----//
	fetchSiteList: function () {
//        var siteList = list.getSiteList($('#copySite'));
//		$('#copySite').html(siteList);
        list.getSiteList($('#copySite'));
	},
	//----//
	chkBarcdForDupe: function () {
		var barcd = $.trim($('#copyBarcode_nmbr').val());
		barcd = flos.pad(barcd,bs.opts.barcdWidth,'0');
		$('#copyBarcode_nmbr').val(barcd);
		// Set copyId to null if not defined (in case of new item)
		var currCopyId = null;
		if(typeof(bs.crntCopy) != "undefined"){
			currCopyId = bs.crntCopy.copyid;
		}

        $.post(ced.catalogSrvr,{'mode':'chkBarcdForDupe','barcode_nmbr':barcd,'copyid':currCopyId}, function (response) {
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
	doCopyEdit: function (e) {
		e.preventDefault();
		e.stopPropagation();
		$('#editRsltMsg').html('').hide();
		var btnid = e.currentTarget.id,
				copyid = btnid.split('-')[1];
		for (var nCopy in idis.copys) {
			var crntCopy =idis.copys[nCopy];
		  if (crntCopy.copyid == copyid) break;
		}
		ced.crntCopy = crntCopy;
		$('#copyBarcode_nmbr').val(crntCopy.barcode);
		$('#copyDesc').val(crntCopy.desc);
		$('#copySite').val([crntCopy.siteid]);
		$('#copyTbl #status_cd').val(crntCopy.status);
		$('#copyLegend').html("<?php echo T("Edit Copy Properties"); ?>");

        // LJ: This is too late to load the list, as it is an async request, and will post populate.
        //ced.fetchStatusCds();
        //ced.fetchSiteList();

		// custom fields
		var fldData = crntCopy.custom;
		$('#cstmFlds input').each(function (n) {
			var parts = this.id.split('_');
			var code = parts[1];
			if (typeof(fldData) !== 'undefined') {
				var datum = fldData[code];
				$(this).val(datum);
			} else {
                $(this).val("");
            }
		});

        $('#copySubmitBtn').val('<?php echo T("Update"); ?>');

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
	doCopyNew: function (e) {
		//console.log('in copyEditorJs.php::doCopyNew()');
        ced.bibid = $('#copyBibid').val(); // set within srchJs::newCopy(); FL Aug 2016
        if ($('#autobarco:checked').length > 0) {
            ced.doGetBarcdNmbr();
            $('#copyBarcode_nmbr').disable();
        }
        ced.fetchStatusCds();
        ced.fetchSiteList();

		$('#copySite').val("<?php echo Settings::get('library_name');?>");
		$('#copyMode').val('newCopy');
		$('#copyCancelBtn').val('<?php echo T("Go Back"); ?>');
		$('#copySubmitBtn').enable();
        $('#copySubmitBtn').val('<?php echo T("Create"); ?>');

		// unbind & bind needed here because of button reuse elsewhere
        $('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').on('click',null,function (e) {
			e.preventDefault();
			e.stopPropagation();
			var params = "&mode=newCopy&bibid="+ced.bibid;
			ced.doPostCopy2DB(params);
			if (typeof(ni) !== 'undefined') {
				ni.doPhotoAdd();
			}
		});
	    // prevent submit button from firing a 'submit' action
		return false;
	},

	doCopyUpdate: function (e) {
		e.stopPropagation();
		e.preventDefault();

	    var params = "&mode=updateCopy&bibid="+ced.bibid+"&copyid="+ced.crntCopy.copyid;

		// post to DB
		ced.doPostCopy2DB(params);
		return false;
	},
	doPostCopy2DB: function (params) {
		//console.log('in copyEditorJs.php::doPostCopy2Db()');
		var copy = ced.crntCopy,
			barcdNmbr = $('#copyBarcode_nmbr').val(),
	  		copyDesc = $('#copyDesc').val(),
	  		statusCd = $('#copyTbl #status_cd').val(),
	  		siteid = $('#copySite').val();
		params += "&barcode_nmbr="+barcdNmbr+"&copy_desc="+copyDesc
					 +  "&status_cd="+statusCd+"&siteid="+siteid;  // adds to incoming argument value

		// Custom fields
		$('#cstmFlds input').each(function (n) {
			var code = this.id.split('_');
			params += '&'+this.id+'='+$(this).val();
		});

	    $.post(ced.catalogSrvr,params, function(response) {
            if (response == '!!success!!') {
                //$('#copyCancelBtn').val("Back");
                //$('#copySubmitBtn').disable();
                if (typeof(idis) === 'object') {
                    // needed because idis not available in all use cases of the copy editor
                    idis.fetchCopyInfo(); // refresh copy display
                }

                // LJ: This module is used by search and new items.
                if(typeof ni !== 'undefined') {
                    ni.doPhotoAdd();
                } else {
                    bs.rtnToBiblio();
                }
                $('#rsltMsg').html('Copy updated successfully!').show();
                setTimeout(function() {
                    $("#rsltMsg").hide(500);
                }, 5000);
            } else {
                $('#editRsltMsg').html(response).show();
            }
        });
        // LJ: removed JSON, as the result is not JSON.
	    //}, 'json');
	},
	doCopyDelete: function (e) {
	  $(this).parent().parent().addClass('hilite');
	  if (confirm('<?php echo T("Are you sure you want to delete this copy?"); ?>')) {
		  var btnid = e.currentTarget.id,
			  copyid = btnid.split('-')[1],
	    	  params = "&mode=deleteCopy&bibid="+idis.bibid+"&copyid="+copyid;
	  	  $.post(ced.catalogSrvr,params, function(response){
	  	        $('#editRsltMsg').html(response).show();
	  	        idis.fetchCopyInfo(); // refresh copy display
	  	  });
	  };
	  $(this).parent().parent().removeClass('hilite');
	}
}
$(document).ready(ced.init);
</script>
