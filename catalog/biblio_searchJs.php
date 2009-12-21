<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
<style>
.biblioImage {
	float left;
	border: 3px solid green;
	height: 50px; width: 50px;
	}
#onlineMsg {
	color: blue;
	}
</style>

<script language="JavaScript" >
// JavaScript Document
//------------------------------------------------------------------------------
// biblio_search Javascript
bs = {
	multiMode: false,
	
	init: function () {
		// get header stuff going first
		bs.initWidgets();
		bs.resetForms();

		bs.url = 'biblio_server.php';
		bs.urlLookup = '../online/server.php'; //may not exist

		$('#advancedSrch').hide();
		$('#advanceQ:checked').val(['N'])
		$('#advanceQ').bind('click',null,function(){
			if ($('#advanceQ:checked').val() == 'Y')
				$('#advancedSrch').show();
			else
				$('#advancedSrch').hide();
		});
		$('#srchByBarcd').bind('click',null,bs.doBarcdSearch);
		$('#srchByPhrase').bind('click',null,bs.doPhraseSearch);
		
		// for the search results section
		$('#addNewBtn').bind('click',null,bs.makeNewCopy);
		$('#addList2CartBtn').bind('click',null,bs.doAddListToCart);
		$('#addItem2CartBtn').bind('click',null,bs.doAddItemToCart);
		$('#biblioListDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
		$('#biblioListDiv .goPrevBtn').bind('click',null,function () {bs.goPrevPage(bs.previousPageItem);});
		$('#biblioListDiv .goNextBtn').bind('click',null,function () {bs.goNextPage(bs.nextPageItem);});
		$('#biblioListDiv .goNextBtn').disable();
		$('#biblioListDiv .goPrevBtn').disable();

		// for the copy editor function
		// to handle startup condition
		if ($('#autobarco:checked').length > 0) {
			$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').bind('change',null,function (){
		  if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
			}
			else {
				$('#barcode_nmbr').enable();
			}
		});
		
		// for the single biblio display screen
		$('#biblioEditBtn').bind('click',null,function () {
			bs.doItemEdit(bs.theBiblio);
		});
		$('#biblioDeleteBtn').bind('click',null,function () {
			bs.doItemDelete(bs.theBiblio);
		});
		$('#marcBtn').bind('click',null,function () {
		  //console.log('swapping state to MARC column');
			$('#biblioDiv td.filterable').toggle()
		});
		$('#biblioDiv .gobkBtn').bind('click',null,function () {
		  if (bs.multiMode) {
				if (bs.srchType = 'phrase')
					bs.doPhraseSearch();
				else
					bs.doBarCdSearch();
				bs.rtnToList();
			} else {
				//bs.doBarCdSearch();
			  bs.rtnToSrch();
			}
		});

		// for the item edit and online update functions
	  $('#onlnUpdtBtn').bind('click',null,function (){
			$('#onlnDoneBtn').show();
			$('#onlnUpdtBtn').hide();
			$('#itemEditorDiv td.filterable').show();
			bs.fetchOnlnData();
		});
	  $('#onlnDoneBtn').bind('click',null,function (){
			$('#itemEditorDiv td.filterable').hide();
			$('#onlnUpdtBtn').show();
			$('#onlnDoneBtn').hide();
		});
		$('#itemSubmitBtn').val('<?php echo T('Update'); ?>')
											 .bind('click',null,bs.doItemUpdate);
		$('.itemGobkBtn').bind('click',null,function () {
   		$('#itemEditorDiv').hide();
		 	$('#biblioDiv').show();
		});
			
		// for the copy editor screen
		$('#copySubmitBtn').val('<?php echo T('Update'); ?>');
		$('#copySubmitBtn').bind('click',null,function () {
			bs.doCopyUpdate();
			bs.rtnToBiblio();
		});
		$('#copyCancelBtn').val('<?php echo T('Go Back'); ?>');
		$('#copyCancelBtn').bind('click',null,function () {
			bs.rtnToBiblio();
		});

		bs.fetchOpts();
		bs.fetchCrntMbrInfo();
		bs.fetchMaterialList();
		bs.fetchSiteList();
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
	  bs.multiMode = false;
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
	},

	rtnToList: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').show();
	  $('#searchDiv').hide();
	  $('#itemEditorDiv').hide();
	  $('#copyEditorDiv').hide();
	},

	rtnToBiblio: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').show();
	  $('#biblioListDiv').hide();
	  $('#searchDiv').hide();
	  $('#itemEditorDiv').hide();
	  $('#copyEditorDiv').hide();
	},

	//------------------------------
	fetchOpts: function () {
	  $.getJSON(bs.url,{mode:'getOpts'}, function(jsonData){
	    bs.opts = jsonData
//			if (bs.opts.lookupAvail == 1) {
//				bs.lookupAvailable = true;
//				//console.log('lookup engine available');
//			}
		});
	},
	fetchCrntMbrInfo: function () {
	  $.get(bs.url,{mode:'getCrntMbrInfo'}, function(data){
			$('#crntMbrDiv').empty().html(data).show();
		});
	},
	fetchMaterialList: function () {
	  $.get(bs.url,{mode:'getMaterialList'}, function(data){
			$('#srchMatTypes').html(data);
			$('#itemMediaTypes').html(data);
		});
	},
	fetchCollectionList: function () {
	  $.get(bs.url,{mode:'getCollectionList'}, function(data){
			$('#itemEditColls').html(data);
		});
	},
	fetchSiteList: function () {
	  $.get(bs.url,{mode:'getSiteList'}, function(data){
			$('#copy_site').html(data);
			// Add all for search sites
			data = '<option value="all"  selected="selected">All</option>' + data;
			$('#srchSites').html(data);
		});
	},	
	
	//------------------------------
	doBarcdSearch: function (e) {
	  bs.srchType = 'barCd';
	  $('p.error').html('').hide();
	  var params = $('#barcodeSearch').serialize();
		params += '&mode=doBarcdSearch';
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				bs.biblio = eval('('+jsonInpt+')'); // JSON 'interpreter'
				if (!bs.biblio.data) {
	  			$('#rsltMsg').html('<?php T('Nothing Found by bar cd search') ?>').show();
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
	doPhraseSearch: function (e,firstItem) {
	  if(firstItem==null) firstItem=0;
	  bs.srchType = 'phrase';
	  $('#errSpace').html('');
		$('#srchRsltsDiv').html('');
	  var params = $('#phraseSearch').serialize();
		params += '&mode=doPhraseSearch&firstItem='+firstItem;
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				var biblioList = eval('('+jsonInpt+')'); // JSON 'interpreter'
				
				// no hits
				if ((biblioList.length == 0) || ($.trim(jsonInpt) == '[]') ) {
				  bs.multiMode = false;
	  			$('#srchRsltsDiv').html('<p class="error">Nothing Found by text search</p>');
					$('#biblioListDiv .goNextBtn').disable();
					$('#biblioListDiv .goPrevBtn').disable();
        	$('#biblioListDiv').show()
		  		$('#searchDiv').hide();
				}

				// single hit
				// Changed to two, as an extra record is added with the amount of records etc. (also, if not first page ignore this) - LJ
				else if (biblioList.length == 2 && firstItem == 0) {
				  bs.multiMode = false;
      		// Changed from 0 to 1 as the first row shows record info
					bs.biblio = eval('('+biblioList[1]+')');
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
	showList: function (firstItem, biblioList) {
	  if(firstItem==null){
	    firstItem=0;
	  }
	  
		// Modified in order to limit results per page. First "record" contains this data - LJ
		var queryInfo = eval('('+biblioList[0]+')');
		var modFirstItem = parseInt(queryInfo.firstItem) + 1;
		$('#rsltQuan').html(' '+queryInfo.totalNum+' <?php T("items"); ?>('+modFirstItem+'-'+queryInfo.lastItem+ ') ');
		bs.biblio = Array();

		// Added table for showing a list view and better alignment
		var header = "<fieldset>\n<table id=\"listTbl\">\n<tbody class=\"striped\">\n";
		$('#srchRsltsDiv').html(header);

		for (var nBiblio in biblioList) {
			var html = "<tr> \n";
			var biblio = eval('('+biblioList[nBiblio]+')');
			bs.biblio[biblio.bibid] = biblio;
			var callNo = ''; var title = ''; var author=''; var subtitle='';
			if (biblio.data) {
				$.each(biblio.data, function (fldIndex, fldData) {
					var tmp = eval('('+fldData+')');
					if (tmp.label == 'Title'){
						title = tmp.value;
					}
					if (tmp.label == 'Subtitle'){
						subtitle = tmp.value;
					}					
					if (tmp.label == 'Author') {
						author = tmp.value;
						if (author && (author.length>30)) author = author.substring(0,30)+'...';
					}
					if (tmp.label == 'Call Number') callNo = tmp.value;
				});
			} else {
				// skip these
				title = 'unknown'; callNo = 'not assigned';
				continue;
			}
			// Add subtitle to title and chop on 50 charaters if needed
			title = title + ' ' + subtitle;
			if(title.length>50) title = title.substring(0,50)+'...';
			
			if (bs.opts.showBiblioPhotos == 'Y') {
				html += '<td id="photo_'+biblio.bibid+'" class="biblioImage">'+
								'		<img src=\"../images/shim.gif\" />'+
								'</td>'+"\n";
	  		$.get(bs.url,{mode:'getPhoto'}, function(data){
	  		  $('#srchRsltsDiv #photo_'+biblio.bibid).html(data);
	  		});
			}
			html += '<td>\n';
			html += '	<input type="hidden" value="'+biblio.bibid+'" />'+"\n";
			html += '	<input type="button" class="moreBtn" value="More info" />'+"\n";
			html += '<td>\n';
			html += '<td><img src="../images/'+biblio.avIcon+'" alt="Grn: available<br />Blu: on hold<br />Red: not available" /></td>\n';
			html += '<td><img src="../images/'+biblio.imageFile+'" /></td>'+"\n";
			html += '<td>'+title+"</td>\n";
			html += '<td>'+author+'</td><td>'+callNo+"</td>\n";
			html += '<td><div class="biblioBtn">'+"\n";
			html += "</div></td> \n";
			html += "</tr>\n";
			$('#listTbl tbody').append(html);
		}
		var trailer = "</tbody></table>";
		$('#srchRsltsDiv').append(trailer);
		   obib.reStripe();

	  // subject button is created dynamically, so duplicate binding is not possible
		$('.moreBtn').bind('click',null,bs.getPhraseSrchDetails);
			if(parseInt(firstItem)>=parseInt(queryInfo.itemsPage)){
			bs.previousPageItem = parseInt(firstItem) - parseInt(queryInfo.itemsPage);
			$('#biblioListDiv .goPrevBtn').enable();
		} else {
			$('#biblioListDiv .goPrevBtn').disable();
		}
		if((parseInt(queryInfo.itemsPage) + parseInt(firstItem) <= parseInt(queryInfo.lastItem))&&(parseInt(queryInfo.totalNum)!=parseInt(queryInfo.lastItem))){
			bs.nextPageItem = parseInt(queryInfo.itemsPage) + parseInt(firstItem);
			$('#biblioListDiv .goNextBtn').enable();
		} else {
			$('#biblioListDiv .goNextBtn').disable();
		}
     	$('#biblioListDiv').show()
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
	showOneBiblio: function (biblio) {
	  bs.theBiblio = biblio;
	  var txt = '';
		$.each(bs.theBiblio.data, function(fldIndex,fldData) {
		  var tmp = eval('('+fldData+')');
		  txt += "<tr>\n";
			txt += "	<td class=\"filterable\">"+tmp.marcTag+"</td>\n";
			txt += "	<td>"+tmp.label+"</td>\n";
			txt += "	<td>"+tmp.value+"</td>\n";
			txt += "</tr>\n";
		});
		txt += "<tr>\n";
		txt += "	<td class=\"filterable\">&nbsp</td>\n";
		txt += "	<td>Date Added</td>\n";
		txt += "	<td>"+bs.theBiblio.createDt+"</td>\n";
		txt += "</tr>\n";
  	$('tbody#biblio').html(txt);
		obib.reStripe2('biblioTbl','odd');
		$('#biblioDiv td.filterable').hide();

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
	  $.getJSON(bs.url,{'mode':'getCopyInfo','bibid':bs.biblio.bibid}, function(jsonInpt){
				bs.copyJSON = jsonInpt;
				if (!bs.copyJSON) {
  				$('tbody#copies').html('<?php T("No Copies."); ?>');
					return false; // no copies found
				}
				
				var html = '';
				for (nCopy in bs.copyJSON) {
				  var crntCopy = eval('('+bs.copyJSON[nCopy]+')')
				  html += "<tr>\n";
					if (!opacMode) {
						html += "	<td>\n";
						html += "		<a href='' class=\"editBtn\" >edit</a>\n";
						html += "		<a href='' class=\"deltBtn\" >del</a>\n";
					html += "		<input type=\"hidden\" value=\""+crntCopy.copyid+"\">\n";
						html += "	</td>\n";
					}
					html += "	<td>"+crntCopy.barcode_nmbr+"</td>\n";
					html += "	<td>"+crntCopy.copy_desc+"</td>\n";
					if (crntCopy.site) {
						html += "	<td>"+crntCopy.site+"</td>\n";
					}
					else {
						$('#siteFld').hide();
					}
					html += "	<td>"+crntCopy.status
					if (crntCopy.mbrId) {
					  html += ' to <a href=\"../circ/mbr_view.php?mbrid='+crntCopy.mbrId+'\">'
								 + crntCopy.mbrName+'</a>';
					}
					html += "	</td>\n";
					html += "	<td>"+bs.makeDueDateStr(crntCopy.last_change_dt)+"</td>\n";
					// Sometimes the info has to come ou of an array (if coming from list) - LJ
					var daysDueBack = parseInt(bs.biblio.daysDueBack);
					if(isNaN(daysDueBack)) {			
						daysDueBack = parseInt(bs.biblio[bs.biblio.bibid].daysDueBack);
					}					
					html += "	<td>"+bs.makeDueDateStr(crntCopy.last_change_dt,daysDueBack)+"</td>\n";
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
				obib.reStripe2('copyList','odd');

				// dynamically created buttons
				$('.editBtn').bind('click',null,bs.doCopyEdit);
				$('.deltBtn').bind('click',{'copyid':crntCopy.copyid},bs.doCopyDelete);
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
	doItemEdit: function (biblio) {
		bs.fetchCollectionList();
		$('#onlnUpdtBtn').show();
		$('#onlnDoneBtn').hide();

	  $('#biblioDiv').hide();
	  $.get(bs.url,{'mode':'getBiblioFields',
									'bibid':biblio.bibid,
									'matlCd':biblio.matlCd,
									'collCd':biblio.collCd},
									function (response) {
			$('#marcBody').html(response);
			$('#itemEditorDiv fieldset legend').html('<?php echo T('Edit Item Properties'); ?>');
			$('#itemEditorDiv td.filterable').hide();
			obib.reStripe2('biblioFldTbl','odd');

			// fill non-MARC fields with data on hand
			$('#nonMarcBody #mediaType').val([biblio.matlCd]);
			$('#nonMarcBody #collectionCd').val([biblio.collCd]);
			$('#nonMarcBody #opacFlg').val([biblio.opacFlg]);
			
			// fill MARC fields with data on hand
			// first non-repeating fields
			$('#marcBody input.only1:text').each(function (){
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

	  	 var hidingRows = $('#itemEditorDiv td.filterable');

    	$('#itemEditorDiv').show();
		});
	},
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
	  msgText += '.<br />' + <?php T('this may take a moment.');?>
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
			  msgText = <?php T('hits found, too many to process',numHits); ?>+'.';
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
				$('#marcBody input.accptBtn').bind('click',null,bs.doFldUpdt);
			} // else
		}); // .post
	},
	doFldUpdt: function (e) {
		var rowNmbr = ((e.target.id).split('_'))[1];
		var srcId = '#marcBody input[name="onln_'+rowNmbr+'[data]"]';
		var text = $(srcId).val();
		//console.log('you clicked btn #'+rowNmbr+' containing "'+text+'" from '+srcId );
		var destId = '#marcBody input[name="fields['+rowNmbr+'][data]"]';
		$(destId).val(text);
	},
	doItemUpdate: function () {
		params = "&mode=updateBiblio&bibid="+bs.biblio.bibid +
						 '&'+ $('#biblioEditForm').not('.online').serialize();
	  $.post(bs.url,params, function(response){
	  	$('#itemRsltMsg').html(response);
			bs.rtnToBiblio()
			if (bs.srchType == 'barCd')
				bs.doBarCdSearch();
			else if (bs.srchType = 'phrase')
				bs.doPhraseSearch();
//			bs.rtnToBiblio()
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

	//------------------------------
	doCopyEdit: function (e) {
		$('#editRsltMsg').html('');
		var copyid = $(this).next().next().val();
console.log('copy id='+copyid);
		for (nCopy in bs.copyJSON) {
			bs.crntCopy = eval('('+bs.copyJSON[nCopy]+')')
		  if (bs.crntCopy['copyid'] == copyid) break;
		}
		$('#copyTbl #barcode_nmbr').val(bs.crntCopy.barcode_nmbr);
		$('#copyTbl #copy_desc').val(bs.crntCopy.copy_desc);
		$('#copyTbl #copy_site').val([bs.crntCopy.site]);
		$('#copyTbl #status_cd').val(bs.crntCopy.status_cd);
		$('#copyEditorDiv fieldset legend').html("<?php echo T('Edit Copy Properties'); ?>");

		// custom fields
		for(nField in bs.crntCopy.custFields){
			$('#copyTbl #custom_'+bs.crntCopy.custFields[nField].code).val(bs.crntCopy.custFields[nField].data);
		}
		
		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').bind('click',null,function () {
			bs.doCopyUpdate();
			bs.rtnToBiblio();
			return false;
		});

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
		$('#copyEditorDiv').show();

		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').bind('click',null,function () {
			bs.doCopyNew();
			bs.rtnToBiblio();
			return false;
		});
	  // prevent submit button from firing a 'submit' action
		return false;
	},
	doGetBarcdNmbr: function () {
		$.getJSON(bs.url,{'mode':'getBarcdNmbr','bibid':bs.biblio.bibid}, function(jsonInpt){
		  $('#copyTbl #barcode_nmbr').val(jsonInpt.barcdNmbr);
		});
	},
	doCopyNew: function () {
		var params= $('#copyForm').serialize() + "&mode=newCopy&bibid="+bs.biblio.bibid;
		if ($('#autobarco:checked').length > 0) {
			params += "&barcode_nmbr="+$('#copyTbl #barcode_nmbr').val();
		}
	  $.post(bs.url,params, function(response){
	  	$('#editRsltMsg').html(response);
	  	bs.fetchCopyInfo(); // refresh copy display
	  	return false;
	  });
	  // prevent submit button from firing a 'submit' action
	  return false;
	},
	doCopyUpdate: function () {
		if ($('#copyTbl #barcode_nmbr').attr('disabled')) {
	  	var barcdNmbr = bs.crntCopy.barcode_nmbr;
		} else {
	  	var barcdNmbr = $('#copyTbl #barcode_nmbr').val();
	  }
	  // serialize() ignores disabled fields, so cant reliably use in this case
	  var copyDesc = $('#copyTbl #copy_desc').val();
	  var statusCd = $('#copyTbl #status_cd').val();
	  var siteid = $('#copyTbl #copy_site').val();
		params = "&mode=updateCopy&bibid="+bs.biblio.bibid+"&copyid="+bs.crntCopy.copyid
					 + "&barcode_nmbr="+barcdNmbr+"&copy_desc="+copyDesc
					 + "&status_cd="+statusCd+"&siteid="+siteid;
		// Custom fields
		for(nField in bs.crntCopy.custFields){
			params = params + '&custom_'+bs.crntCopy.custFields[nField].code+'='+$('#copyTbl #custom_'+bs.crntCopy.custFields[nField].code).val();
		}					
		//console.log('params='+params);
	  $.post(bs.url,params, function(response){
	  	$('#editRsltMsg').html(response);
	  	bs.fetchCopyInfo(); // refresh copy display
	    $('#editCancelBtn').val('Go Back');
	  });
	  // prevent submit button from firing a 'submit' action
	  return false;
	},
	doCopyDelete: function (e) {
	  $(this).parent().parent().addClass('hilite');
	  if (confirm('<?php echo T('Are you sure you want to delete this copy?'); ?>')) {
	  	var copyid = e.data.copyid;
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
