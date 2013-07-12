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
		img.url = '../working/imageSrvr.php';
		img.initWidgets();

		$('#orderBy').on('change',null,function () {
			img.fetchFotoPage(0);
		});
		$('.nextBtn').on('click',null,img.getNextPage);
		$('.prevBtn').on('click',null,img.getPrevPage);
		$('.gobkBiblioBtn').on('click',null,img.rtnToGallery);

		img.resetForm();
    img.fetchFotoPage();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		img.firstItem = 0;
		img.srchType = '';
		$('#biblioDiv').hide();
		$('#rptDiv').show();
		$('#prevBtn').disable();
		$('#nextBtn').disable();
	},
	rtnToGallery: function () {
		$('#biblioDiv').hide();
		$('#rptDiv').show();
	},

	//------------------------------
	fetchOpts: function () {
	  $.getJSON(img.url,{mode:'getOpts'}, function(jsonData){
	    img.opts = jsonData
		});
	},
	//------------------------------
	getNextPage:function () {
		$('.nextBtn').disable();
		img.fetchFotoPage(img.nextPageItem);
	},

	getPrevPage:function () {
		$('.prevBtn').disable();
		img.fetchFotoPage(img.prevPageItem);
	},

	fetchFotoPage: function (firstItem) {
		if (firstItem === undefined) firstItem = img.firstItem;
		var orderBy = $('#orderBy option:selected').val();
	  $.getJSON(img.url,{'mode':				'getPage',
											 'orderBy':			orderBy,
											 'firstItem':		firstItem,
											 'tab':					$('#tab').val(),
											}, function(data){
			img.firstItem = parseInt(data.firstItem);
			img.lastItem = parseInt(data.lastItem);
			img.perPage = parseInt(data.perPage);
			var ttlNmbr = parseInt(data.nmbr);
			$('.countBox').html((img.firstItem+1)+' - '+img.lastItem+' <?php echo T("of");?> '+ttlNmbr).show();

			var $table = $('#fotos'),
					cells = data.tbl,
					tab = '<?php echo $tab;?>',
					perLine = 7,
					html = '',
					cntr = 0;
			$table.html('<tr>');
			for (var entry in cells) {
				var cell = cells[entry];
				if (cntr == perLine) {
					$table.append('</tr>'+"\n"+'<tr>');
					cntr = 0;
				}
				var bibid = cell['bibid'];
				html  = '<td valign="bottom" align="center">'+"\n";
				html += '	<div class="galleryBox">'+"\n";
				html += '		<div><img id="img-'+bibid+'" src="../photos/'+cell['url']+'" class="biblioImage" /></div>'+"\n";
				html += '		<div class="smallType">'+"\n";
				html += '			<a href="#" id="a-'+bibid+'" >'+"\n";
				html += '			<output >'+cell[orderBy]+'</output>'+"\n";
				html += '		</a></div>'+"\n";
				html += '</td>'+"\n";
				cntr++;
				$table.append(html);

				$('#fotos img').on('click',null,function (e) {
					e.preventDefault(); e.stopPropagation();
					var id = e.currentTarget.id;
					$('#'+id).toggleClass('resize');
				});
				$('#fotos a').on('click',null,function (e) {
					e.preventDefault(); e.stopPropagation();
					idis.init(img.opts); // be sure all is ready
					var idParts = e.currentTarget.id.split('-');
					idis.doBibidSearch(idParts[1]);
					$('#biblioDiv').show();
					$('#rptDiv').hide();
				});
			}
			$table.append('</tr>');
			$('#gallery').show();

			// enable or disable next / prev buttons
			if(img.firstItem >= img.perPage){
				img.prevPageItem = img.firstItem - img.perPage;
				$('.prevBtn').enable();
			} else {
				$('.prevBtn').disable();
			}
			if(((img.perPage+img.firstItem) <= img.lastItem) && (ttlNmbr > img.lastItem)){
				img.nextPageItem = img.perPage + img.firstItem;
				$('.nextBtn').enable();
			} else {
				$('.nextBtn').disable();
			}
		});
	},

}
$(document).ready(img.init);
</script>
