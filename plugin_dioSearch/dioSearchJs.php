<script language="JavaScript">
// JavaScript Document
"use strict";

var dio = {
	init: function () {
		dio.url = 'dioSearchSrvr.php';

		dio.initWidgets();
		dio.resetForms();
		
		$('#orfnChkBtn').bind('click',null,dio.findOrfans);

		//$('#dupChkBtn').bind('click',null,dio.findDupes);
		//$('#absntChkBtn').bind('click',null,dio.findAbsnts);
		//$('#maybeChkBtn').bind('click',null,dio.findMaybes);

		//dio.fetchModuleList();
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
	fetchModuleList: function () {
	  $.getJSON(dio.url,{'cat':'locale', 
											 'mode':'getModuleList'}, function(data){
			dio.obMods = data.sort();
		});
	},
	
	//------------------------------
	findOrfans: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(dio.url, {'mode':'ck4CssUnused',
									  },
						function (response) {
			$('#rslts').html(response);
		});
	},

	findDupes: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(dio.url, {'cat':'locale', 
										 'mode':'ck4TransDupes',
										 'locale':choice.val(),
									  },
						function (response) {
			$('#rslts').html(response);
		});
	},
	findAbsnts: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		$.each(dio.obMods, function (n,module) {
			var choice = $('#locSet option:selected');
			$.post(dio.url, {'cat':'locale',
											 'mode':'ck4TransNeeded',
											 'locale':choice.val(),
											 'module':module,
										  },
							function (response) {
				$('#rslts').append(response);
			});
		});
	},

	findMaybes: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		$.each(dio.obMods, function (n,module) {
			var choice = $('#locSet option:selected');
			$.post(dio.url, {'cat':'locale',
											 'mode':'ck4TransMaybe',
											 'locale':choice.val(),
											 'module':module,
										  },
							function (response) {
				$('#rslts').append(response);
			});
		});
	},

}

$(document).ready(dio.init);
</script>
