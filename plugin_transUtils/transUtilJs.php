<script language="JavaScript">
// JavaScript Document
"use strict";

var tru = {
	init: function () {
		tru.url = 'transSrvr.php';

		tru.initWidgets();
		tru.resetForms();
		
		$('#dupChkBtn').bind('click',null,tru.findDupes);
		$('#orfnChkBtn').bind('click',null,tru.findOrfans);
		$('#absntChkBtn').bind('click',null,tru.findAbsnts);
		$('#maybeChkBtn').bind('click',null,tru.findMaybes);

		tru.fetchLocaleList();
		tru.fetchModuleList();
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
	fetchLocaleList: function () {
	  $.getJSON(tru.url,{'cat':'locale', 
											 'mode':'fetchLocaleList'}, function(data){
			var html = ''; 
			$.each(data, function (key,value) {
				html += '<option ';
				if (key == '<?php echo $Locale ?>') {
					$('#crntLoc').html(value);
					html += 'selected ';
				}
				html += 'value="'+key+'">'+value+'</option>';
			});
			$('#locSet').html(html);
		});
	},
	fetchModuleList: function () {
	  $.getJSON(tru.url,{'cat':'locale', 
											 'mode':'fetchModuleList'}, function(data){
			tru.obMods = data.sort();
		});
	},
	
	//------------------------------
	findDupes: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(tru.url, {'cat':'locale', 
										 'mode':'ck4TransDupes',
										 'locale':choice.val(),
									  },
						function (response) {
			$('#rslts').html(response);
		});
	},
	findOrfans: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(tru.url, {'cat':'locale', 
										 'mode':'ck4TransUnused',
										 'locale':choice.val(),
									  },
						function (response) {
			$('#rslts').html(response);
		});
	},
	findAbsnts: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		$.each(tru.obMods, function (n,module) {
			var choice = $('#locSet option:selected');
			$.post(tru.url, {'cat':'locale',
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
		$.each(tru.obMods, function (n,module) {
			var choice = $('#locSet option:selected');
			$.post(tru.url, {'cat':'locale',
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

$(document).ready(tru.init);
</script>
