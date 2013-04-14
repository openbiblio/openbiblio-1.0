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
		csvi.initWidgets();

		csvi.url = '../catalog/importServer.php';
		csvi.form = $('#specForm');
		
		$('#imptSrce').bind('change',null,function () {$('#imptBtn').enable();});
		$("#imptBtn").bind('click',null,csvi.processImportFile);
		$("#revuBkupBtn").bind('click',null,csvi.rtnToIntro);
		$("#rsltBkupBtn").bind('click',null,csvi.rtnToIntro);
		
		csvi.resetForm();
	},
	
	//------------------------------
	initWidgets: function () {
		$('section').hide();
	},
	
	resetForm: function () {
		//console.log('resetting Search Form');
		$('#review').hide();
		$('#rslts').hide();
		$('#imptBtn').disable();
		$('#intro').show();
	},
	rtnToIntro: function () {
		csvi.resetForm();
		$('#imptBtn').enable();
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
														var testTF = $('[name="test"]:checked').val();
														if ( testTF == 'true') {
															$('#rslts').hide();
															$('#review').show();
															csvi.displayColHeads();	
														console.log('back from displayColHeads()');	
															$('#rslts').hide();
															$('#review').show();
														} else {
															//console.log('posting');		
															$('#review').hide();
															$('#rslts').show();
														}
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
		headings = record.split("\t");
		colHeads = $('#colHeads');
		colHeads.html(" ");		
		for( var i=0; headings.length-1; i++) {
			heading = headings[i].trim();
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
			    break;
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
	}
	
};

$(document).ready(csvi.init);

</script>
