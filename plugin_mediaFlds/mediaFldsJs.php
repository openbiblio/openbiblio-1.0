<script language="JavaScript">
// JavaScript Document
"use strict";

var mlo = {
	init: function () {
		mlo.url = 'mediaFldsSrvr.php';
		mlo.listSrvr = '../shared/listSrvr.php'
		mlo.initWidgets();
		mlo.resetForms();
		
		$('#exportBtn').on('click',null,mlo.exportLayout);
		$('#inportBtn').on('click',null,mlo.inportLayout);
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
    mlo.fetchMediaList();
		$('#rsltsArea').hide();
	  $('#msgDiv').hide();
	},
	
	//------------------------------
	fetchMediaList: function () {
	  $.getJSON(mlo.listSrvr,{mode:'getMediaList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			};
			$('#material_cd').html(html);
		});
	},

	//------------------------------
	exportLayout: function () {
		$('#msgDiv').hide();
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#material_cd option:selected');
		$.get(mlo.url, {'mode':'exportLayout',
										 		'material_cd':choice.val(),
									  	 },
			function (response) {
			$('#rslts').html(response);
			}
		);
	},
	inportLayout: function (e) {
		$('#msgDiv').hide();
		$('#rslts').html('');
		$('#rsltsArea').show();
		var reader = new FileReader();
		var fn = $('#newLayout').prop('files');
		reader.readAsText(fn[0]);
		reader.onload = function () {
			var text = reader.result;
			$('#rslts').html(text);
		};
	},

}

$(document).ready(mlo.init);
</script>
