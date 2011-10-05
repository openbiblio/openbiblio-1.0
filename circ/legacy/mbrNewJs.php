<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<script language="JavaScript" defer>
// JavaScript Document
//------------------------------------------------------------------------------

mnf = {
	init: function () {
		mnf.url = 'memberServer.php';

		// get header stuff going first
		mnf.initWidgets();
		mnf.resetForms();

		$('form').bind('submit',null,mnf.doSubmits);
	},
	
	//------------------------------
	initWidgets: function () {
	},
	
	resetForms: function () {
	  //console.log('resetting Search Form');
		$('p.error, input.error').html('').hide();
	  $('#newDiv').show();
		$('#msgDiv').hide();
	},

	doSubmits: function (e) {
		e.preventDefault();
		e.stopPropagation();
		var theId = $('input[type="submit"]:focus').attr('id');
		//console.log('the btn id is: '+theId);
		switch (theId) {
			case 'addNewBtn':		mnf.doMbrAdd();		break;
		}
	},
	
	//------------------------------
	doMbrAdd: function () {
		$('#msgDiv').hide();
		$('#mode').val('addNewMember');
		var parms = $('#mewmbrForm').serialize();
		//console.log('updating: '+parms);
		$.post(mnf.url, parms, function(response) {
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
$(document).ready(mnf.init);
</script>
