<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
?>
<style>
div#listDiv h5{
	text-align: left; margin:0; padding:0;
	}
/*#pluginSet {
	margin: 0; padding: 0;
	}*/
#listDiv ul {
	list-style-type: none; text-decoration: none;
	display: block; text-align: left; margin:0; padding:0;
	}
#listDiv label {
	text-align: left; margin:0; padding:0;
	}
/*#msgDiv fieldset {
		padding: 5px;
	}*/
#msgArea h4 {
	margin: 0; padding: 2px;
	color: red;
	}
</style>


<script language="JavaScript">
"use strict";

var plm = {
	
	init: function () {
	  //init(); // part of original openbiblio js code
		plm.initWidgets();

		plm.url = 'plugMgrSrvr.php';
		plm.editForm = $('#workForm');

		$('#pluginOK').on('change',null,plm.doToggleList)

		plm.fetchPluginInfo();
		plm.resetForms();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');

	  $('#msgDiv').hide();
		$('#formArea').show();
		plm.checkPluginOK();
	},

	//------------------------------
	doToggleList: function (btnId) {
	  var state = ($('#pluginOK:checked').length>0?'Y':'N');
		var parms = 'mode=updatePluginSetting&allow_plugins_flg='+state;
		$.post(plm.url, parms, function(response) {
		  if (response.length > 0) {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			plm.checkPluginOK();
		});
	},
	checkPluginOK: function () {
	  if ($('#pluginOK:checked').length > 0) {
			plm.enableList();
		}
		else {
			plm.disableList();
		}
	},
	enableList: function () {
		$('#listArea fieldset')
				.css('color','#000000')
				.css('background-color','#ffffff');
		$('#listArea input.plugins').enable()
	},
	disableList: function () {
			$('#listArea fieldset')
					.css('color','#888888')
					.css('background-color','#cccccc');
			$('#listArea input.plugins').disable()
	},

	//------------------------------
	fetchPluginInfo: function () {
	  $.getJSON(plm.url,{mode:'getPluginList'}, function(data){
			$('#pluginList').empty();
	    var html='';
	    if (data.length > 0) {
				for (var n in data) {
					html += '<li>';
					html += '<input type="checkbox" class="plugins" id="'+data[n]['name']+'"  '
								+ ' name="'+data[n]['name']+'" value="Y" '
					      + (data[n]['OK'] == 'Y'?'checked':'')+' />';
					html += data[n]['name'];
					html += '</li>\n';
				}
			}
			else {
				html = "<h5>No Plugins found</h5>"
			}
				$('#pluginList').html(html)
				$('#pluginList input').on('click',null,plm.doUpdateList);
			plm.checkPluginOK();
		});
	},
	
	doUpdateList: function (e) {
	  var meId = e.target.id;
	  var state = ($('#'+meId+':checked').length>0?'Y':'N');
		var parms = 'mode=updatePluginList&id='+meId+'&allowPlugin='+state;
		$.post(plm.url, parms, function(response) {
		  if (response.length > 0) {
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
		});
 	}
};

$(document).ready(plm.init);
</script>
