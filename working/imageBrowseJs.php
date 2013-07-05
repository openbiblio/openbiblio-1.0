<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";
var img = {
	init: function () {
		img.url = '../working/reportSrvr.php';
    img.listSrvr = '../shared/listSrvr.php';
		img.initWidgets();

		$('#orderBy').on('change',null,img.fetchFotoPage);

		img.resetForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		img.pageNmbr = 0;
		img.srchType = '';
    img.fetchFotoPage();
		$('#rptArea').show();
	},

	//------------------------------
	getNextPage: function () {
    img.fetchFotoPage();
	},

	fetchFotoPage: function () {
		var orderBy = $('#orderBy option:selected').val();
	  $.getJSON(img.url,{'mode':				'getPage',
											 'orderBy':			orderBy,
                       'searchType':	img.srchType,
											 'page':				img.pageNmbr,
											 'tab':					$('#tab').val(),
											}, function(data){
			var table = '<table>'+data.tbl+'</table>';
			img.pageNmbr = data.curPage;
			$('#gallery').html(table).show();
			$('.pageList').html(data.ndx).show();
			$('.countBox').html('<?php echo T("results found.");?>: '+data.nmbr).show();
		});
	},

}
$(document).ready(img.init);
</script>
