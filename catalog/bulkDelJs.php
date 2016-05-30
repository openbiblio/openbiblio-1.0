<script language="JavaScript" >
// JavaScript Document
//------------------------------------------------------------------------------
// biblio_bulkDel Javascript
"use strict";

var bbd = {
	init: function () {
		// get header stuff going first
		bbd.initWidgets();

		bbd.url = 'catalogServer.php';
		bbd.fetchCrntMbrInfo();

		$('#bulkDel_btn').on('click',null,bbd.getEntries);

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
		$('#bulkDel_formDiv').show();
		
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
    getEntries: function () {
        $(":radio[id^='byB']:checked").each(function (n) {
            var theId = (String)(this.id).trim().substr(0,7);
            if (theId == 'byBarcd') {
console.log('got barcds');
                var cpyList = $('#barcodes').val();
                bbd.doDeleteCpys(cpyList);
           } else if (theId == 'byBibid') {
console.log('got bibIdds');
                var bibList = $('#bibids').val();
                bbd.bibAray = bibList.split(',');
                bbd.doDeleteBibs(bbd.bibAray);
           } else {
                console.log('invalid bulk delete selection: '+this.id);
           }
        });

    },

	doDeleteCpys: function (cpyList) {
		// lets first confirm barcodes are valid
	 	$.getJSON(bbd.url,{mode:'getBibsFrmCopies', cpyList:cpyList}, function(jsonData){
	 		bbd.cpyAray = jsonData[0];
	 		bbd.bibAray = jsonData[1];
	 		bbd.errAray = jsonData[2];
	 		bbd.barcdStr= jsonData[3].join('\n');
	  	    $('#barcodes').val(bbd.barcdStr);
console.log('jsonData:'+jsonData);	  	
	 		if (bbd.errAray[0]) {
console.log('we have an error');	 	
				$('#errSpace').html(bbd.errAray[0].msg).show();
				return false;
			}

			// now see if user wants to continue with deletion
			var txt1 = bbd.cpyAray.length,
			txt2 = bbd.bibAray.length,
			//msg = <?php echo T("Bulk Delete Confirm",array('copy'=>"$txt1",'item'=>"$txt2")); ?>;
			msg = 'OK to delete '+txt1+' copies from '+txt2+' titles?';
			if (!confirm('"'+msg+'"')) {
				// delete rejected
				return false;	
			} else {
				// delete OK
				$.post(bbd.url,{mode:'deleteMultiCopies', cpyList:bbd.cpyAray}, function(response) {
					$('#errSpace').html(response).show();
					
					if ( $('#del_items:checked').val() == 'Y') {
dconsole.log('deleting parent books');
						//$.post(bbd.url,{mode:'deleteMultiBiblios', bibList:bbd.bibAray}, function(response) {
						//	$('#errSpace').html(response).show();
						//});
                        bbd.deleteBibs();
					};

				});
			};						
	 	});	
	},

    doDeleteBibs: function (bibAray) {
		var msg = 'OK to delete '+bibAray.length+' titles?';
		if (!confirm('"'+msg+'"')) {
			// delete rejected
			return false;
		} else {
            bbd.deleteBibs(bibAray);
        }
    },

    deleteBibs: function (bibAray) {
        $.post(bbd.url,{mode:'deleteMultiBiblios', bibList:bbd.bibAray}, function(response) {
        	$('#errSpace').html(response).show();
		});
    },
	
};

$(document).ready(bbd.init);
</script>
