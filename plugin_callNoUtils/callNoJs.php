<script language="JavaScript">
// JavaScript Document
"use strict";

var cn = {
	init: function () {
		cn.url = 'callNoSrvr.php';

		cn.resetForms();
		cn.toggleFields();

		$("#callNoMode").change(function () {
			cn.toggleFields();
		});
		
		$('#callNoChkBtn').bind('click',null,cn.findCallNos);
	},
	collect_checkboxes: function () {
		var values = $('input:checkbox:checked').map(function () {
  			return this.name;
		}).get();
		return values;
	},
	resetForms: function () {
		$('#rsltsArea').hide();
	    $('#msgDiv').hide();
        $('#callNoChkBtn').focus();
	},
	toggleFields: function () {
		if ($("#callNoMode").val() == "search")
			$("#call_number_schemata").hide();
		else
			$("#call_number_schemata").show();
	},
	
	//------------------------------
	findCallNos: function () {
		$('#rslts').html('');
		$('#rsltsArea').show();
		var choice = $('#locSet option:selected');
		$.post(cn.url, {'mode':$("#callNoMode").val(),
			'schemata':cn.collect_checkboxes()
									  },
						function (response) {
			$('#rslts').html(response);
		});
	},

}

$(document).ready(cn.init);
</script>
