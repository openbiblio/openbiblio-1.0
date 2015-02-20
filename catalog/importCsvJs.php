<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
   
<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
"use strict";

var csvi = {
	init: function () {
		$('section').hide();
		$('.help').hide();
		csvi.initWidgets();
		csvi.autoBarcodeFlg = (<?php echo "'".$_SESSION['item_autoBarcode_flg']."'"; ?> == 'Y'?true:false);

		csvi.BCD_NEVER = 0;
		csvi.BCD_IF = 1;
		csvi.BCD_ALWAYS = 2;
	
		csvi.url = '../catalog/importServer.php';
		csvi.form = $('#specForm');
		
		/* input file delimiters */
		csvi.rcdTerminator = "\n";
		csvi.fldTerminator = "\t";

		csvi.getCollections();
		csvi.getMediaTypes();
		
		$('#imptSrce').on('change',null,function () {$('#imptBtn').enable();});
		$("#imptBtn").on('click',null,csvi.processImportFile);
		$("#helpBtn").on('click',null,function () {$('.help').toggle();});
		$(".bkupBtn").on('click',null,csvi.rtnToIntro);
		$("#Post2DbBtn").on('click',null,csvi.post2Db);
		$("#bcdDeflt").on('change',null,function () {csvi.bcdOpt = $('bcdDflt').val();});
		
		csvi.resetForm();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		//console.log('resetting Search Form');
		$('.help').hide();
		csvi.setCopyDefault()
		$('#review').hide();
		$('#rslts').hide();
		$('#imptBtn').disable();
		$('#intro').show();
	},
	//----//
	rtnToIntro: function () {
		csvi.resetForm();
		$('#imptBtn').enable();
	},
	showHelp: function () {
		$('#help').show('norm');
	},
	closeHelp: function () {
		$('#help').hide('norm');
	},
	setCopyDefault: function () {
		if (csvi.autoBarcodeFlg) {
			$('#bcdDeflt').val(2);
		} else {
			$('#bcdDeflt').val(1);
		}
	},
		
	//------------------------------
	getCollections: function () {
		csvi.collections = [];
	  $.getJSON(csvi.url,{'mode':'getCollections'}, function(json){
	  	$.each(json, function (k,v){
				csvi.collections[k] = v;
			});
	  });
	},
	//----//
	getMediaTypes: function () {
		csvi.mediaTypes = [];
	  $.getJSON(csvi.url,{'mode':'getMediaTypes'}, function(json){
	  	$.each(json, function (k,v){
	  		csvi.mediaTypes[k] = v;
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
	getShowAllFlg: function () {
		return ($('#showAll').val() == 'Y'?true:false);
	},
	//getTestTF: function () {
	//	return $('[name="test"]:checked').val();
	//},
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
		csvi.importFile(e);	//file will be stored at csvi.File
		e.stopPropagation();
		return false;;	
	},
	//----//
	importFile: function () {
		$.ajaxFileUpload({
				url:							csvi.url,
				secureuri:				false,
				fileElementId:		'imptSrce',
				dataType: 				'json',
        contentType: 			$('#specForm').attr( "enctype", "multipart/form-data" ),
				data:							{ 'mode':'fetchCsvFile' },
				success: 					function (response, status) {
														csvi.File = response;			
														$('#intro').hide();
														$('#review').show();
														csvi.displayColHeads();	
														csvi.processRows();
													},
				error: 						function (data, status, e) { 
														alert(e); 
														console.log('error');
														console.log(JSON.parse(data));				
													}
		});

	},
	//----//
	displayColHeads: function () {
		/* print column headings given in the first row of the input file */
		var record = csvi.File[0],
				headings,
				heading,
				txt = "",
				tag = "";
		csvi.headings = record.split(csvi.fldTerminator);
		colHeads = $('#colHeads');
		colHeads.html(" ");		
		for( var i=0; i<csvi.headings.length; i++) {
			heading = csvi.headings[i].trim();
	  	txt = '<tr><td>'+heading+'</td>';
	  	switch (heading) {
	  		case 'barCo':
	  			txt += <?php echo "'<td>".T("Barcode Number")."</td></tr>'"; ?>;
	  			break;
	  		case "coll":
	  		 	txt += <?php echo "'<td>".T("Collection")."</td></tr>'"; ?>;
	 		   	break;
	  		case "media":
	    		txt += <?php echo "'<td>".T("Media Type")."</td></tr>'"; ?>;
	    		break;
	  		case "opac?":
	  		  txt += <?php echo "'<td>".T("opacFlag")."</td></tr>'"; ?>;
	  		  break;
			  default:
					var pattern = /^[0-9][0-9]*\$[a-z]$/;
			    if (pattern.test(heading)) {
						var parts = heading.split('$');
			    	txt += '<td id="tag'+parts[0]+parts[1]+'"></td></tr>';
			    	csvi.fetchTagDescription(heading);
			    } else {
	      		txt = <?php echo "'<td>".T("CSVunknownIgnored")."</td></tr>'"; ?>;
	    		}
	  	}
	  	colHeads.append(txt);
		}
	},
	//----//
	fetchTagDescription: function (tag) {
	  $.get(csvi.url,{'mode':'getMarcDesc', 'code':tag}, function(response){
	  	//console.log(tag+" desc = '"+response+"'");
			var parts = tag.split('$');
			$('#tag'+parts[0]+parts[1]).html(response);
		});
	},
	/* ---- */
	processRows: function () {
		var rec = {};	// output record
		var csvRcrds = $('#csvRcrds'); csvRcrds.html(' ');
		var csvErrs = $('#csvErrs'); csvErrs.html(' ');
		var showAll = csvi.getShowAllFlg();
		csvi.csvRecords = [];
		//console.log('showAll='+showAll);
		//console.log('bcdDflt='+$('#bcdDeflt').val());
	
		/* for use with barcodes */
		var width = <?php echo $_SESSION[item_barcode_width]; ?>;
	 	if( width <= 1 ) var w = 13; else var w = width;
	  	
		for (var line=1; line<csvi.File.length; line++) {
			//console.log('working row #'+line+' of '+csvi.File.length+' : '+csvi.File[line]);
					
			/* break an input line into an array of its parts */
			var data = csvi.File[line].split(csvi.fldTerminator);
			if (showAll) csvRcrds.append(' <tr><td colspan="3">&nbsp</td></tr>\n');
    	if (showAll) csvRcrds.append(' <tr><td colspan="3"> - - - - - - - Line #'+line+' - - - - - - - - </td></tr>\n');

  		var mandatoryCols = {
  			'coll'  : false,		// collection name
  			'media' : false,		// media type name
			};

			var fields = {},
					rec = {};

			/* default options */
			rec['copy_desc'] = $('#copyText').val();
			rec['copy_action'] = csvi.getCopyAction();
			rec['status_cd'] = csvi.getDfltStatus();

			//console.log(rec);
			//console.log("desc="+rec['copy_desc']+"; action="+rec['copy_action']+"; status="+rec['status_cd']);

			for (var n=0; n<csvi.headings.length; n++) {
    		if ( (data[n] === undefined) || (data[n] == '') ) {
					var entry = '';
				} else {
    			var entry = data[n].trim();
				}
    		var target = csvi.headings[n].trim();
				//console.log("working "+(n)+" of "+csvi.headings.length+" with entry '"+entry+"' in column '"+target+"'");

				/* if current column is a 'mandatory' and is present in headings mark as 'seen' */
    		if ( mandatoryCols[target] ) mandatoryCols[target] = true;
    		
    		switch (target) {
    			case 'barCo':
    				/* pad bar code to proper size */
      			rec['barcode_nmbr'] = flos.pad(entry, w, "0");
						if (csvi.isDupBarCd()) {
							csvErrs.append(' <tr><td colspan="3">'+<?php echo "'".T("LineNmbr")."'"; ?>+i+<?php echo "'".T("Barcode")."'"; ?>
															+' '+rec['barcode_nmbr']+<?php echo " '".T("isaDup")."'"; ?>+"</td></tr>\n");
							//continue;
						} else {
    	  			if (showAll) csvRcrds.append(" <tr><td>"+<?php echo "'".T("Barcode")."'"; ?>+"</td><td>&nbsp;</td><td>"+rec['barcode_nmbr']+"</td></tr>\n");
    	  		}
      			if ( ( parseInt(rec['barcode_nmbr']) == 0 ) && ( csvi.bcdDflt >= csvi.BCD_IF ) ){
							rec['barcode_nmbr'] = 'autogen';
						}
    				break;
			    case "coll":
						var flg = false,
								txt = '',
								thisOne = csvi.collections.indexOf(entry);
						if (entry == '') {
							txt = ' <?php echo T("absent"); ?>';
							flg = true;
						} else if (thisOne < 0) {
							txt = "'"+entry+"' <?php echo T("invalid"); ?>";
							flg = true;
						}
						if (flg) {
			        var thisOne = csvi.getDfltColl();
							csvErrs.append(' <tr><td colspan="3">'+
															 <?php echo "'".T("LineNmbr")."'"; ?>+line+" "+
															 <?php echo "'".T("Collection")."'"; ?>+" "+txt+
															 " <?php echo T("using default"); ?>: '"+
															 csvi.collections[thisOne]+"'</td></tr>\n");
						}
			      rec['collection_cd'] = thisOne;
      			if (showAll) csvRcrds.append("  <tr><td>"+<?php echo "'".T("Collection")."'"; ?>+"</td><td>&nbsp;</td><td>"+csvi.collections[thisOne]+"</td></tr>\n");
			      break;
    			case 'media':
			    	var thisOne = csvi.mediaTypes.indexOf(entry);
			      if (thisOne < 0) {
			        thisOne = csvi.getDfltMedia();
							csvErrs.append(' <tr><td colspan="3">'+<?php echo "'".T("LineNmbr")."'"; ?>+i+" "+<?php echo "'".T("Media")."'"; ?>
															+" '"+entry+"' <?php echo T("invalid, using default"); ?>: '"+csvi.mediaTypes[thisOne]+"'</td></tr>\n");
			      }
			      rec['material_cd'] = thisOne;
      			if (showAll) csvRcrds.append("  <tr><td>"+<?php echo "'".T("Media Type")."'"; ?>+"</td><td>&nbsp;</td><td>"+csvi.mediaTypes[thisOne]+"</td></tr>\n");
      			break;
			    case "opac?":
						var patternYes = /^[yYtT]/;
						var patternNo = /^[nNfF]/;
						
			    	if (patternYes.test(entry)) {
			        rec["opac_flg"] = true;
			      } else if (patternNo.test(entry)) {
			        rec["opac_flg"] = false;
			      } else {
			        rec["opac_flg"] = csvi.getOpacFlg();
			      }
      			if (showAll) csvRcrds.append("  <tr><td>"+<?php echo "'".T("Show in OPAC")."'"; ?>+"</td><td>&nbsp;</td>" +
      											"<td>"+(rec["opac_flg"] == true?"true":"false")+"</td><tr>\n");
			      break;
			    default:
			    	var pattern = /^[0-9][0-9]*\$[a-z]$/;
			      if (pattern.test(target)) {
			        var tag = target.split('$');
							var tgt = { 'tag':tag[0], 'subfield_cd':tag[1], 'data':entry };		        
							fields[target] = tgt;
											
	      			if (showAll) csvRcrds.append("  <tr><td>"+tag[0]+"</td>" +
	      											"<td>"+tag[1]+"</td>" +
	      											"<td>"+entry+"</td></tr>\n");
			      }
			      break;
			  }
			}

			/* check for barcode present, and add if user wishes */
			if ((! rec['barcode_nmbr'] ) && ( $('#cpyAction').val() == csvi.BCD_ALWAYS )){
				csvErrs.append(' <tr><td colspan="3">Line #'+line+" "+<?php echo "'".T("barcode missing, auto-generating")."'"; ?>+"</td></tr>\n");
      	rec['barcode_nmbr'] = 'autogen';
			}

			/* now add 'fields array' to rec */
			rec['fields'] = fields
			//console.log(rec);
			csvi.csvRecords[line-1] = rec;
		}	
		
		/* parsing complete, ready to send to server for posting to database */
		//console.log(csvi.csvRecords);
	},
	
	post2Db: function () {
		//console.log('sending '+csvi.csvRecords.length+' records to OB database now');
		$('#intro').hide();
		$('#review').show();
		$('#rslts').show();

		$('#csvImportRslts').html('<ul id="postRslt"></ul>');
		for (var i=0; i<csvi.csvRecords.length; i++) {
			csvi.postOneRecd(csvi.csvRecords[i]);
		}
	},
	postOneRecd: function (aRcd) {
		//console.log(aRcd);
		$.post(csvi.url, {'mode':'postCsvData',
											'record':aRcd,
											'userid':$('#userid').val()
										 }, function (response) {
			$('#postRslt').append('<li>'+response+'</li>');
		});
	},
};

$(document).ready(csvi.init);

</script>
