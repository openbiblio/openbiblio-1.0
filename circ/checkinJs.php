<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<script language="JavaScript" defer>
// JavaScript Document
//------------------------------------------------------------------------------

var opacMode = true;

chk = {
	init: function () {
		chk.url = 'circulationServer.php';

		// get header stuff going first
		chk.initWidgets();
		chk.resetForms();
		chk.fetchOpts();
		
		$('form').bind('submit',null,function (e) {
			e.preventDefault();
			e.stopPropagation();
			var theId = $('input[type="submit"]:focus').attr('id');
			//console.log('the btn id is: '+theId);
			switch (theId) {
				case 'addToCrtBtn':		chk.doCheckin();		break;
			}
		});

	},
	//------------------------------
	initWidgets: function () {
	},
	
	resetForms: function () {
	  //console.log('resetting Search Form');
		$('p.error, input.error').html('').hide();
	  $('#ckinDiv').show();
		$('#msgDiv').hide();
	},

	//------------------------------
	fetchOpts: function () {
	  $.getJSON(chk.url,{mode:'getOpts'}, function(jsonData){
	    chk.opts = jsonData
		});
	},
	//------------------------------
	doCheckin: function () {
		var barcd = $.trim($('#barcodeNmbr').val());
		barcd = flos.pad(barcd,chk.opts.item_barcode_width,'0');
		$('#barcodeNmbr').val(barcd); // redisplay expanded value

		var parms = $('#chekinForm').serialize();
		$.post(chk.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html('<?php echo T('Added');?>');
					$('#updateMsg').show();
				}
				$('#msgArea').html('Added!');
				$('#msgDiv').show().hide(10000);
			}
		});
		return false;
	},
};
$(document).ready(chk.init);

</script>
