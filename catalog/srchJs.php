<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<script language="JavaScript" defer>
// JavaScript Document
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
		$('#ph_searchText').on('keyup',null,bs.checkPhraseSrchBtn);

		// for the search results section
		$('#addNewBtn').on('click',null,bs.makeNewCopy);
		$('#addList2CartBtn').on('click',null,bs.doAddListToCart);
		$('#addItem2CartBtn').on('click',null,bs.doAddItemToCart);
		$('#biblioListDiv .gobkBtn').on('click',null,bs.rtnToSrch);
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
			bs.doItemEdit(bs.theBiblio);
		});
		$('#biblioDeleteBtn').on('click',null,function () {
			bs.doItemDelete(bs.theBiblio);
		});
		$('#marcBtn').on('click',null,function () {
		  //console.log('swapping state to MARC column');
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
		$('#biblioDiv .gobkBtn').on('click',null,function () {
		  if (bs.multiMode) {
				if (bs.srchType = 'phrase')
					bs.doPhraseSearch();
				else
					bs.doBarCdSearch();
				bs.rtnToList();
			}
			else {
			  bs.rtnToSrch();
			}
		});

		// for the item edit and online update functions
	  $('#onlnUpdtBtn').on('click',null,function (){
			$('#onlnDoneBtn').show();
			$('#onlnUpdtBtn').hide();
			$('#itemEditorDiv td.filterable').show();
			bs.fetchOnlnData();
		});
	  $('#onlnDoneBtn').on('click',null,function (){
			$('#itemEditorDiv td.filterable').hide();
			$('#onlnUpdtBtn').show();
			$('#onlnDoneBtn').hide();
		});
		$('#itemSubmitBtn').val('<?php echo T("Update"); ?>')
											 .on('click',null,bs.doItemUpdate);
		$('.itemGobkBtn').on('click',null,function () {
   		$('#itemEditorDiv').hide();
		 	$('#biblioDiv').show();
		});
			
		// for the new copy function
		// to handle startup condition
		if ($('#autobarco:checked').length > 0) {
			$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').on('change',null,function (){
		  if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
				bs.doGetBarcdNmbr();
				$('#copySubmitBtn').enable().css('color', bs.srchBtnClr);
			}
			else {
				$('#barcode_nmbr').enable();
			}
		});

		// for the copy editor screen
		$('#barcode_nmbr').on('change',null,bs.chkBarcdForDupe);
		$('#copySubmitBtn').val('<?php echo T("Update"); ?>');
		$('#copySubmitBtn').on('click',null,bs.doCopyUpdate);
		$('#copyCancelBtn').val('<?php echo T("Go Back"); ?>');
		$('#copyCancelBtn').on('click',null,bs.rtnToBiblio);
		
		// for the photo editor screen.availHeight
		$('.gobkFotoBtn').on('click',null,bs.rtnToBiblio);
		$('#updtFotoBtn').on('click',null,bs.doUpdatePhoto);
		$('#deltFotoBtn').on('click',null,bs.doDeletePhoto);
		$('#addFotoBtn').on('click',null,bs.doAddNewPhoto);

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
	},
	//------------------------------
	initWidgets: function () {
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
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(bs.url,{mode:'getOpts'}, function(jsonData){
	    bs.opts = jsonData
			idis.init(bs.opts); // used for biblio item & copy displays
		});
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
			$('#copy_site').html(html);
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
					idis.fetchCopyInfo();
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
			} else {
				bs.biblio = $.parseJSON(jsonInpt);
				if (bs.biblio.data == null) {
				  var msgTxt =
	  			$('#rsltMsg').html('<?php echo T("Nothing Found") ?>').show();
	  			bs.rtnToSrch();
				}
				else {
					bs.multiMode = false;
					idis.showOneBiblio(bs.biblio)
					isis.fetchCopyInfo();
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
      bs.doBibidSearch(searchText);
			return;
		}

    //Moved this forward to show a please wait text, as search can take up to a second on a large databse and user might click twice.
	  $('#srchRslts').html('<p class="error"><img src="../images/please_wait.gif" width="26" /><?php echo T("Searching"); ?></p>');

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
				// no hits
				if ((biblioList.length == 0) || ($.trim(jsonInpt) == '[]') ) {
				  bs.multiMode = false;
	  			$('#srchRslts').html('<p class="error"><?php echo T("Nothing Found") ?></p>');
				  $('#biblioListDiv .goNextBtn').disable();
				  $('#biblioListDiv .goPrevBtn').disable();
				}

				// single hit
				// Changed to two, as an extra record is added with the amount of records etc. (also, if not first page ignore this) - LJ
				else if (biblioList.length == 2 && firstItem == 0) {
				  bs.multiMode = false;
      		// Changed from 0 to 1 as the first row shows record info
					bs.biblio = $.parseJSON(biblioList[1]);
					idis.showOneBiblio(bs.biblio)
					idis.fetchCopyInfo();
				}
				// multiple hits
				else {
				  bs.multiMode = true;
				  bs.showList(firstItem, biblioList);
				}
//				bs.rtnToList();
	    }

		});
		return false;
	},

	/* ====================================== */
	showList: function (firstItem, biblioList) {
	  if(firstItem==null){
	    firstItem=0;
	  }
	  
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
			var title = '', booktitle='', booksubtitle='', reporttitle='', reportsubtitle='',
					author='', coauthor='', editors='', corporate='',
					year='', journal='', jrnlDate='',
					callNo = '', edition = '', pubDate = ''; 
			var html = '';
			var biblio = JSON.parse(biblioList[nBiblio]);

			bs.biblio[biblio.bibid] = biblio;
			html += '<tr class="listItem">\n';

			//--// the leftside pretty stuff
			html += '	<td id="itemVisual">\n';
			html += '		<div> \n';
				//// if wanted, we create space for a possible photo, and fill it if one is found //
			if (bs.opts.showBiblioPhotos == 'Y') {
				html += '		<div id="photo_'+biblio.bibid+'" class="photos" >\n'+
								'			<img src="../images/shim.gif" class="biblioImage noHover" height="50px" width="50px" />\n'+
								'		</div>'+"\n";
	  		$.getJSON(bs.url,{ 'mode':'getPhoto', 'bibid':biblio.bibid  }, function(data){
	  			//--// when this returns, it will over-write the above shim, if there is anything found //
	  			if (data != null) {
						var theId = data[0].bibid, 
								fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+data[0].url;
						//console.log(theId+'==>>'+fotoFile);
						$('#photo_'+theId).html($('<img src="'+fotoFile+'" class="biblioImage hover">'));
					}
	  		});
			}
			//--// some administrative info and a 'more detail' button
			html += '	<div id="dashBd">\n';
			html += '		<img src="../images/'+biblio.avIcon+'" class="flgDot" title="Grn: available<br />Blu: on hold<br />Red: not available" />\n';
			html += '		<img src="../images/'+biblio.imageFile+'" width="32" height="32" />'+'\n';
			html += '		<br />\n';
			html += '		<input type="hidden" value="'+biblio.bibid+'" />'+'\n';
			html += '		<input type="button" class="moreBtn" value="<?php echo T("More info"); ?>" />'+'\n';
			html += '	</div>\n';
			html += '</div></td>';

			//--// the more useful stuff, biblio data
			if (biblio.data) {
				//// Construct a set of tags to define content of displayable lines.
				//// Order of lines is determined by 'position' column of material_fields table.
				//// Actual number of lines displayed will be seperately determined later
				var lineTag = [];
				var lineSpec = bs.displayInfo[biblio.matlCd];
				for (var i=0; i<lineSpec.length; i++) {
					if (!lineSpec[i]) continue; // skip null, undefined or non-existent elements
					var n = parseInt(lineSpec[i]['row'])+1;
					lineTag[n] = lineSpec[i]['tag']+lineSpec[i]['suf'];
				}

				//// Construct all potential lines for later use.
				var lines = [], lineNo;
				$.each(biblio.data, function (fldIndex, fldData) {
					var tmp = JSON.parse(fldData);
					if (!tmp.value) tmp.value = 'n/a';
					lineNo = lineTag.indexOf(tmp.marcTag);
					lines[lineNo] = tmp.value.trim();
				});

			} else {
				// skip these
				title = 'unknown'; callNo = 'not assigned';
				continue;
			}

			//--// Display first 'N' lines of biblio information
			// number of rows to display is based on Media type
			var N = bs.mediaLineCnt[biblio.matlCd];
			html += '<td id="itemInfo">\n';
			for (var i=1; i<=N; i++) {
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
		$('#updtFotoBtn').hide();  //see doUpdatePhoto() below
		$('#fotoHdr').val('<?php echo T("EditingExistingFoto"); ?>')
		$('#deltFotoBtn').show();
		$('#addFotoBtn').hide();
		$('#fotoMode').val('updatePhoto')
		$('#fotoSrce').attr({'required':false, 'aria-required':false});
		bs.showPhotoForm();
	},
	doPhotoAdd: function () {
		$('#updtFotoBtn').hide(); //see doUpdatePhoto() below
		$('#fotoHdr').val('<?php echo T("AddingNewFoto"); ?>')
		$('#deltFotoBtn').hide();
		$('#addFotoBtn').show();
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
					$('#fotoBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" >');
	  	$('#fotoFile').val('');
	  	$('#fotoCapt').val('');
	  	$('#fotoImgUrl').val('');
	  } else {
			$('#fotoEdLegend').html('<?php echo T("CoverPhotoFor");?>: '+idis.crntTitle);
	  	$('#fotoFile').val(idis.crntFoto.url);
					var fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+idis.crntFoto.url;
					$('#fotoBlkB').html($('<img src="'+fotoFile+'" id="biblioFoto" class="hover" >'));
	  	//$('#fotoBlkB').html('<img src="<?php echo OBIB_UPLOAD_DIR; ?>'+bs.crntFoto.url+'" id="foto" class="hover" >');
	  	$('#fotoCapt').val(idis.crntFoto.caption);
	  	$('#fotoImgUrl').val(fotoFile);
		}
		$('#photoEditorDiv').show();
	},
	doUpdatePhoto: function () {
	  /// left as an exercise for the motivated - FL (I'm burned out on this project)
	},
	doAddNewPhoto: function (e) {
		e.stopPropagation();
		$.ajaxFileUpload({
				url:							bs.url,
				secureuri:				false,
				fileElementId:		'fotoSrce',
				dataType: 				'json',
				data:							{'mode':'addNewPhoto',
													 'bibid':$('#fotoBibid').val(),
													 'url':$('#fotoFile').val(), 
													 'caption':$('#fotoCapt').val(),
													 'type':$('#fotoType').val(),
													 'position':$('#fotoPos').val(),
													},
				success: 					function (data, status) {
														//console.log('success');
														if(typeof(data.error) != 'undefined') {
															if(data.error != '') {
																$('#fotoMsg').html(data.error);
															} else {
																$('#fotoMsg').html(status);
															}
														}
													},
				error: 						function (data, status, e) {
														$('#fotoMsg').html('status');
														console.log(data);
													},
			});
		return false;
	},
	doDeletePhoto: function (e) {
		if (confirm("<?php echo T("Are you sure you want to delete this cover photo"); ?>")) {
	  	$.post(bs.url,{'mode':'deletePhoto',
										 'bibid':$('#fotoBibid').val(),
										 'url':$('#fotoFile').val(),
										 }
										 ,function(response){
				if(response) {
					$('#fotoMsg').html('<?php T("Delete completed"); ?>');
				}
			});
		}
		e.stopPropagation();
		return false;
	},
	
	/* ====================================== */
	doItemEdit: function (biblio) {
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
			ie.init(); // ensure field bindings are current

    	$('#itemEditorDiv').show();
		});
	},

	/* ====================================== */
	fetchOnlnData: function () {
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

	  if (isbn) {
	  	var msgText = '<?php T("Searching for ISBN"); ?>'+' '+isbn;
	  	params = "&mode=search&srchBy=7&lookupVal="+isbn;
	  	var item = isbn;
		} else if (issn) {
	  	var msgText = '<?php T("Searching for ISSN"); ?>'+' '+issn;
	  	params = "&mode=search&srchBy=7&lookupVal="+issn;
	  	var item = issn;
		} else if (title && author) {
	  	var msgText = "Searching for<br />Title: '"+title+"',<br />by "+author;
	  	params = "&mode=search&srchBy=4&lookupVal="+title+"&srchBy2=1004&lookupVal2="+author;
	  	var item = '"'+title+'", by '+author;
		}
	  msgText += '.<br />' + '<?php echo T("this may take a moment.");?>'
		$('#onlineMsg').html(msgText);
		
	  $.post(bs.urlLookup,params,function(response){
			//console.log('params==>'+params)
			var rslts = JSON.parse(response);
			var numHits = parseInt(rslts.ttlHits);
			var maxHits = parseInt(rslts.maxHits);
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
	doFldUpdt: function (e) {
		var rowNmbr = ((e.target.id).split('_'))[1];
		var srcId = '#marcBody input[name="onln_'+rowNmbr+'[data]"]';
		var text = $(srcId).val();
		//console.log('you clicked btn #'+rowNmbr+' containing "'+text+'" from '+srcId );
		var destId = '#marcBody input[name="fields['+rowNmbr+'][data]"]';
		$(destId).val(text);
	},
	doItemUpdate: function () {
	  // verify all required fields are present
	  //if (!ie.validate()) return false;
	  
		var params = "&mode=updateBiblio&bibid="+bs.biblio.bibid +
						 '&'+ $('#biblioEditForm').not('.online').serialize();
	  $.post(bs.url,params, function(response){
	    if (response == '!!success!!'){
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
	doItemDelete: function (biblio) {
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
	makeNewCopy: function () {
		$('#biblioDiv').hide();
		if ($('#autobarco:checked').length > 0) {
			bs.doGetBarcdNmbr();
		}
  	var crntsite = bs.opts.current_site
		$('#copy_site').val(crntsite);
		
		$('#copyEditorDiv').show();

		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').on('click',null,function () {
			idis.doCopyNew();
			//bs.rtnToBiblio();
			return false;
		});
	  // prevent submit button from firing a 'submit' action
		return false;
	},
	doGetBarcdNmbr: function () {
		$.getJSON(bs.url,{'mode':'getNewBarcd'}, function(jsonInpt){
		  $('#copyTbl #barcode_nmbr').val(jsonInpt.barcdNmbr);		  
		});	
	},
	chkBarcdForDupe: function () {
		var barcd = $.trim($('#barcode_nmbr').val());
		barcd = flos.pad(barcd,bs.opts.barcdWidth,'0');
		$('#barcode_nmbr').val(barcd);
		// Set copyId to null if not defined (in case of new item)
		var currCopyId = null;
		if(typeof(bs.crntCopy) != "undefined"){
			currCopyId = bs.crntCopy.copyid;
		}
		
	  $.get(bs.url,{'mode':'chkBarcdForDupe','barcode_nmbr':barcd,'copyid':currCopyId}, function (response) {
	  	if(response.length > 0){
			$('#copySubmitBtn').disable().css('color', '#888888');
			$('#editRsltMsg').html(response).show();
		} else {
			$('#copySubmitBtn').enable().css('color', bs.srchBtnClr);
			$('#editRsltMsg').html(response).show();
		}
		})
	},
};
$(document).ready(bs.init);

</script>
