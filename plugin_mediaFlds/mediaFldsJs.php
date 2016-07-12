<script language="JavaScript">
// JavaScript Document
"use strict";

var mlo = {
	init: function () {
		mlo.url = '../plugin_mediaFlds/mediaFldsSrvr.php';
		mlo.listSrvr = '../shared/listSrvr.php';
		mlo.initWidgets();
		mlo.resetForms();
		
		$('#exportBtn').on('click',null,mlo.exportLayout);
		$('#importBtn').on('click',null,mlo.importLayout);
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
			$('#exportMedia').html(html);
			$('#importMedia').html(html);
		});
	},

	//------------------------------
	exportLayout: function () {
		$('#msgDiv').hide();
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#exportMedia option:selected');
		$.get(mlo.url, {'mode':'exportLayout',
										 		'material_cd':choice.val(),
									  	 },
			function (response) {
			$('#rslts').html(response);
			}
		);
	},
	importLayout: function (e) {
		$('#msgDiv').hide();
		$('#rslts').html('');
		$('#rsltsArea').show();
		var reader = new FileReader();
		var fn = $('#newLayout').prop('files');
		var choice = $('#importMedia option:selected');
		reader.readAsText(fn[0]);
		reader.onload = function () {
			var json = reader.result;
			$.post(mlo.url, {'mode':'importLayout',
											 'layout':json,
											 'material_cd':choice.val(),
											},
				function (response) {
					$('#rslts').html(response);
				}
			);
		};
	},

}

$(document).ready(mlo.init);
</script>
