// JavaScript Document
//------------------------------------------------------------------------------
// biblio_bulkDel Javascript
bbd = {
	init: function () {
		// get header stuff going first
		bbd.initWidgets();

		bbd.url = 'biblio_server.php';
		bbd.fetchCrntMbrInfo();

		$('#bulkDel_btn').bind('click',null,bbd.doBulkDelete);

		// begin processing;
		bbd.resetForms();
	},

	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
	  //console.log('resetting Entry Form');
	  $('#crntMbrDiv').hide();
		$('p.error').hide();
		
		$('#barcodes').html('');
		$('input#del_items').prop("checked", false);
		
	  $('#entryFormDiv').show();
	  $('#confirmFormDiv').show();
	},
	
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(bbd.url,{mode:'getOpts'}, function(jsonData){
	    bbd.opts = jsonData
		});
	},
	fetchCrntMbrInfo: function () {
	  $.get(bbd.url,{mode:'getCrntMbrInfo'}, function(data){
			$('#crntMbrDiv').empty().html(data).show();
		});
	},
	
	//------------------------------
	doBulkDelete: function () {
		// lets first confirm barcodes are valid
		var cpyList = $('#barcodes').val();
	 	$.getJSON(bbd.url,{mode:'getBibsFrmCopies', cpyList:cpyList}, function(jsonData){
	 		bbd.cpyAray = jsonData[0];
	 		bbd.bibAray = jsonData[1];
	 		bbd.errAray = jsonData[2];
	 		bbd.barcdStr= jsonData[3].join('\n');
	  	$('#barcodes').val(bbd.barcdStr);
console.log('jsonData:'+jsonData);	  	
	 		if (bbd.errAray[0]) {
				//console.log('we have an error');	 	
				$('#errSpace').html(bbd.errAray[0].msg).show();
				return false;
			}

			// now see if user wants to continue with deletion
			var txt1 = bbd.cpyAray.length,
					txt2 = bbd.bibAray.length,
					//msg = <?php echo T('Bulk Delete Confirm',array('copy'=>"$txt1",'item'=>"$txt2")); ?>;
					msg = 'OK to delete '+txt1+' copies from '+txt2+' titles?';
			if (!confirm('"'+msg+'"')) {
				// delete rejected
				return false;	
			}
			else {
				// delete OK
				$.post(bbd.url,{mode:'deleteMultiCopies', cpyList:bbd.cpyAray}, function(response) {
					$('#errSpace').html(response).show();
					
//					if ( $('#del_items').prop("checked") ) {
//						$.post(bbd.url,{mode:'deleteMultiBiblios', bibList:bbd.bibAray}, function(response) {
//							$('#errSpace').html(response).show();
//						});
//					};

				});
			};						
	 	});	
	},
	
}
$(document).ready(bbd.init);
