<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

var oed = {
	<?php
	require_once(REL(__FILE__, "../classes/Localize.php"));
	$jsLoc = new Localize('OBIB_LOCALE',$tab);

	echo 'editHdr 	 : "'.T("Online Options").'",'."\n";
	echo 'successMsg : "'.T("Update successful").'",'."\n";
	?>
	init: function () {
		//console.log('in oed init');
	  
		oed.initWidgets();

		oed.url = '../admin/adminSrvr.php';
		oed.editForm = $('#editForm');

	  	$('#editHdr').html(<?php echo "'".T("Online Options")."'"; ?>);
		$('#editForm').on('submit',null,oed.doUpdate);

		oed.fetchOpts();
		oed.resetForms()
	},

	//------------------------------
	initWidgets: function () {
	},
	
	resetForms: function () {
		//console.log('resetting!');
		obib.hideMsg();
		$('#lookupVal').focus();
	},
	
	doBackToList: function () {
		oed.fetchOpts();
		$('#lookupVal').focus();
	},

	//------------------------------
	fetchOpts: function () {
	  $.post(oed.url,{ 'cat':'opts', 'mode':'getOpts'}, function(data){
	  		$('#protocol').val(data.protocol);
			$('#maxHits').val(data.maxHits);
			$('#timeout').val(data.timeout);
			$('#keepDashes').val([data.keepDashes]);
			$('#callNmbrType').val(data.callNmbrType);
			$('#autoDewey').val([data.autoDewey]);
			$('#defaultDewey').val(data.defaultDewey);
			$('#autoCutter').val([data.autoCutter]);
			$('#cutterType').val(data.cutterType);
			$('#cutterWord').val(data.cutterWord);
			$('#noiseWords').val(data.noiseWords);
			$('#autoCollect').val([data.autoCollect]);
			$('#fictionName').val(data.fictionName);
			$('#fictionCode').val(data.fictionCode);
			$('#fictionLoC').val(data.fictionLoc);
			$('#fictionDew').val(data.fictionDew);
		}, 'json');
	},

	doValidate: function () {
		//console.log('user input validation not available!!!!, see admin/settings_edit');
		return true;
	},

	doUpdate: function (e) {
	  if (!oed.doValidate()) return;

		//obib.hideMsg();
		$('#mode').val('updateOpts');
		e.preventDefault();
		e.stopPropagation();
		var parms = $('#editForm').serialize();
		//console.log(parms);
		$.post(oed.url, parms, function(response) {
			//console.log(response);
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				obib.showMsg(response);
			} else if (response.substr(0,1)=='1'){
				obib.showMsg(oed.successMsg);
			  	oed.doBackToList();
			} else {
				obib.showMsg(response);
			  	oed.doBackToList();
			}
		}, 'JSON');
		return false;
	}
};

//$(document).ready(oed.init);
oed.init();
</script>
