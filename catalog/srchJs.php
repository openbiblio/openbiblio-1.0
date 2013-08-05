<script language="JavaScript" defer >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * JavaScript portion of the Biblio ExistingItem Manager
 * @author Luuk Jansen
 * @author Fred LaPlante
 */
"use strict";
<?php
	// If a circulation user and NOT a cataloging user the system should treat the user as opac
//	if(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))
	if(strtolower($tab) == 'opac' || strtolower($tab) == 'circulation' )
	  echo "var opacMode = true;";
	else
	  echo "var opacMode = false;";
?>
//------------------------------------------------------------------------------

var bs = {
	<?php
		echo "showMarc: '".T("Show Marc Tags")."',\n";
		echo "hideMarc: '".T("Hide Marc Tags")."',\n";
	?> 
	multiMode: false,
	
	init: function () {
		// get header stuff going first
		bs.initWidgets();
		bs.url = 'catalogServer.php';
		bs.listSrvr = '../shared/listSrvr.php';
		bs.urlLookup = '../catalog/onlineServer.php'; //may not exist
		bs.opts = [];

		// for search criteria form
		$('#advancedSrch').hide();
		$('#advanceQ:checked').val(['N'])
		$('#advanceQ').on('click',null,function(){
			if ($('#advanceQ:checked').val() == 'Y')
				$('#advancedSrch').show();
			else
				$('#advancedSrch').hide();
		});
		$('#barcdSrchBtn').on('click',null,bs.doBarcdSearch);
		$('#phraseSrchBtn').on('click',null,bs.doPhraseSearch);
		bs.srchBtnClr = $('#phraseSrchBtn').css('color');
		$('#bc_searchBarcd').on('keyup',null,bs.checkBarcdSrchBtn);
		$('#bc_searchBarcd').on('change',null,bs.formatBarcode);
		$('#ph_searchText').on('keyup',null,bs.checkPhraseSrchBtn);

		// for the search results section
		$('#addNewBtn').on('click',null,bs.doNewCopy);
		$('#addList2CartBtn').on('click',null,bs.doAddListToCart);
		$('#addItem2CartBtn').on('click',null,bs.doAddItemToCart);
		$('.listGobkBtn').on('click',null,bs.rtnToSrch);
		$('#biblioListDiv .goPrevBtn').on('click',null,function () {bs.goPrevPage(bs.previousPageItem);});
		$('#biblioListDiv .goNextBtn').on('click',null,function () {bs.goNextPage(bs.nextPageItem);});
		$('#biblioListDiv .goNextBtn').disable();
		$('#biblioListDiv .goPrevBtn').disable();

		// for the single biblio display screen
		$('#photoAddBtn').on('click',null,function () {
			bs.doPhotoAdd(bs.theBiblio);
		});
		$('#photoEditBtn').on('click',null,function () {
			bs.doPhotoEdit(bs.theBiblio);
		});
		$('#biblioEditBtn').on('click',null,function () {
//			bs.doItemEdit(bs.theBiblio);
			ie.doItemEdit(bs.theBiblio);
		});
		$('#biblioDeleteBtn').on('click',null,function () {
			bs.doItemDelete(bs.theBiblio);
		});
		$('#marcBtn').on('click',null,function () {
		  var marcFld$ = $('#biblioDiv td.filterable');
		  if (marcFld$.is(':hidden')) {
				$('#biblioDiv td.filterable').show();
				$('#marcBtn').val(bs.hideMarc);
			}
			else {
				$('#biblioDiv td.filterable').hide();
				$('#marcBtn').val(bs.showMarc);
			}
		});
		$('.bibGobkBtn').on('click',null,function () {
		  if (bs.multiMode) {
				bs.rtnToList();
			} else {
			  bs.rtnToSrch();
			}
		});

		// for the copy editor screen
		$('#copyCancelBtn').on('click',null,function () {
			idis.fetchCopyInfo(); // refresh copy display
			bs.rtnToBiblio();
		});

		// for the item edit and online update functions
		$('.itemGobkBtn').on('click',null,function () {
   		$('#itemEditorDiv').hide();
		 	$('#biblioDiv').show();
		});
			
		bs.resetForms();
		bs.fetchOpts(); // also inits itemDisplayJs
		bs.fetchCrntMbrInfo();
		// prepare pull-down lists
		bs.fetchMaterialList();
		bs.fetchCollectionList();
		bs.fetchSiteList();
		// needed for search results presentation
		bs.fetchMediaDisplayInfo();
		bs.fetchMediaLineCnt();
		bs.fetchMediaIconUrls();
	},
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
	  //console.log('resetting Search Form');
	  $('#crntMbrDiv').hide();
	  $('#searchDiv').show();
		$('p.error').hide();
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').hide();
	  $('#itemEditorDiv').hide();
	  $('#copyEditorDiv').hide();
	  $('#photoEditorDiv').hide();
	  bs.multiMode = false;
	  bs.checkPhraseSrchBtn();
	  bs.checkBarcdSrchBtn();
		$('#marcBtn').val(bs.showMarc);
		if (opacMode) $('#barcodeSearch').hide();
		$('#ph_searchText').focus();
	},
	rtnToSrch: function () {
  	$('tbody#biblio').html('');
  	$('tbody#copies').html('');
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').hide();
	  $('#searchDiv').show();
	  $('#itemEditorDiv').hide();
	  $('#copyEditorDiv').hide();
	  $('#photoEditorDiv').hide();
	  bs.checkPhraseSrchBtn();
	  bs.checkBarcdSrchBtn();
	},
	rtnToList: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').show();
	  $('#searchDiv').hide();
	  $('#itemEditorDiv').hide();
	  $('#copyEditorDiv').hide();
	  $('#photoEditorDiv').hide();
	},
	rtnToBiblio: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').show();
	  $('#biblioListDiv').hide();
	  $('#searchDiv').hide();
	  $('#itemEditorDiv').hide();
	  $('#copyEditorDiv').hide();
	  $('#photoEditorDiv').hide();
	},

	checkPhraseSrchBtn: function () {
		if (($('#ph_searchText').val()).length > 0) { // empty input
			$('#phraseSrchBtn').enable().css('color', bs.srchBtnClr);
		} else {
			$('#phraseSrchBtn').disable().css('color', '#888888');
		}
	},
	checkBarcdSrchBtn: function () {
		if (($('#bc_searchBarcd').val()).length > 0) { // empty input
			$('#barcdSrchBtn').enable().css('color', bs.srchBtnClr);
		} else {
			$('#barcdSrchBtn').disable().css('color', '#888888');
		}
	},
	formatBarcode: function () {
		var barcd = $.trim($('#bc_searchBarcd').val());
		barcd = flos.pad(barcd,bs.opts.barcdWidth,'0');
		$('#bc_searchBarcd').val(barcd); // redisplay expanded value
	},

	doAltStart: function () {
		// alternate startup in response to remote package
		//console.log('checking for alternative starts using: '+<?php echo "'".$_REQUEST[barcd]."'"; ?>);
		<?php
		if ($_REQUEST['barcd']) {
			echo "$('#bc_searchBarcd').val('$_REQUEST[barcd]');\n";
			echo "bs.doBarcdSearch();\n";
		}
		else if ($_REQUEST['bibid']) {
			echo "bs.doBibidSearch($_REQUEST[bibid]);\n";
		}
		else if ($_REQUEST['searchText']) {
			echo "$('#ph_searchText').val('$_REQUEST[searchText]');\n";
			echo "$('#ph_searchType').val('$_REQUEST[searchType]');\n";
			echo "bs.doPhraseSearch();\n";
		}
		?>
	},

	showMsg: function (msg) {
		$('#errSpace').html(msg);
		$('.error').show();
	},

	//------------------------------
	fetchOpts: function () {
		bs.opts['showBiblioPhotos'] = '<?php echo Settings::get('show_item_photos');?>';
		bs.opts['barcdWidth'] = <?php echo Settings::get('item_barcode_width');?>;
    bs.opts['current_site'] = <?php echo Settings::get('library_name');?>;
		idis.init(bs.opts); // used for biblio item & copy displays
		ie.init(bs.opts); // ensure field bindings are current
	},
	fetchCrntMbrInfo: function () {
	  $.get(bs.url,{mode:'getCrntMbrInfo'}, function(data){
			$('#crntMbrDiv').empty().html(data).show();
		});
	},
	fetchMaterialList: function () {
	  $.getJSON(bs.listSrvr,{mode:'getMediaList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#itemMediaTypes').html(html);
			html = '<option value="all"  selected="selected">All</option>' + html;
			$('#srchMediaTypes').html(html);
		});
	},
	fetchCollectionList: function () {
	  $.getJSON(bs.listSrvr,{mode:'getCollectionList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#itemEditColls').html(html);
			html = '<option value="all"  selected="selected">All</option>' + html;
			$('#srchCollections').html(html);
		});
	},
	fetchSiteList: function () {
	  $.getJSON(bs.listSrvr,{mode:'getSiteList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#copySite').html(html);
			html = '<option value="all"  selected="selected">All</option>' + html;
			$('#srchSites').html(html);

			// now ready to begin a search
			bs.doAltStart();
		});
	},
	fetchMediaDisplayInfo: function () {
	  $.getJSON(bs.url,{mode:'getMediaDisplayInfo',howMany:'all'}, function(response){
			bs.displayInfo = response;
		});
	},
	fetchMediaIconUrls: function () {
	  $.getJSON(bs.listSrvr,{mode:'getMediaIconUrls'}, function(response){
			bs.mediaIconUrls = response;
		});
	},
	fetchMediaLineCnt: function () {
	  $.getJSON(bs.url,{mode:'getMediaLineCnt'}, function(response){
			bs.mediaLineCnt = response;
		});
	},

	/* ====================================== */
	doBibidSearch: function (bibid) {
	  bs.srchType = 'bibid';
	  $('p.error').html('').hide();
	  var params = '&mode=doBibidSearch&bibid='+bibid;
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				bs.biblio = $.parseJSON(jsonInpt);
				if (!bs.biblio.data) {
	  			$('#rsltMsg').html('<?php echo T("NothingFoundByBarcdSearch") ?>').show();
				}
				else {
					idis.showOneBiblio(bs.biblio)
					//idis.fetchCopyInfo();
				}
	    }
		  $('#searchDiv').hide();
	    $('#biblioDiv').show();
		});
		return false;
	},
	doBarcdSearch: function (e) {
		var barcd = $.trim($('#searchBarcd').val());
		barcd = flos.pad(barcd,bs.opts.barcdWidth,'0');
		$('#searchBarcd').val(barcd); // redisplay expanded value
		
	  bs.srchType = 'barCd';
	  $('p.error').html('').hide();
	  var params = $('#barcodeSearch').serialize();
		params += '&mode=doBarcdSearch';
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
				return false;
			} else {
				bs.biblio = $.parseJSON(jsonInpt);
				if (bs.biblio.hdr != null) {
					bs.multiMode = false;
					idis.showOneBiblio(bs.biblio)
					//idis.fetchCopyInfo();
				}
				else if (bs.biblio.hdr == null) {
				  var msgTxt =
	  			$('#rsltMsg').html('<?php echo T("Nothing Found") ?>').show();
	  			bs.rtnToSrch();
				}
				else {
					bs.multiMode = false;
					idis.showOneBiblio(bs.biblio)
				}
	    }
		  $('#searchDiv').hide();
	    $('#biblioDiv').show();
		});
		return false;
	},
	doPhraseSearch: function (e,firstItem) {
	  $('#biblioListDiv').show()
	  $('#searchDiv').hide();
	  $('#resultsArea').html('');
	  $('#errSpace').html('');

		// searchType 'ID' gets special handling
		var searchType = $('#ph_searchType option:selected').val();
		var searchText = $('#ph_searchText').val();
		//console.log('searchType==>'+searchType+'; searchText==>'+searchText);
		if (searchType == 'id') {
			e.preventDefault();
      bs.doBibidSearch(searchText);
			return false;
		}

    /* Moved this forward to show a please wait text, as search can take */
		/*up to a second on a large databse and user might click twice.      */
		var msg = '<p class="error">'
							'	<img src="../images/please_wait.gif" width="26" />'
              '	<?php echo T("Searching"); ?>'
							'</p>'+"\n";
	  $('#srchRslts').html(msg);

	  $('.rsltQuan').html('');
	  if(firstItem==null) firstItem=0;
	  bs.srchType = 'phrase';		
	  var params = $('#phraseSearch').serialize();
		params += '&mode=doPhraseSearch&firstItem='+firstItem;

	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				var biblioList = $.parseJSON(jsonInpt);

				if ((biblioList.length == 0) || ($.trim(jsonInpt) == '[]') ) {
					//console.log('no hits');
				  bs.multiMode = false;
	  			$('#srchRslts').html('<p class="error"><?php echo T("Nothing Found") ?></p>');
				  $('#biblioListDiv .goNextBtn').disable();
				  $('#biblioListDiv .goPrevBtn').disable();
				}
				else if (biblioList.length == 2 && firstItem == 0) {
					//console.log('single hit');
					// Changed to two, as an extra record is added with the amount of records etc.
					// (also, if not first page ignore this) - LJ
				  bs.multiMode = false;
      		// Changed from 0 to 1 as the first row shows record info
					bs.biblio = $.parseJSON(biblioList[1]);
					idis.showOneBiblio(bs.biblio)
					//idis.fetchCopyInfo();
				}
				else {
					console.log('multiple hits');
				  bs.multiMode = true;
				  bs.showList(firstItem, biblioList);
				}
	    }
		});
		return false;
	},

	/* ====================================== */
	getPhoto: function (bibid, dest) {
		if (bibid === undefined) console.log('Missing bibid in getPhoto()');
		$.getJSON(bs.url,{ 'mode':'getPhoto', 'bibid':bibid  }, function(data){
			if (data != null) {
				var fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+data[0].url;
				$(dest).html($('<img src="'+fotoFile+'" class="biblioImage hover">'));
			}
		});
	},

	showList: function (firstItem, biblioList) {
	  if(firstItem == null) firstItem=0;
	  
		// Modified in order to limit results per page. First "record" contains this data - LJ
		var queryInfo = $.parseJSON(biblioList[0]);
		var firstItem = parseInt(queryInfo.firstItem),
				lastItem = parseInt(queryInfo.lastItem),
				perPage = parseInt(queryInfo.itemsPage),
				ttlNum = parseInt(queryInfo.totalNum),
				modFirstItem = parseInt(queryInfo.firstItem) + 1;
		$('.rsltQuan').html(' '+ttlNum+' <?php echo T("Items"); ?>('+modFirstItem+'-'+lastItem+ ') ');
		bs.biblio = Array();

		$('#listTbl tbody#srchRslts').html('');
		for (var nBiblio in biblioList) {
			if (nBiblio == 0) continue;
			var biblio = JSON.parse(biblioList[nBiblio]);

			var title = '', booktitle='', booksubtitle='', reporttitle='', reportsubtitle='',
					author='', coauthor='', editors='', corporate='',
					year='', journal='', jrnlDate='',
					callNo='', edition ='', pubDate='', nrCopies=0;
			var html = '';
			var hdr = biblio.hdr;
			var cpys = biblio.cpys;
			bs.biblio[hdr.bibid] = biblio;
			var imageFile = bs.mediaIconUrls[hdr.material_cd];
			html += '<tr class="listItem">\n';

			//--// the leftside pretty stuff
			html += '	<td>\n';
			html += '		<div class="itemVisual"> \n';
			/* if wanted, we create space for a possible photo, and fill it if one is found */
			var showFoto = '<?php echo Settings::get('show_item_photos'); ?>';
			if ((showFoto == 'Y') && (hdr.bibid !== undefined)){
				html += '		<div class="photos"  id="photo_'+hdr.bibid+'">\n';
				html += '			<img src="../images/shim.gif" class="biblioImage noHover" height="50px" width="50px" '
												   + 'height="'+bs.fotoHeight+'" width="'+bs.fotoWidth+'" >';
				html += '		</div>'+"\n";
				bs.getPhoto(hdr.bibid, '#photo_'+hdr.bibid );
			}
			/*  some administrative info and a 'more detail' button */
			html += '	<div class="dashBds">\n';
			html += ' 	<div class="dashBdsA">';
			html += '			<p>copies:'+hdr.ncpys+'</p>';
			html += '		</div>\n';
			html += ' 	<div class="dashBdsB">';
			html += '			<img src="../images/'+hdr.avIcon+'" class="flgDot" title="Grn: available<br />Blu: on hold<br />Red: not available" />\n';
			html += '			<img src="../images/'+imageFile+'" width="32" height="32" />'+'\n';
			html += '		</div>\n';
			html += ' 	<div class="dashBdsC">';
			html += '			<input type="hidden" value="'+hdr.bibid+'" />'+'\n';
			html += '			<input type="button" class="moreBtn" value="<?php echo T("More info"); ?>" />'+'\n';
			html += ' 	</div>';
			html += '	</div>\n';
			html += '</div></td>';  // end of itemVisual div

			/* the more useful stuff, biblio data */
			var marc = biblio.marc;
			if (marc) {
				//// Construct all potential lines for later use.
				var lines = [],
						lineNo;
				$.each(marc, function (ndx, fld) {
					if (!fld.value) fld.value = 'n/a';
					lineNo = fld.line;
					lines[lineNo] = fld.value.trim();
				});
			} else {
				// skip these
				title = 'unknown'; callNo = 'not assigned';
				continue;
			}

			//--// Display first 'N' lines of biblio information
			// number of rows to display is based on Media type
			var N = bs.mediaLineCnt[hdr.material_cd];
			html += '<td id="itemInfo">\n';
			for (var i=0; i<N; i++) {
				if (!lines[i]) continue; // skip null, undefined or non-existent elements
				if (lines[i] != '') html += '<p class="searchListItem">'+lines[i]+'</p>\n';
			}
			html += '</tr>\n';
			$('#srchRslts').append(html);
		}
		obib.reStripe2('listTbl','odd');

	  // this button is created dynamically, so duplicate binding is not possible
		$('.moreBtn').on('click',null,bs.getPhraseSrchDetails);
		
		// enable or disable next / prev buttons
		if(firstItem>=perPage){
			bs.previousPageItem = firstItem - perPage;
			$('#biblioListDiv .goPrevBtn').enable();
		} else {
			$('#biblioListDiv .goPrevBtn').disable();
		}
		if((perPage+firstItem <= lastItem)&&(ttlNum!=lastItem)){
			bs.nextPageItem = perPage + firstItem;
			$('#biblioListDiv .goNextBtn').enable();
		} else {
			$('#biblioListDiv .goNextBtn').disable();
		}
		
		$('#biblioListDiv').show()
		$('#biblioDiv').hide()
 		$('#searchDiv').hide();
	},
	goNextPage:function (firstItem) {
		$('#biblioListDiv .goNextBtn').disable();
		bs.doPhraseSearch(null,firstItem);
	},
	goPrevPage:function (firstItem) {
		$('#biblioListDiv .goPrevBtn').disable();
		bs.doPhraseSearch(null,firstItem);
	},
	doAddListToCart:function () {
    var params = "mode=addToCart&name=bibid&tab=catalog";
		for (var bibid in bs.biblio) {
	  	params += "&id[]="+bibid;
		}
	  $.post(bs.url,params, function(response){
	    $('#results_found').html(response);
	  });
	},
	getPhraseSrchDetails: function () {
	  var bibid = $(this).prev().val();
		bs.biblio.bibid = bibid;
		idis.showOneBiblio(bs.biblio[bibid]);
		//idis.fetchCopyInfo(bs.biblio[bibid]);
	},
	doAddItemToCart:function () {
    var params = "mode=addToCart&name=bibid&tab=catalog";
	  params += "&id[]="+bs.biblio.bibid;
	  $.post(bs.url,params, function(response){
	    $('#results_found').html(response);
	  });
	},
	
	/* ====================================== */
	
	makeDueDateStr: function (dtOut, daysDueBack) {
		if(daysDueBack==null) daysDueBack=0;
		var dt = dtOut.split(' ');
		var dat = dt[0]; var tm = dt[1];daysDueBack
		var datAray = dat.split('-');
		var theYr = datAray[0];
		var theMo = datAray[1]-1;
		var theDy = datAray[2];
		var dateOut = new Date(theYr,theMo,theDy);
		dateOut.setDate(dateOut.getDate() + daysDueBack);
		return dateOut.toDateString();
	},
	

	//------------------------------
	findMarcField: function (biblio, tag) {
	  for (var i=0; i< biblio.data.length; i++) {
			var tmp = eval('('+biblio.data[i]+')');
			if (tmp.marcTag == tag) {
				return tmp;
			}
		};
		return null;
	},
	findMarcFieldSet: function (biblio, tag) {
	  var fldSet = []; var n = 0;
	  for (var i=0; i< biblio.data.length; i++) {
			var tmp = eval('('+biblio.data[i]+')');
			if (tmp.marcTag == tag) {
				fldSet[n] = tmp;  n++;
			}
		}
		return fldSet;
	},

	/* ====================================== */
	doPhotoEdit: function () {
		$('#updtFotoBtn').hide(); 
		$('#fotoHdr').val('<?php echo T("EditingExistingFoto"); ?>')
		$('#deltFotoBtn').show();
		$('#addFotoBtn').hide();
		//$('#updtFotoBtn').show(); //not yet available
    $('#fotoMsg').hide();
		$('#fotoMode').val('updatePhoto')
		$('#fotoSrce').attr({'required':false, 'aria-required':false});
		bs.showPhotoForm();
	},
	doPhotoAdd: function () {
		$('#updtFotoBtn').hide();
		$('#fotoHdr').val('<?php echo T("AddingNewFoto"); ?>')
		$('#deltFotoBtn').hide();
		//$('#updtFotoBtn').hide(); // not yet available
		$('#addFotoBtn').show();
    $('#fotoMsg').hide();
		$('#fotoMode').val('addNewPhoto')
		$('#fotoSrce').attr({'required':true, 'aria-required':true});
		bs.showPhotoForm();
	},
	showPhotoForm: function () {
	  $('#biblioDiv').hide();
	  $('#fotoSrce').val('')
	  $('#fotoBibid').val(idis.crntBibid);

	  if (idis.crntFoto == null) {
			$('#fotoEdLegend').html('<?php echo T("EnterNewPhotoInfo"); ?>');
	  	$('#fotoName').val(idis.crntBibid+'.jpg');
			wc.eraseImage();
	  } else {
			$('#fotoEdLegend').html('<?php echo T("CoverPhotoFor");?>: '+idis.crntTitle);
	  	$('#fotoName').val('<?php echo OBIB_UPLOAD_DIR; ?>'+idis.crntFoto.url);
			wc.showImage($('#fotoName').val());
		}
		$('.gobkFotoBtn').on('click',null,bs.rtnToBiblio);
		$('#photoEditorDiv').show();
	},
	
	/* ====================================== */
	doItemEdit: function (biblio) {
ie.doItemEdit(biblio);
return;

		$('#onlnUpdtBtn').show();
		$('#onlnDoneBtn').hide();
	  $('#biblioDiv').hide();

	  bs.bibid = biblio.bibid;
	  bs.matlCd = biblio.matlCd;
	  bs.collCd = biblio.collCd;
	  bs.opacFlg = biblio.opacFlg;
	  $.get(bs.url,{'mode':'getBiblioFields',
									'bibid':bs.bibid,
									'matlCd':bs.matlCd,
									'collCd':bs.collCd},
									function (response) {
			$('#marcBody').html(response);
			$('#itemEditorDiv fieldset legend').html('<?php echo T("Edit Item Properties"); ?>');
			$('#itemEditorDiv td.filterable').hide();
			obib.reStripe2('biblioFldTbl','odd');

			// set non-MARC fields using data on hand
			$('#opacFlg').val(bs.opacFlg);
			$('#itemMediaTypes').val(bs.matlCd);
			$('#itemEditColls').val(bs.collCd);
			
			// fill MARC fields with data on hand
			// first non-repeating 'input' fields
			$('#marcBody input.only1:text').each(function (){
			  var tmp = bs.findMarcField(biblio, this.id);
			  if (tmp){
			  	$('#marcBody #'+tmp.marcTag).val(tmp.value);
			  	$('#marcBody #'+tmp.marcTag+'_fieldid').val(tmp.fieldid);
			  	$('#marcBody #'+tmp.marcTag+'_subfieldid').val(tmp.subfieldid);
			  }
			});
			// then any 'textarea' fields
			$('#marcBody textarea.only1').each(function() {
			  var tmp = bs.findMarcField(biblio, this.id);
			  if (tmp){
			  	$('#marcBody #'+tmp.marcTag).val(tmp.value);
			  	$('#marcBody #'+tmp.marcTag+'_fieldid').val(tmp.fieldid);
			  	$('#marcBody #'+tmp.marcTag+'_subfieldid').val(tmp.subfieldid);
				}
			});
			// then repeaters
			bs.lastFldTag = ''; 
			$('#marcBody input.rptd:text').not('.online').each(function (){
				var fldNamePrefix = (this.name.split(']'))[0]+']';
			  if (this.id != bs.lastFldTag) {
					bs.lastFldTag = this.id;
			  	bs.tmpList = bs.findMarcFieldSet(biblio, this.id);
			  	bs.fldNo = 0; bs.maxFldNo = bs.tmpList.length;
				}
				if (bs.fldNo < bs.maxFldNo) {
				  var tmp = bs.tmpList;
					var selector1 = 'input'+'[name="'+fldNamePrefix+'[data]"]';
			  	$(selector1).val(tmp[bs.fldNo].value);
			  	var selector2 = 'input'+'[name="'+fldNamePrefix+'[fieldid]"]';
			  	$(selector2).val(tmp[bs.fldNo].fieldid);
			  	var selector3 = 'input'+'[name="'+fldNamePrefix+'[subfieldid]"]';
			  	$(selector3).val(tmp[bs.fldNo].subfieldid);
			  	bs.fldNo++;
			  }
			});
//			ie.init(); // ensure field bindings are current

//    	$('#itemEditorDiv').show();
		});
	},

	/* ====================================== */
	xfetchOnlnData: function () {
		if ($('#245a').length > 0) var title =  $('#245a').val();
		//console.log('title==>'+title);
		if ($('#100a').length > 0) var author= $('#100a').val().split(',')[0];
		//console.log('author==>'+author);
		if ($('#020a').length > 0) {
		  var isbn  = $('#020a').val().split(',');
		  for (var i=0; i<isbn.length; i++) {
		    if (!((isbn[i].substr(0,3) == '978') && (isbn[i].length == 10))) {
		    	var ISBN = isbn[i];
		    	break;
				}
			}
			//console.log('isbn==>'+isbn);
		}
		if ($('#022a').length > 0) var issn  = ($('#022a').val()).split(',');
		//console.log('issn==>'+issn);

		var msgText = '',
				params = '',
				item = '';
	  if (isbn) {
	  	msgText = '<?php T("Searching for ISBN"); ?>'+' '+isbn,
	  	params = "&mode=search&srchBy=7&lookupVal="+isbn,
	  	item = isbn;
		} else if (issn) {
	  	msgText = '<?php T("Searching for ISSN"); ?>'+' '+issn;
	  	params = "&mode=search&srchBy=7&lookupVal="+issn;
	  	item = issn;
		} else if (title && author) {
	  	msgText = "Searching for<br />Title: '"+title+"',<br />by "+author;
	  	params = "&mode=search&srchBy=4&lookupVal="+title+"&srchBy2=1004&lookupVal2="+author;
	  	item = '"'+title+'", by '+author;
		} else {
			msgText = '<?php T("NotEnoughtData"); ?>';
			return;
		}

	  msgText += '.<br />' + '<?php echo T("this may take a moment.");?>'
		$('#onlineMsg').html(msgText);
		
	  $.post(bs.urlLookup,params,function(response){
			//console.log('params==>'+params)
			var rslts = JSON.parse(response),
					numHits = parseInt(rslts.ttlHits),
					maxHits = parseInt(rslts.maxHits);
			if (numHits < 1) {
				$('#onlineMsg').html(rslts.msg+' for '+item);
			}
			else if (numHits >= maxHits) {
			  msgText = '<?php echo T("hits found, too many to process"); ?>';
				$('#onlineMsg').html(numHits+' '+msgText);
			}
			else if (numHits > 1){
				$('#onlineMsg').html(numHits+'hits found, this version can only handle one.');
			}
			else if (rslts.ttlHits == 1){
			  var data;
				$('#onlineMsg').html('Success!!<br /><br />'+
														 'Click the arrow buton to enter online data,<br />'+
														 'then click "Update" at the bottom of the page.');
				bs.hostData = rslts.data;
				$.each(rslts.data, function(hostIndex,hostData) {
				  $.each(hostData, function(hitIndex,hitData) {
					  data = hitData;
				  }); // .each
				}); // .each
				for (var tag in data) {
					$('#marcBody input.online:text').filter('#'+tag).val(data[tag]);
				}

				// this button created dynamicly by server
				$('#marcBody input.accptBtn').on('click',null,bs.doFldUpdt);
			} // else
		}); // .post
	},

	/* ====================================== */
	xdoFldUpdt: function (e) {
		var rowNmbr = ((e.target.id).split('_'))[1];
		var srcId = '#marcBody input[name="onln_'+rowNmbr+'[data]"]';
		var text = $(srcId).val();
		//console.log('you clicked btn #'+rowNmbr+' containing "'+text+'" from '+srcId );
		var destId = '#marcBody input[name="fields['+rowNmbr+'][data]"]';
		$(destId).val(text);
	},
	xdoItemUpdate: function () {
	  // verify all required fields are present
	  //if (!ie.validate()) return false;
	  
		var params = "&mode=updateBiblio&bibid="+bs.biblio.bibid +
						 '&'+ $('#biblioEditForm').not('.online').serialize();
	  $.post(bs.url,params, function(response){
	    if (response == '!!success!!'){
				//bs.rtnToBiblio();
    		$('#itemEditorDiv').hide();
				// successful update, repeat search with existing criteria
				if (bs.srchType == 'barCd')
					bs.doBarCdSearch();
				else if (bs.srchType = 'phrase')
					bs.doPhraseSearch();
			} else {
			  // failure, show error msg, leave form in place
				$('#itemRsltMsg').html(response);
	 		}
	  });
	  return false;
	},
	xdoItemDelete: function (biblio) {
		//console.log('there are '+bs.copyJSON.length+' copies.');
		if (bs.copyJSON) {
			alert('You must delete all copies before you can delete an item!');
		}
		else {
	  	if (confirm('<?php echo T("Are you sure you want to delete this item?"); ?>')) {
	    	var params = "&mode=deleteBiblio&bibid="+bs.biblio.bibid;
	  		$.post(bs.url,params, function(response){
	  		  $('#rsltMsg').html(response);
					if (bs.srchType == 'barCd')
						bs.doBarCdSearch();
					else if (bs.srchType = 'phrase')
						bs.doPhraseSearch();
	  			$('#biblioDiv').hide();
	  		});
			}
		}
		return false;
	},

	/* ====================================== */
	doNewCopy: function (e) {
  	e.stopPropagation();
		$('#biblioDiv').hide();
		$('#copyBibid').val(bs.biblio.bibid);
  	var crntsite = bs.opts.current_site
		$('#copySite').val(crntsite);

		$('#copyEditorDiv').show();
		ced.doCopyNew(e);
		e.preventDefault();
	},

};
$(document).ready(bs.init);

</script>
