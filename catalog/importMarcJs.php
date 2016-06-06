<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
   
<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript - importMarcJs.php
"use strict";

var mrci = {
	init: function () {
		$('section').hide();
		$('.help').hide();
		mrci.initWidgets();
		mrci.autoBarcodeFlg = (<?php echo "'".$_SESSION['item_autoBarcode_flg']."'"; ?> == 'Y'?true:false);

		mrci.BCD_NEVER = 0;
		mrci.BCD_IF = 1;
		mrci.BCD_ALWAYS = 2;
	
		mrci.url = '../catalog/importServer.php';
		mrci.form = $('#specForm');
		
		/* input file delimiters */
		mrci.rcdTerminator = "\n";
		mrci.fldTerminator = "\t";

		mrci.getCollections();
		mrci.getMediaTypes();
		
		$('#imptSrce').on('change',null,function () {$('#imptBtn').enable();});
		$("#imptBtn").on('click',null,mrci.processImportFile);
		$("#helpBtn").on('click',null,function () {$('.help').toggle();});
		$(".bkupBtn").on('click',null,mrci.rtnToIntro);
		$("#Post2DbBtn").on('click',null,mrci.post2Db);
		$("#bcdDeflt").on('change',null,function () {mrci.bcdOpt = $('bcdDflt').val();});
		
		mrci.resetForm();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		//console.log('resetting Search Form');
		$('.help').hide();
		mrci.setCopyDefault()
		$('#review').hide();
		$('#rslts').hide();
		$('#imptBtn').disable();
		$('#intro').show();
	},
	//----//
	rtnToIntro: function () {
		mrci.resetForm();
		$('#imptBtn').enable();
	},
	showHelp: function () {
		$('#help').show('norm');
	},
	closeHelp: function () {
		$('#help').hide('norm');
	},
	setCopyDefault: function () {
		if (mrci.autoBarcodeFlg) {
			$('#bcdDeflt').val(2);
		} else {
			$('#bcdDeflt').val(1);
		}
	},
		
	//------------------------------
	getCollections: function () {
		mrci.collections = [];
	  $.getJSON(mrci.url,{'mode':'getCollections'}, function(json){
	  	$.each(json, function (k,v){
				mrci.collections[k] = v;
			});
	  });
	},
	//----//
	getMediaTypes: function () {
		mrci.mediaTypes = [];
	  $.getJSON(mrci.url,{'mode':'getMediaTypes'}, function(json){
	  	$.each(json, function (k,v){
	  		mrci.mediaTypes[k] = v;
	  	});
	  });
	},
	//----//
	getDfltColl: function () {
		return $('#collectionCd').val();
	},
	getDfltMedia: function () {
		return $('#materialCd').val();
	},
	getOpacFlg: function () {
		return $('#opacFlg').val();
	},
	getDfltStatus: function () {
		return $('#code').val();
	},
	getCopyAction: function () {
		return $('#cpyAction').val();
	},
	isDupBarCd: function (barCd) {
/*
TODO				// Check for uniqueness with existing barcodes and new entries read.
  					$barcode = $rec["barcode_nmbr"];
					  if ($barcode != "") {
				    	if (in_array($barcode, $newBarcodes)) {
				    	  array_push($localErrors, T("biblioCopyQueryErr1"));
				    	  $validate = false;
				   	 } else {
								echo T("Barcode not present")."<br />";
							}
				  	  // push new barcode into validation array 
				  	  array_push($newBarcodes, $barcode);
					  }					  
*/
		return false;
	}, 

	//------------------------------
	processImportFile: function (e) {
		e.preventDefault();
		mrci.importFile(e);	//file will be stored at mrci.File
		e.stopPropagation();
		return false;;	
	},
	//----//
	importFile: function () {
		$.ajaxFileUpload({
				url:							mrci.url,
				secureuri:				false,
				fileElementId:		'imptSrce',
				dataType: 				'text',
        contentType: 			$('#specForm').attr( "enctype", "multipart/form-data" ),
				data:							{ 'mode':'processMarcFile',
														'test':$('[name="test"]:checked').val(),
														'collectionCd': $('#collectionCd').val(),
														'materialCd': $('#materialCd').val(),
														'opacFlg': $('#opacFlg option:selected').val(),
														 },
				success: 					function (response, status) {
														mrci.File = response;
														$('#mrcImportRslts').html(response);
														$('#intro').hide();
														$('#rslts').show();
													},
				error: 						function (data, status, e) { 
														alert(e); 
														console.log('error');
														console.log(JSON.parse(data));				
													}
		});

	},
	
};

$(document).ready(mrci.init);

</script>
