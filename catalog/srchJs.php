<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<script language="JavaScript" defer>
// JavaScript Document
<?php
	// If a circulation user and NOT a cataloging user the system should treat the user as opac
	if(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))
	  echo "var opacMode = true;";
	else
	  echo "var opacMode = false;";
?>
//------------------------------------------------------------------------------
// biblio_search Javascript
"use strict";

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
		bs.urlLookup = '../catalog/onlineServer.php'; //may not exist

		// search form
		$('#advancedSrch').hide();
		$('#advanceQ:checked').val(['N'])
		$('#advanceQ').on('click',null,function(){
			if ($('#advanceQ:checked').val() == 'Y')
				$('#advancedSrch').show();
			else
				$('#advancedSrch').hide();
		});
		$('#srchByBarcd').on('click',null,bs.doBarcdSearch);
		$('#srchByPhrase').on('click',null,bs.doPhraseSearch);
		bs.srchBtnClr = $('#srchByBarcd').css('color');
		$('#searchBarcd').on('keyup',null,bs.checkSrchByBarcdBtn);
		$('#searchText').on('keyup',null,bs.checkSrchByPhraseBtn);

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
		$('#itemSubmitBtn').val('<?php echo T('Update'); ?>')
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
		$('#copySubmitBtn').val('<?php echo T('Update'); ?>');
		$('#copySubmitBtn').on('click',null,bs.doCopyUpdate);
		$('#copyCancelBtn').val('<?php echo T('Go Back'); ?>');
		$('#copyCancelBtn').on('click',null,bs.rtnToBiblio);
		
		// for the photo editor screen.availHeight
		$('.gobkFotoBtn').on('click',null,bs.rtnToBiblio);
		$('#updtFotoBtn').on('click',null,bs.doUpdatePhoto);
		$('#deltFotoBtn').on('click',null,bs.doDeletePhoto);
		$('#addFotoBtn').on('click',null,bs.doAddNewPhoto);

		bs.resetForms();
		bs.fetchOpts();
		bs.fetchCrntMbrInfo();
		bs.fetchMaterialList();
		bs.fetchCollectionList();
		bs.fetchSiteList();
	},
	//------------------------------
	initWidgets: function () {
	},
	checkSrchByPhraseBtn: function () {
		if (($('#searchText').val()).length > 0) { // empty input
			$('#srchByPhrase').enable().css('color', bs.srchBtnClr);
		} else {
			$('#srchByPhrase').disable().css('color', '#888888');
		}
	},
	checkSrchByBarcdBtn: function () {
		if (($('#searchBarcd').val()).length > 0) { // empty input
			$('#srchByBarcd').enable().css('color', bs.srchBtnClr);
		} else {
			$('#srchByBarcd').disable().css('color', '#888888');
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
	  bs.checkSrchByPhraseBtn();
	  bs.checkSrchByBarcdBtn();
		$('#marcBtn').val(bs.showMarc);
		if (opacMode) $('#barcodeSearch').hide();
		$('#searchText').focus();
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
	  bs.checkSrchByPhraseBtn();
	  bs.checkSrchByBarcdBtn();
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
			echo "$('#searchBarcd').val('$_REQUEST[barcd]');\n";
			echo "bs.doBarcdSearch();\n";
		}
		else if ($_REQUEST['bibid']) {
			echo "bs.doBibidSearch($_REQUEST[bibid]);\n";
		}
		else if ($_REQUEST['searchText']) {
			echo "$('#searchText').val('$_REQUEST[searchText]');\n";
			echo "$('#searchType').val('$_REQUEST[searchType]');\n";
			echo "bs.doPhraseSearch();\n";
		}
		?>
	},
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(bs.url,{mode:'getOpts'}, function(jsonData){
	    bs.opts = jsonData
		});
	},
	fetchCrntMbrInfo: function () {
	  $.get(bs.url,{mode:'getCrntMbrInfo'}, function(data){
			$('#crntMbrDiv').empty().html(data).show();
		});
	},
	fetchSiteList: function () {
	  $.get(bs.url,{mode:'getSiteList'}, function(data){
			$('#copy_site').html(data);
			// Add all for search sites
			data = '<option value="all"  selected="selected">All</option>' + data;
			$('#srchSites').html(data);
			
			// now ready to begin a search
			bs.doAltStart();
		});
	},	
	fetchMaterialList: function () {
	  $.get(bs.url,{mode:'getMaterialList'}, function(data){
			$('#itemMediaTypes').html(data);
			// Add all for search media
			data = '<option value="all"  selected="selected">All</option>' + data;
			$('#srchMediaTypes').html(data);
		});
	},
	fetchCollectionList: function () {
	  $.get(bs.url,{mode:'getCollectionList'}, function(data){
			$('#itemEditColls').html(data);
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
	  			$('#rsltMsg').html('<?php echo T('Nothing Found by bar cd search') ?>').show();
				}
				else {
					bs.showOneBiblio(bs.biblio)
					bs.fetchCopyInfo();
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
	  			$('#rsltMsg').html('<?php echo T('Nothing Found') ?>').show();
	  			bs.rtnToSrch();
				}
				else {
					bs.multiMode = false;
					bs.showOneBiblio(bs.biblio)
					bs.fetchCopyInfo();
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
	  			  $('#srchRslts').html('<p class="error"><?php echo T('Nothing Found') ?></p>');
				  $('#biblioListDiv .goNextBtn').disable();
				  $('#biblioListDiv .goPrevBtn').disable();
				}

				// single hit
				// Changed to two, as an extra record is added with the amount of records etc. (also, if not first page ignore this) - LJ
				else if (biblioList.length == 2 && firstItem == 0) {
				  bs.multiMode = false;
      		// Changed from 0 to 1 as the first row shows record info
					bs.biblio = $.parseJSON(biblioList[1]);
					bs.showOneBiblio(bs.biblio)
					bs.fetchCopyInfo();
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
			var title = '', subtitle='',
					author='', coauthor='',
					corporate='', year='', journal='', jrnlDate='',
					callNo = '', edition = '', pubDate = ''; 
			var html = '';
			var biblio = JSON.parse(biblioList[nBiblio]);
			bs.biblio[biblio.bibid] = biblio;
			if (biblio.data) {
				$.each(biblio.data, function (fldIndex, fldData) {
					var tmp = JSON.parse(fldData);
					if (!tmp.value) tmp.value = 'n/a';
					switch (tmp.marcTag){
						case '240a': 
						case '245a': title = tmp.value.trim(); 
							break;
						case '245b': subtitle = tmp.value.trim(); 
							break;
						case '100a':
							author = tmp.value.trim();
							if (author && (author.length>30)) author = author.substring(0,30)+'...';
							break;
						case '700a':
							coauthor = tmp.value.trim();
							if (coauthor && (coauthor.length>100)) coauthor = coauthor.substring(0,100)+'...';
							break;
						case '110a':
							corporate = tmp.value.trim();
							if (corporate && (corporate.length>100)) corporate = corporate.substring(0,100)+'...';
							break;
						case '099a': callNo = tmp.value.trim(); 
							break;
						case '773p': journal = tmp.value.trim();
							break;
						case '240f': year = tmp.value.trim();
							break;
						case '130f': jrnlDate = tmp.value.trim();
							break;
						case '260c': pubDate = tmp.value.trim();
							break;
						case '250a': edition = tmp.value.trim();
							break;
					}
				});
			} else {
				// skip these
				title = 'unknown'; callNo = 'not assigned';
				continue;
			}
			// Add subtitle to title
			title = title + ' ' + subtitle;
			
			html += '<tr class="listItem">\n';
			html += '	<td id="itemVisual">\n';
			html += '		<div> \n';
			if (bs.opts.showBiblioPhotos == 'Y') {
				// first we create space for a possible photo //
				html += '		<div id="photo_'+biblio.bibid+'" class="photos" >\n'+
								'			<img src="../images/shim.gif" class="biblioImage noHover" height="50px" width="50px" />\n'+
								'		</div>'+"\n";
	  		$.getJSON(bs.url,{ 'mode':'getPhoto', 'bibid':biblio.bibid  }, function(data){
	  			// when this returns, it will over-write the above shim, if there is anything found //
	  			if (data != null) {
						var theId = data[0].bibid, 
								fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+data[0].url;
						//console.log(theId+'==>>'+fotoFile);
						$('#photo_'+theId).html($('<img src="'+fotoFile+'" class="biblioImage hover">'));
					}
	  		});
			}
			html += '	<div id="dashBd">\n';
			html += '		<img src="../images/'+biblio.avIcon+'" class="flgDot" title="Grn: available<br />Blu: on hold<br />Red: not available" />\n';
			html += '		<img src="../images/'+biblio.imageFile+'" />'+'\n';
			html += '		<br />\n';
			html += '		<input type="hidden" value="'+biblio.bibid+'" />'+'\n';
			html += '		<input type="button" class="moreBtn" value="More info" />'+'\n';
			html += '	</div>\n';
			html += '</div></td>';
			html += '<td id="itemInfo">\n';
			html += '	<p id="itemTitle" wrap >'+title+'</p>\n';
			if ((corporate+author+coauthor) != '') {
				html += ' <p id="itemAuthor" >';
				html += 		corporate;
				html += 		author+';&nbsp;&nbsp;';
				html += 		coauthor;
				html += '	</p>\n';
			}
			if ((journal+year+jrnlDate) != '') {
				html += ' <p id="itemJournal" >';
				html += 		journal+'&nbsp;&nbsp;';
				html += 		year;
				html += 		jrnlDate;
				html += '	</p>\n';
			}
			html += '	<p id="itemCallNo" >';
			if (callNo != '')
				html += callNo;
			if ((callNo != '') && (pubDate != ''))
				html += '&nbsp;&nbsp; --- &nbsp;&nbsp;';
			if (pubDate != '')
				html += pubDate
			if ((pubDate != '') && (edition != '')) 
				'&nbsp;&nbsp; --- &nbsp;&nbsp;';
			if (edition != '')
				html += 		edition;
			html += '	</p>\n';
			html += "</td></tr>\n";

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
		bs.showOneBiblio(bs.biblio[bibid]);
		bs.fetchCopyInfo();
	},
	doAddItemToCart:function () {
    var params = "mode=addToCart&name=bibid&tab=catalog";
	  params += "&id[]="+bs.biblio.bibid;
	  $.post(bs.url,params, function(response){
	    $('#results_found').html(response);
	  });
	},
	
	/* ====================================== */
	showOneBiblio: function (biblio) {
	  if(!biblio)
			bs.theBiblio = $(this).prev().val();
		else
	  	bs.theBiblio = biblio;
		$('#theBibId').html(bs.theBiblio.bibid);
		
  	bs.crntFoto = null;
  	bs.crntBibid = bs.theBiblio.bibid;
		$('#photoEditBtn').hide();		
		$('#photoAddBtn').hide();		
		$('#bibBlkB').html('');
		if (bs.opts.showBiblioPhotos == 'Y') {
  		$.getJSON(bs.url,{ 'mode':'getPhoto', 'bibid':bs.theBiblio.bibid  }, function(data){
  			if (data == null) {
  				bs.crntFoto = data;
					$('#photoAddBtn').show();
					$('#bibBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" >');
  			} else {
  				bs.crntFoto = data[0];
					$('#photoEditBtn').show();
					var fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+bs.crntFoto.url;
					$('#bibBlkB').html($('<img src="'+fotoFile+'" id="biblioFoto" class="hover" >'));
				}
  		});
		}

	  var txt = '';
		$.each(bs.theBiblio.data, function(fldIndex,fldData) {
		  var tmp = JSON.parse(fldData);
		  txt += "<tr>\n";
			txt += "	<td class=\"filterable hilite\">"+tmp.marcTag+"</td>\n";
			txt += "	<td>"+tmp.label+"</td>\n";
			txt += "	<td>"+tmp.value+"</td>\n";
			txt += "</tr>\n";
			if (tmp.marcTag == '245a') {
				bs.crntTitle = tmp.value;
				//console.log('title==>>'+bs.crntTitle);				
			}
		});
		txt += "<tr>\n";
		txt += "	<td class=\"filterable hilite\">&nbsp</td>\n";
		txt += "	<td>Date Added</td>\n";
		txt += "	<td>"+bs.theBiblio.createDt+"</td>\n";
		txt += "</tr>\n";
  	$('tbody#biblio').html(txt);
		obib.reStripe2('biblioTbl','odd');
		$('#biblioDiv td.filterable').hide();
		$('#marcBtn').val(bs.showMarc);

		if (!bs.lookupAvailable)$('#onlnUpdtBtn').hide();
	  $('#searchDiv').hide();
    $('#biblioListDiv').hide()
		$('#biblioDiv').show();
	},
	
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
	
	fetchCopyInfo: function () {
	  $('tbody#copies').html('<tr><td colspan="9"><p class="error"><img src="../images/please_wait.gif" width="26" /><?php echo T("Searching"); ?></p></td></tr>');
	  $.getJSON(bs.url,{'mode':'getCopyInfo','bibid':bs.biblio.bibid}, function(jsonInpt){
				bs.copyJSON = jsonInpt;
				if (!bs.copyJSON) {
					var msg = '(<?php echo T("No copies"); ?>)';
					$('tbody#copies').html('<tr><td colspan="9" class="hilite">'+msg+'</td></tr>');
					return false; // no copies found
				}
				
				var html = '';
				for (nCopy in bs.copyJSON) {
				  var crntCopy = eval('('+bs.copyJSON[nCopy]+')')
				  html += "<tr>\n";
					if (!opacMode) {
						html += "	<td>\n";
						html += '		<input type="button" value="edit" class="editBtn" /> \n';
						html += '		<input type="button" value="delete" class="deltBtn" /> \n';
						html += '		<input type="hidden" value="'+crntCopy.copyid+'">\n';
						html += "	</td>\n";
					}
					if (crntCopy.site) {
						html += "	<td>"+crntCopy.site+"</td>\n";
					}
					else {
						$('#siteFld').hide();
					}
					html += "	<td>"+crntCopy.status
					if (crntCopy.mbrId) {
						var text = 'href="../circ/mbr_view.php?mbrid='+crntCopy.mbrId+'"';
					  html += ' to<br /><a '+text+'>'+crntCopy.mbrName+'</a>';
					}
					html += "	</td>\n";
					html += "	<td>"+bs.makeDueDateStr(crntCopy.last_change_dt)+"</td>\n";
					// Due back is onyl needed when checkked out - LJ
					if(crntCopy.statusCd == "ln" || crntCopy.statusCd == "out"){
						// Sometimes the info has to come out of an array (if coming from list) - LJ
						var daysDueBack = parseInt(bs.biblio.daysDueBack);
						if(isNaN(daysDueBack)) {			
							daysDueBack = parseInt(bs.biblio[bs.biblio.bibid].daysDueBack);
						}					
						html += "	<td>"+bs.makeDueDateStr(crntCopy.last_change_dt,daysDueBack)+"</td>\n";
					} else {
						html += "<td>---</td>";
					}
					html += "	<td>"+crntCopy.barcode_nmbr+"</td>\n";
					html += "	<td>"+crntCopy.copy_desc+"</td>\n";
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
				obib.reStripe2('copyList','odd');

				// dynamically created buttons
				$('.editBtn').on('click',null,bs.doCopyEdit);
				$('.deltBtn').on('click',{'copyid':crntCopy.copyid},bs.doCopyDelete);
	  });
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
		$('#deltFotoBtn').show();
		$('#addFotoBtn').hide();
		$('#fotoMode').val('updatePhoto')
		$('#fotoSrce').attr({'required':false, 'aria-required':false});
		bs.showPhotoForm();
	},
	doPhotoAdd: function () {
		$('#updtFotoBtn').hide(); //see doUpdatePhoto() below
		$('#deltFotoBtn').hide();
		$('#addFotoBtn').show();
		$('#fotoMode').val('addNewPhoto')
		$('#fotoSrce').attr({'required':true, 'aria-required':true});
		bs.showPhotoForm();
	},
	showPhotoForm: function () {
	  $('#biblioDiv').hide();
	  $('#fotoSrce').val('')
		$('#fotoEdLegend').html('Cover Photo for: '+bs.crntTitle);
	  $('#fotoBibid').val(bs.crntBibid);
	  
	  if (bs.crntFoto != null) {
	  	$('#fotoFile').val(bs.crntFoto.url);
	  	$('#fotoBlkB').html('<img src="<?php echo OBIB_UPLOAD_DIR; ?>'+bs.crntFoto.url+'" id="foto" class="hover" >');
	  	$('#fotoCapt').val(bs.crntFoto.caption);
	  	$('#fotoImgUrl').val(bs.crntFoto.imgurl);
	  } else {
	  	$('#fotoBlkB').html('');
	  	$('#fotoFile').val('');
	  	$('#fotoCapt').val('');
	  	$('#fotoImgUrl').val('');
		}
		$('#photoEditorDiv').show();
	},
	doUpdatePhoto: function () {
	  /// left as an exercise for the motivated - FL (I'm burned out on this project)
	},
	doAddNewPhoto: function (e) {
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
														if(typeof(data.error) != 'undefined') {
															if(data.error != '') {
																alert(data.error);
															} else {
																alert(data.msg);
															}
														}
														bs.returnToBiblio();
													},
				error: 						function (data, status, e) { alert(e); }
			});
		e.stopPropagation();
		return false;
	},
	doDeletePhoto: function () {
		if ("<?php echo T("Are you sure you want to delete this cover photo"); ?>") {
	  	$.post(bs.url,{'mode':'deletePhoto',
										 'bibid':$('#fotoBibid').val(),
										 'url':$('#fotoFile').val(),
										 'url':$('#fotoFile').val(),
										 }
										 ,function(response){
				if(response) {
					$('#fotoMsg').html(response);
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
			$('#itemEditorDiv fieldset legend').html('<?php echo T('Edit Item Properties'); ?>');
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
	  var title =  $('#marcBody input.offline:text').filter('#245a').val();
	  var author= ($('#marcBody input.offline:text').filter('#100a').val()).split(',')[0];

	  var isbn  = ($('#marcBody input.offline:text').filter('#020a').val()).split(',');
	  for (var i=0; i<isbn.length; i++) {
	    if (!((isbn[i].substr(0,3) == '978') && (isbn[i].length == 10))) {
	    	var ISBN = isbn[i];
	    	break;
			}
		}

	  if (ISBN) {
	  	//var msgText = <?php T("Searching for ISBN %isbn%", ISBN); ?>;
	  	var msgText = "Searching for ISBN "+ISBN;
	  	params = "&mode=search&srchBy=7&lookupVal="+ISBN;
	  	var item = ISBN
		}
  	else if (title && author) {
	  	var msgText = "Searching for<br />Title: '"+title+"',<br />by "+author;
	  	params = "&mode=search&srchBy=4&lookupVal="+title+"&srchBy2=1004&lookupVal2="+author;
	  	var item = '"'+title+'", by '+author;
		}
	  msgText += '.<br />' + '<?php echo T('this may take a moment.');?>'
		$('#onlineMsg').html(msgText);
		
	  $.post(bs.urlLookup,params,function(response){
			//console.log('params='+params)
			var rslts = eval('('+response+')'); // JSON 'interpreter'
			var numHits = parseInt(rslts.ttlHits);
			var maxHits = parseInt(rslts.maxHits);
			if (numHits < 1) {
				$('#onlineMsg').html(rslts.msg+' '+item);
			}
			else if (numHits >= maxHits) {
			  msgText = '<?php echo T('hits found, too many to process',numHits); ?>'+'.';
				$('#onlineMsg').html();
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
	  
		params = "&mode=updateBiblio&bibid="+bs.biblio.bibid +
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
	  	if (confirm('<?php echo T('Are you sure you want to delete this item?'); ?>')) {
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
	doCopyEdit: function (e) {
		$('#editRsltMsg').html('');
		var copyid = $(this).next().next().val();
		for (nCopy in bs.copyJSON) {
			bs.crntCopy = eval('('+bs.copyJSON[nCopy]+')')
		  if (bs.crntCopy['copyid'] == copyid) break;
		}
		$('#copyTbl #barcode_nmbr').val(bs.crntCopy.barcode_nmbr);
		$('#copyTbl #copy_desc').val(bs.crntCopy.copy_desc);
		$('#copyTbl #copy_site').val([bs.crntCopy.site]);
		$('#copyTbl #status_cd').val(bs.crntCopy.statusCd);
		$('#copyEditorDiv fieldset legend').html("<?php echo T('Edit Copy Properties'); ?>");

		// custom fields
		for(nField in bs.crntCopy.custFields){
			$('#copyTbl #custom_'+bs.crntCopy.custFields[nField].code).val(bs.crntCopy.custFields[nField].data);
		}
		
		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').on('click',null,function () {
			bs.doCopyUpdate();
			// Moved to function
			//bs.rtnToBiblio();
			return false;
		});

		// Set 'update' button to enabled in case it wasn't from a previous edit
		$('#copySubmitBtn').enable().css('color', bs.srchBtnClr);
		
		$('#biblioDiv').hide();
		$('#copyEditorDiv').show();
	  // prevent submit button from firing a 'submit' action
		return false;
	},
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
			bs.doCopyNew();
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
	doCopyNew: function () {
		$('#copyForm #bibid').val(bs.biblio.bibid);
		$('#copyForm #mode').val('newCopy');
		var params= $('#copyForm').serialize()+"&bibid="+bs.biblio.bibid+"&mode=newCopy";
		if ($('#autobarco:checked').length > 0) {
			params += "&barcode_nmbr="+$('#copyTbl #barcode_nmbr').val();
		}
		
		// post to DB
		bs.doPostCopy2DB(params);
	},
	doCopyUpdate: function () {
	  var barcdNmbr = $('#copyTbl #barcode_nmbr').val();
	  
	  // serialize() ignores disabled fields, so cant reliably use in this case
	  var copyDesc = $('#copyTbl #copy_desc').val();
	  var statusCd = $('#copyTbl #status_cd').val();
	  var siteid = $('#copyTbl #copy_site').val();
		params = "&mode=updateCopy&bibid="+bs.biblio.bibid+"&copyid="+bs.crntCopy.copyid
					 + "&barcode_nmbr="+barcdNmbr+"&copy_desc="+copyDesc
					 + "&status_cd="+statusCd+"&siteid="+siteid;

		// Custom fields
		for(nField in bs.crntCopy.custFields){
			// Only add if has a value, or changed from a value to nothing
			if($('#copyTbl #custom_'+bs.crntCopy.custFields[nField].code).val() != bs.crntCopy.custFields[nField].data ||  $('#copyTbl #custom_'+bs.crntCopy.custFields[nField].code).val() != ""){
				params = params + '&custom_'+bs.crntCopy.custFields[nField].code+'='+$('#copyTbl #custom_'+bs.crntCopy.custFields[nField].code).val();
			}
		}					
		// post to DB
		bs.doPostCopy2DB(params);
	},
	doPostCopy2DB: function (parms) {
		//console.log('parms='+parms);
	  $.post(bs.url,parms, function(response){
	  	if(response == '!!success!!') {
				bs.fetchCopyInfo(); // refresh copy display
				$('#editCancelBtn').val('Go Back');
				bs.rtnToBiblio();
			} else {
				$('#editRsltMsg').html(response);
			}
	  });
	  // prevent submit button from firing a 'submit' action
	  return false;
	},
	doCopyDelete: function (e) {
	  $(this).parent().parent().addClass('hilite');
	  if (confirm('<?php echo T('Are you sure you want to delete this copy?'); ?>')) {
	  	//var copyid = e.data.copyid;
		var copyid = $(this).next().val();
	    var params = "&mode=deleteCopy&bibid="+bs.biblio.bibid+"&copyid="+copyid;
	  	$.post(bs.url,params, function(response){
	  	  $('#rsltMsg').html(response);
	  		bs.fetchCopyInfo(); // refresh copy display
	  	});
		};
	  $(this).parent().parent().removeClass('hilite');
		return false;
	}
};
$(document).ready(bs.init);

</script>
