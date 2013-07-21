<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";
var rpt = {
	init: function () {
		rpt.url = '../reports/reportSrvr.php';
		rpt.initWidgets();

		rpt.rptType = '<?php echo $_GET['type']; ?>';

		$('#orderBy').on('change',null,function () {
			rpt.fetchFotoPage(0);
		});
		$('.nextBtn').on('click',null,rpt.getNextPage);
		$('.prevBtn').on('click',null,rpt.getPrevPage);
		$('.gobkRptBtn').on('click',null,rpt.rtnToSpecs);

		$('#reportcriteriaform').on('submit', null, rpt.doSearch);

		rpt.resetForm();
    rpt.getCriteriaForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		$('#specsDiv').show();
		$('#reportDiv').hide();
		$('#detailDiv').hide();
		$('#workDiv').hide();
		$('#errSpace').hide();
		$('#prevBtn').disable();
		$('#nextBtn').disable();
	},
	rtnToSpecs: function () {
		$('#specsDiv').show();
		$('#reportDiv').hide();
	},

	//------------------------------
	fetchOpts: function () {
	  $.getJSON(rpt.url,{mode:'getOpts'}, function(jsonData){
	    rpt.opts = jsonData;
		});
	},
	getCriteriaForm: function () {
		$.get(rpt.url, {'mode':'getCriteriaForm',
												'type':rpt.rptType,
											 }, function (resp){
			var parts = resp.split('|');
			$('#specs').html(parts[1]);
			$('#type').val(rpt.rptType);
			$('#pageTitle').html(parts[0]);
			$('#title').val(parts[0]);
		});
	},

	//------------------------------
	getNextPage:function () {
		$('.nextBtn').disable();
		rpt.fetchFotoPage(rpt.nextPageItem);
	},
	getPrevPage:function () {
		$('.prevBtn').disable();
		rpt.fetchFotoPage(rpt.prevPageItem);
	},

	doSearch: function (e) {
		e.preventDefault();
		e.stopPropagation();
    var params = $('#reportcriteriaform').serialize();
		$.post(rpt.url, params, function (response) {
			var parts = response.split('|');
			$('#rptCount').html(parts[0]);
			$('#report').html(parts[1]);

			$('#specsDiv').hide();
			$('#reportDiv').show();
		});
	},
}
$(document).ready(rpt.init);

</script>
