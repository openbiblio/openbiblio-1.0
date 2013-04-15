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
		
		$('#imptSrce').bind('change',null,function () {$('#imptBtn').enable();});
		$("#imptBtn").bind('click',null,csvi.processImportFile);
		$("#helpBtn").bind('click',null,function () {$('.help').toggle();});
		$("#revuBkupBtn").bind('click',null,csvi.rtnToIntro);
		$("#rsltBkupBtn").bind('click',null,csvi.rtnToIntro);
		$("#bcdDeflt").bind('change',null,function () {csvi.bcdOpt = $('bcdDflt').val();});
		
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
		return $('#mediaCd').val();
	},
	getOpacFlg: function () {
		return $('#opacFlg').val();
	},
	getShowAllFlg: function () {
		return ($('#showAll').val() == 'Y'?true:false);
	},
	getTestTF: function () {
		return $('[name="test"]:checked').val();
	},
	isDupBarCd: function (barCd) {
/*
  					// Check for uniqueness with existing barcodes and new entries read.
  					$barcode = $rec["barcode_nmbr"];
					  if ($barcode != "") {
				    	if (in_array($barcode, $newBarcodes)) {
				    	  array_push($localErrors, T("biblioCopyQueryErr1"));
				    	  $validate = false;
				   	 } else {
								echo "Barcode not present<br />";
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
														if ( csvi.getTestTF() == 'true' ) {
															$('#rslts').hide();
															$('#review').show();
															csvi.displayColHeads();	
														} else {
															$('#review').hide();
															$('#rslts').show();
														}
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
	//----//
	processRows: function () {
		var rec = {};	// output record
		var csvRcrds = $('#csvRcrds'); csvRcrds.html(' ');
		var csvErrs = $('#csvErrs'); csvErrs.html(' ');
		var showAll = csvi.getShowAllFlg();
console.log('showAll='+showAll);
console.log('bcdDflt='+$('#bcdDeflt').val());	
	
		// for use with barcodes
		var width = <?php echo $_SESSION[item_barcode_width]; ?>;
	 	if( width <= 1 ) var w = 13; else var w = width;
	  	
		for (var i=1; i<csvi.File.length; i++) {
			//console.log('working row #'+i+' of '+csvi.File.length+' : '+csvi.File[i]);
					
			// break an input line into an array of its parts
			var data = csvi.File[i].split(csvi.fldTerminator);	

			if (showAll) csvRcrds.append(' <tr><td colspan="3">&nbsp</td></tr>\n');
    	if (showAll) csvRcrds.append(' <tr><td colspan="3"> - - - - - - - Line #'+i+' - - - - - - - - </td></tr>\n');

  		var mandatoryCols = {
  			'coll'  : false,		// collection name
  			'media' : false,		// media type name
  			//'099$a' : false,		// local call number
    		//'100$a' : false,		// author
    		//'245$a' : false,		// title
			};
			
	  	// for use with MARC tags
			var fields = {};	
			
			for (var n=0; n<csvi.headings.length; n++) {
    		if ( (data[n] === undefined) || (data[n] == '') ) {
					var entry = '';
				} else {
    			var entry = data[n].trim();
				}
    		var target = csvi.headings[n].trim();
				//console.log("working "+(n)+" of "+csvi.headings.length+" with entry '"+entry+"' in column '"+target+"'");    

				// if current column is a 'mandatory' is present in headings mark as 'seen'
    		if ( mandatoryCols[target] ) mandatoryCols[target] = true;
    		
    		switch (target) {
    			case 'barCo':
    				// pad bar code to proper size
      			rec['barcode_nmbr'] = flos.pad(entry, w, "0");
						if (csvi.isDupBarCd()) {
							csvErrs.append(' <tr><td colspan="3">Line #'+i+' Bar Code '+rec['barcode_nmbr']+' is a duplicate</td></tr>\n');
							//continue;
						} else {
    	  			if (showAll) csvRcrds.append(" <tr><td>Bar Code</td><td>&nbsp;</td><td>"+rec['barcode_nmbr']+"</td></tr>\n");
    	  		}
      			if ( ( parseInt(rec['barcode_nmbr']) == 0 ) && ( csvi.bcdDflt >= csvi.BCD_IF ) ){
							rec['barcode_nmbr'] = 'autogen';
						}
    				break;
			    case "coll":
			    	var thisOne = csvi.collections.indexOf(entry);
			      if (thisOne < 0) {
			        //array_push($localWarnings,T("CSVCollUnknown").": ".$entry."; using '".$dfltColl."'.");
			        thisOne = csvi.getDfltColl();
							csvErrs.append(' <tr><td colspan="3">Line #'+i+" Collection '"+entry+"' invalid, using default</td></tr>\n");
			      }		      
			      rec['collection_cd'] = thisOne;
      			if (showAll) csvRcrds.append("  <tr><td>Collection</td><td>&nbsp;</td><td>"+csvi.collections[thisOne]+"</td></tr>\n");
			      break;
    			case 'media':
			    	var thisOne = csvi.mediaTypes.indexOf(entry);
			      if (thisOne < 0) {
			        //array_push($localWarnings,T("CSVCollUnknown").": ".$entry."; using '".$dfltMed."'.");
			        thisOne = csvi.getDfltMedia();
							csvErrs.append(' <tr><td colspan="3">Line #'+i+" Media '"+entry+"' invalid, using default</td></tr>\n");
			      }
			      rec['material_cd'] = thisOne;
      			if (showAll) csvRcrds.append("  <tr><td>Media Type</td><td>&nbsp;</td><td>"+csvi.mediaTypes[thisOne]+"</td></tr>\n");
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
      			if (showAll) csvRcrds.append("  <tr><td>Show OPAC</td><td>&nbsp;</td>" +
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
			
			// check for barcode present, and add if user wishes
console.log('bcd='+rec['barcode_nmbr']);		
console.log('dflt='+$('#bcdDeflt').val());	
			if ((! rec['barcode_nmbr'] ) && ( $('#bcdDeflt').val() == csvi.BCD_ALWAYS )){
console.log('barcode is needed');
				csvErrs.append(' <tr><td colspan="3">Line #'+i+" barcode missing, auto-generating</td></tr>\n");
      	rec['barcode_nmbr'] = 'autogen';
			}
			
			// now add 'fields array' to rec
			rec['fields'] = fields
			console.log(rec);			
		}	
	},
};

$(document).ready(csvi.init);

</script>
