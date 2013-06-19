<script language="JavaScript">
// JavaScript Document
"use strict";
var dio = {
	init: function () {
		dio.url = 'dioSearchSrvr.php';

		dio.initWidgets();
		dio.resetForms();
		
		$('#doiForm').bind('submit',null,dio.search);
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
		$('#rsltsArea').hide();
	  $('#msgDiv').hide();
	},
	
	//------------------------------
	search: function (e) {
		e.preventDefault;
		e.stopPropagation();

  	var Qr=$('#dioCd').val()
		if(Qr){
			if(Qr.indexOf('doi://')==0)Qr=Qr.substr(6);
			if(Qr.indexOf('doi:')==0)Qr=Qr.substr(4)

			//example doi: 10.1007/s10531-011-0143-8
			var newLoc = 'http://dx.doi.org/'+escape(Qr);
			window.open(newLoc,'doiWin');
			return false;
		}
	},

}

$(document).ready(dio.init);
</script>
