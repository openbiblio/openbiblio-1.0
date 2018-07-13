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

		/* populate pull-down lists */
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
		list.getPullDownList('Collection', $('#collectionCd'));
	},
	//----//
	getMediaTypes: function () {
		list.getPullDownList('Media', $('#materialCd'));
	},
	//----//
/*
	getDfltColl: function () {
		return $('#collectionCd').val();
	},
	getDfltMedia: function () {
		return $('#materialCd').val();
	},
*/
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
console.log('in mrci::processImportFile()');
		e.preventDefault();
		mrci.importFile(e);	//file will be stored at mrci.File
		e.stopPropagation();
		return false;;	
	},
	//----//
/* // original code
	importFile: function () {
		$.ajaxFileUpload({
			url:			mrci.url,
			secureuri:		false,
			fileElementId:	'imptSrce',
			dataType: 		'text',
      		contentType: 	$('#specForm').attr( "enctype", "multipart/form-data" ),
			data:			{ 'mode':'processMarcFile',
							  'test':$('[name="test"]:checked').val(),
							  'collectionCd': $('#collectionCd').val(),
							  'materialCd': $('#materialCd').val(),
							  'opacFlg': $('#opacFlg option:selected').val(),
							},
			success: 		function (response, status) {
								mrci.File = response;
								$('#mrcImportRslts').html(response);
								$('#intro').hide();
								$('#rslts').show();
							},
			error: 			function (data, status, e) {
								alert(e); 
								console.log('error');
								console.log(JSON.parse(data));				
							}
		});

	},
*/
/*  new attempt - 25 Jun 18
	importFile: function () {
console.log('in mrci::importFile()');
		$('input[type="file"]').ajaxfileupload({
			  action: mrci.url,
			  valid_extensions : ['marc','csv'],
			  params: { 'mode':'processMarcFile',
						'test':$('[name="test"]:checked').val(),
						'collectionCd': $('#collectionCd').val(),
						'materialCd': $('#materialCd').val(),
						'opacFlg': $('#opacFlg option:selected').val(),
					  },
			  onComplete: function(response) {
			    	console.log('custom handler for file:');
			    	alert(JSON.stringify(response));
			  },
			  onStart: function() {
//			    	if(weWantedTo) return false; // cancels upload
console.log('beginning upload');
			  },
			  onCancel: function() {
			    	console.log('no file selected');
			  }
		});
	},
*/
/*  // another try 25 Jun 2018
	importFile: function () {
		const file = $('input[type="file"]').val();
console.log('preping file: '+file);
        const uri = mrci.url;
        const xhr = new XMLHttpRequest();
        const fd = new FormData();

        xhr.open("POST", uri, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText); // handle response.
            }
        };
        fd.append('myFile', file);
        // Initiate a multipart/form-data upload
        xhr.send(fd);
	},
*/
/*
	// trying photoEditor code 27 Jun 2018
	importFile: function () {
console.log('in mrci::importFile()');
//console.log($('input[type="file"]').prop('files'));
		const file = $('input[type="file"]').prop('files');
console.log(file);
		$.post(mrci.url,
			{ 'mode':'processMarcFile',
			  'test':$('[name="test"]:checked').val(),
			  'collectionCd': $('#collectionCd').val(),
			  'materialCd': $('#materialCd').val(),
			  'opacFlg': $('#opacFlg option:selected').val(),
			  'imptSrce': file[0],
			},
			function (response) {
console.log('upload completed');
				$('#mrcImportRslts').html(response);
				$('#intro').hide();
				$('#rslts').show();
			},
		);
	},
*/
	// using SimpleUpload.js 27 Jun 2018
	importFile: function () {
console.log('in mrci::importFile()');
        $('input[type="file"]').simpleUpload(mrci.url, {
			data:{  'junk':'dummy',
					'mode':'processMarcFile',
			  		'test':$('[name="test"]:checked').val(),
			  		'collectionCd': $('#collectionCd').val(),
			  		'materialCd': $('#materialCd').val(),
			  		'opacFlg': $('#opacFlg option:selected').val(),
			},
			success: function (rslt) {
console.log('upload completed');
				$('#mrcImportRslts').html(rslt);
				$('#intro').hide();
				$('#rslts').show();
			},
			error: function (response) {
console.log('upload failed');
				$('#mrcImportRslts').html(response);
				$('#intro').hide();
				$('#rslts').show();
			},
		});
	},

};

$(document).ready(mrci.init);

</script>
