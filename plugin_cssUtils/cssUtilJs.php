<script language="JavaScript">
// JavaScript Document
"use strict";

var csu = {
	init: function () {
		csu.url = 'cssUtilSrvr.php';

		csu.initWidgets();
		csu.resetForms();
		
		$('#orfnChkBtn').bind('click',null,csu.findOrfans);

		//$('#dupChkBtn').bind('click',null,csu.findDupes);
		//$('#absntChkBtn').bind('click',null,csu.findAbsnts);
		//$('#maybeChkBtn').bind('click',null,csu.findMaybes);

		//csu.fetchModuleList();
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
	  $.getJSON(csu.url,{'cat':'locale', 
											 'mode':'getModuleList'}, function(data){
			csu.obMods = data.sort();
		});
	},
	
	//------------------------------
	findOrfans: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(csu.url, {'mode':'ck4CssUnused',
									  },
						function (response) {
			$('#rslts').html(response);
		});
	},

	findDupes: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(csu.url, {'cat':'locale', 
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
		$.each(csu.obMods, function (n,module) {
			var choice = $('#locSet option:selected');
			$.post(csu.url, {'cat':'locale',
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
		$.each(csu.obMods, function (n,module) {
			var choice = $('#locSet option:selected');
			$.post(csu.url, {'cat':'locale',
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

$(document).ready(csu.init);
</script>
