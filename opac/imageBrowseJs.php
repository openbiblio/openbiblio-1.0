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
		img.url = '../opac/imageSrvr.php';
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
		$('#workDiv').hide();
		$('#biblioDiv').hide();
		$('#fotoDiv').show();
		$('#prevBtn').disable();
		$('#nextBtn').disable();
	},
	rtnToGallery: function () {
		$('#biblioDiv').hide();
		$('#fotoDiv').show();
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
		img.orderBy = $('#orderBy option:selected').val();
	  $.getJSON(img.url,{'mode':				'getPage',
											 'orderBy':			img.orderBy,
											 'firstItem':		firstItem,
											 'tab':					$('#tab').val(),
											}, function(data){
			img.firstItem = parseInt(data.firstItem);
			img.lastItem = parseInt(data.lastItem);
			img.perPage = parseInt(data.perPage);
			img.columns = parseInt(data.columns);
			img.ttlNmbr = parseInt(data.nmbr);
			img.cells = data.tbl;

			img.showFotos();
			$(window).on('resize',null,img.showFotos);
		});
	},
	showFotos: function () {
			$('.countBox').html((img.firstItem+1)+' - '+img.lastItem+' <?php echo T("of");?> '+img.ttlNmbr).show();

			var $table = $('#fotos'),
					tab = '<?php echo $tab;?>',
					html = '',
					cntr = 0;

			if (img.columns == 0) {
				/* provide for flexible nmbr of columns based on screen width */
				var bodyWidth = $('body').width();
				var asideWidth = $('aside').outerWidth();
				var displayWidth = bodyWidth - asideWidth;
				var fotoWidth = parseInt($('#img-dummy').outerWidth())*1.5;
				var perLine = Math.round((displayWidth/fotoWidth),0);
//console.log('width: body='+bodyWidth+'; aside='+asideWidth+'; display='+displayWidth+'; foto='+fotoWidth+'; perLine='+perLine);
			} else {
				/* columns will be per Admin|Settings entry */
				var perLine = img.columns;
			}

			$table.html('<tr>');
			for (var entry in img.cells) {
				var cell = img.cells[entry];
				if (cntr == perLine) {
					$table.append('</tr>'+"\n"+'<tr>');
					cntr = 0;
				}
				var bibid = cell['bibid'];
				html  = '<td valign="bottom" align="center">'+"\n";
				html += '	<div class="galleryBox">'+"\n";
				html += '		<div><img id="img-'+bibid+'" src="../photos/'+cell['url']+'" class="biblioImage" /></div>'+"\n";
				html += '		<div class="smallType">'+"\n";
				html += '			<a href="#" id="a-'+bibid+'" >'+cell[img.orderBy]+'</a>'+"\n";
				html += '		</div>'+"\n";
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
					$('#fotoDiv').hide();
				});
			}
			$('#gallery').show();

			// enable or disable next / prev buttons
			if(img.firstItem >= img.perPage){
				img.prevPageItem = img.firstItem - img.perPage;
				$('.prevBtn').enable();
			} else {
				$('.prevBtn').disable();
			}
			if(((img.perPage+img.firstItem) <= img.lastItem) && (img.ttlNmbr > img.lastItem)){
				img.nextPageItem = img.perPage + img.firstItem;
				$('.nextBtn').enable();
			} else {
				$('.nextBtn').disable();
			}
	},

}
$(document).ready(img.init);

/*
INSERT INTO `openbibliowork`.`settings` (
`name`,`position`,`title`,`type`,`width`,`type_data`,`validator`,`value`,`menu`)
VALUES ('item_columns','8','Photo Columns,'int',NULL,NULL,NULL,'7','admin');
*/

</script>
