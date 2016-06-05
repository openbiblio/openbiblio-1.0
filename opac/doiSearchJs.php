<script language="JavaScript">
// JavaScript Document
"use strict";
var doi = {
	init: function () {
		doi.url = 'doiSearchSrvr.php';

		doi.initWidgets();
		doi.resetForms();
		
		$('#doiForm').bind('submit',null,doi.search);
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

  	var Qr=$('#doiCd').val()
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

$(document).ready(doi.init);
</script>
