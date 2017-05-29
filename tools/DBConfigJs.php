<script language="JavaScript">
// JavaScript Document
"use strict";

var cdc = {
	init: function () {
		cdc.url = '../tools/toolSrvr.php';

		cdc.initWidgets();
		cdc.resetForms();
		
		$('#action').on('click',null,cdc.makeChanges);
		cdc.fetchDbInfo();
		cdc.fetchCollations();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
		$('#info').show();
		$('#rsltsArea').hide();
	  $('#msgDiv').hide();
	},

	//------------------------------
	fetchDbInfo: function () {
		$.post(cdc.url, {'cat':'database',
							'mode':'getDbSrvrInfo',
						   },
			function (response) {
				$('#version').html(response.version['VERSION()']);

				var sets = '';
				var charSets = response.charSets;
				$.each(charSets, function (key, value){
					sets += '<tr><td>'+key.split('_')[2]+'</td><td>'+value+'</td>';
				});
				$('#srvrCharSets tbody').html(sets);

				var sets = '';
				var collates = response.collations;
				$.each(collates, function (key, value){
					sets += '<tr><td>'+key.split('_')[1]+'</td><td>'+value+'</td>';
				});
				$('#srvrCollations tbody').html(sets);

				var sets = '';
				var engines = response.engines;
				$.each(engines, function (key, value){
					sets += '<tr><td>'+key+'</td><td>'+value.support+'</td><td>'+value.transactions+'</td>';
				});
				$('#srvrEngines tbody').html(sets);

				var sets = '';
				var misc = response.misc;
				var firstTime = true;
				$.each(misc, function (key, value){
                    //console.log(key+' = '+value+';<br>/');
					if (firstTime) {
						if (value > '184467440737') {value = 'unlimited';}
						sets += '<tr><td>'+key+'</td><td>'+value+'</td></tr>';
						if (key == 'max_write_lock_count') { firstTime = false; }
					}
				});
				$('#srvrMiscVar tbody').html(sets);

				$('#info').show().find("tr:odd").css( "background-color", "#eee" );
		}, 'json');
	},

	//------------------------------
	fetchCollations: function () {
	    $.getJSON(cdc.url,{'cat':'database',
						 'mode':'fetchCollSet'
						},
			function(data){
    			//console.log(data);
    			var html = '',
    					prior = '';
    			$.each(data, function (key,value) {
    				if (prior != value) {
    					html += '<option class="bold" value="">'+value+'</option>';
    					prior = value;
    				}
    				html += '<option ';
    				if (key == 'utf8_general_ci') html += 'selected ';
    				html += 'value="'+value+'">'+key+'</option>';
    			});
			$('#collSet').html(html);
		}, 'json');
	},
	makeChanges: function () {
		$('#rsltsArea').show();
		var choice = $('#collSet option:selected');
		$.post(cdc.url, {'cat':'database', 
										 'mode':'changeColl',
										 'charset':choice.val(),
										 'collation':choice.text(),
									  },
						function (response) {
			//console.log(response);
			$('#chgRslts').html(response);
			//$('#userMsg').html(response);
			//#('#msgDiv').show()
		});
	},
}

$(document).ready(cdc.init);
</script>
