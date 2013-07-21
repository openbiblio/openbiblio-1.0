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
		$('.gobkBiblioBtn').on('click',null,rpt.rtnToReport);

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
		$('#biblioDiv').hide();
		$('#workDiv').hide();
		$('#errSpace').hide();
		$('#prevBtn').disable();
		$('#nextBtn').disable();
	},
	rtnToSpecs: function () {
		$('#specsDiv').show();
		$('#reportDiv').hide();
	},
	rtnToReport: function () {
		$('#reportDiv').show();
		$('#biblioDiv').hide();
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

			$('div#report a').on('click',null,rpt.displayBiblio);

			$('#specsDiv').hide();
			$('#reportDiv').show();
		});
	},

	displayBiblio: function (e) {
		e.preventDefault();
		e.stopPropagation();
		idis.init(rpt.opts); // be sure all is ready
		var href = e.currentTarget.href,
				query = href.split('?')[1],
				args = obib.urlArgs(query); //parse query into 'named' properties
		idis.doBibidSearch(args.bibid);
		$('#biblioDiv').show();
		$('#reportDiv').hide();
	},
}
$(document).ready(rpt.init);

</script>
