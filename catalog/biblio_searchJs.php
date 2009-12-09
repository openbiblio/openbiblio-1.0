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
// lookup Javascript
bs = {
<?php
//	echo 'editHdr 	 				:"'.T('lookup_optsSettings').'",'."\n";
?>
	init: function () {
		// get header stuff going first
		bs.initWidgets();
		bs.resetForms();

		bs.url = 'biblio_server.php';
		bs.urlLookup = '../lookup2/server.php'; //may not exist

    //$('#advanceQ').disable();
		$('#advancedSrch').hide();
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
		bs.fetchOpts();
		bs.fetchCrntMbrInfo();
		bs.fetchMaterialList();
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
//	  			$('p.error').html('Nothing Found by bar cd search').show();
	  			$('#rsltMsg').html('Nothing Found by bar cd search').show();
				}
				else {
					bs.showOneBiblio(bs.biblio)
					bs.fetchCopyInfo();
				}
	    }
		  $('#searchDiv').hide();
	    $('#biblioDiv').show();
			$('#biblioDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
		});
		return false;
	},
	doPhraseSearch: function (e) {
	  bs.srchType = 'phrase';
	  $('#errSpace').html('');
		$('#srchRsltsDiv').html('');
	  var params = $('#phraseSearch').serialize();
		params += '&mode=doPhraseSearch';
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				var biblioList = eval('('+jsonInpt+')'); // JSON 'interpreter'
				if ((biblioList.length == 0) || ($.trim(jsonInpt) == '[]') ) {
	  			$('#rsltQuan').html('<p class="error">Nothing Found by text search</p>');
					$('#biblioListDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
        	$('#biblioListDiv').show()
		  		$('#searchDiv').hide();
				}
				else if (biblioList.length == 1) {
					bs.biblio = eval('('+biblioList[0]+')');
					bs.showOneBiblio(bs.biblio)
					bs.fetchCopyInfo();
				}
				else {
					$('#rsltQuan').html(biblioList.length+' items found');
					bs.biblio = Array();
				  for (var nBiblio in biblioList) {
				    var html = "<fieldset>\n<table>\n<tr> \n";
						var biblio = eval('('+biblioList[nBiblio]+')');
						bs.biblio[biblio.bibid] = biblio;
						var callNo = ''; var title = '';
						if (biblio.data) {
							$.each(biblio.data, function (fldIndex, fldData) {
		  					var tmp = eval('('+fldData+')');
				    	  if (tmp.label == 'Title') 			title = tmp.value;
				    	  if (tmp.label == 'Call Number') callNo = tmp.value;
							});
						} else {
						  // skip these
              title = 'unknown'; callNo = 'not assigned';
              continue;
						}
						if (bs.opts.showBiblioPhotos == 'Y') {
							html += '<td id="photo_'+biblio.bibid+'" class="biblioImage">'+
											'		<img src=\"../images/shim.gif\" />'+
											'</td>'+"\n";
	  					$.get(bs.url,{mode:'getPhoto'}, function(data){
	  					  $('#srchRsltsDiv #photo_'+biblio.bibid).html(data);
	  					});
						}
						html += '<td><img src="../images/'+biblio.imageFile+'" />'+title+"\n";
						html += '<br />'+callNo+"\n";
						html += '<div class="biblioBtn">'+"\n";
						html += '	<input type="hidden" value="'+biblio.bibid+'" />'+"\n";
						html += '	<input type="button" class="moreBtn" value="This One!" />'+"\n";
						html += "</div> \n";
				    html += "</tr>\n</table>\n</fieldset> \n";
				    $('#srchRsltsDiv').append(html);
					}
					$('.moreBtn').bind('click',null,bs.getPhraseSrchDetails);
					$('#biblioListDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
        	$('#biblioListDiv').show()
		  		$('#searchDiv').hide();
				}
	    }

		});
		return false;
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
		$('#biblioDiv .gobkBtn').bind('click',null,bs.rtnToList);
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
	  var txt = '';
		$.each(biblio.data, function(fldIndex,fldData) {
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
		txt += "	<td>"+biblio.createDt+"</td>\n";
		txt += "</tr>\n";
  	$('tbody#biblio').html(txt);
		obib.reStripe2('biblioTbl','odd');
		$('#biblioEditBtn').bind('click',null,bs.doItemEdit);
		$('#biblioDiv td.filterable').hide();
		$('#marcBtn').bind('click',null,function () {
		  //console.log('swapping state to MARC column');
			$('#biblioDiv td.filterable').toggle()
			}
		);
		$('#biblioDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
		if (!bs.lookupAvailable)$('#onlnUpdtBtn').hide();
	  $('#searchDiv').hide();
    $('#biblioListDiv').hide()
		$('#biblioDiv').show();
	},
	makeDueDateStr: function (dtOut) {
		var dt = dtOut.split(' ');
		var dat = dt[0]; var tm = dt[1];
		var datAray = dat.split('-');
		var theYr = datAray[0];
		var theMo = datAray[1]-1;
		var theDy = datAray[2];
		var dateOut = new Date(theYr,theMo,theDy);
		dateOut.setDate(dateOut.getDate() + parseInt(bs.biblio.daysDueBack));
		return dateOut.toDateString();
	},
	fetchCopyInfo: function () {
	  $.getJSON(bs.url,{'mode':'getCopyInfo','bibid':bs.biblio.bibid}, function(jsonInpt){
	      bs.copyJSON = jsonInpt;
				var html = '';
				for (nCopy in bs.copyJSON) {
				  var crntCopy = eval('('+bs.copyJSON[nCopy]+')')
				  html += "<tr>\n";
					html += "	<td>\n";
					html += "		<a href='' class=\"editBtn\" >edit</a>\n";
					html += "		<a href='' class=\"deltBtn\" >del</a>\n";
					html += "		<input type=\"hidden\" value=\""+crntCopy.copyid+"\">\n";
					html += "	</td>\n";
					html += "	<td>"+crntCopy.barcode_nmbr+"</td>\n";
					html += "	<td>"+crntCopy.copy_desc+"</td>\n";
					if (crntCopy.site) {
						html += "	<td>"+crntCopy.site+"</td>\n";
					}
					else {
						$('#siteFld').hide();
					}
					html += "	<td>"+crntCopy.status
					if (crntCopy.mbrId)
					  html += ' to <a href=\"../circ/mbr_view.php?mbrid='+crntCopy.mbrId+'\">'
								 + crntCopy.mbrName+'</a>';
					html += "</td>\n";
					html += "	<td>"+crntCopy.last_change_dt+"</td>\n";
					html += "	<td>"+bs.makeDueDateStr(crntCopy.last_change_dt)+"</td>\n";
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
				obib.reStripe2('copyList','odd');
				$('.editBtn').bind('click',null,bs.doCopyEdit);
				$('.deltBtn').bind('click',null,bs.doCopyDelete);
	  });
	},

	//------------------------------
	findMarcField: function (tag) {
	  for (var i=0; i< bs.biblio.data.length; i++) {
			var tmp = eval('('+bs.biblio.data[i]+')');
			if (tmp.marcTag == tag) {
				return tmp;
			}
		};
		return null;
	},
	findMarcFieldSet: function (tag) {
	  var fldSet = []; var n = 0;
	  for (var i=0; i< bs.biblio.data.length; i++) {
			var tmp = eval('('+bs.biblio.data[i]+')');
			if (tmp.marcTag == tag) {
				fldSet[n] = tmp;  n++;
			}
		}
		return fldSet;
	},
	doItemEdit: function () {
		bs.fetchCollectionList();
	  $('#biblioDiv').hide();
	  $.get(bs.url,{'mode':'getBiblioFields',
									'bibid':bs.biblio.bibid,
									'matlCd':bs.biblio.matlCd,
									'collCd':bs.biblio.collCd},
									function (response) {
			$('#marcBody').html(response);
			$('#itemEditorDiv fieldset legend').html('<?php echo T('Edit Item Properties'); ?>');
			$('#itemEditorDiv td.filterable').hide();
			obib.reStripe2('biblioFldTbl','odd');

			// fill non-MARC fields with data on hand
			$('#nonMarcBody #mediaType').val([bs.biblio.matlCd]);
			$('#nonMarcBody #collectionCd').val([bs.biblio.collCd]);
			$('#nonMarcBody #opacFlg').val([bs.biblio.opacFlg]);
			
			// fill MARC fields with data on hand
			// first non-repeating fields
			$('#marcBody input.only1:text').each(function (){
			  var tmp = bs.findMarcField(this.id);
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
			  	bs.tmpList = bs.findMarcFieldSet(this.id);
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

	  	$('#onlnUpdtBtn').bind('click',null,function (){
	  	  var hidingRows = $('#itemEditorDiv td.filterable');
	  	  if (hidingRows.is(':hidden')) {
	  	    // here is where we would go get on-line data using the lookup2 engine.
					$('#itemEditorDiv td.filterable').show();
					bs.fetchOnlnData();
				}
				else
					$('#itemEditorDiv td.filterable').hide();
			});

			$('#itemSubmitBtn').val('<?php echo T('Update'); ?>')
												 .bind('click',null,bs.doItemUpdate);
												 
			$('.itemGobkBtn').bind('click',null,function () {
   	 		$('#itemEditorDiv').hide();
		  	$('#biblioDiv').show();
			});

    	$('#itemEditorDiv').show();
		});
		
	},
	fetchOnlnData: function () {
	  var title =  $('#marcBody input.offline:text').filter('#245a').val();
	  var author= ($('#marcBody input.offline:text').filter('#100a').val()).split(',')[0];

	  var isbn  = ($('#marcBody input.offline:text').filter('#020a').val()).split(',');
	  for (var i=0; i<isbn.length; i++) {
	    if ((isbn[i].substr(0,3) == '978') && (isbn[i].length == 10)) continue;
	    var ISBN = isbn[i];
		}
		
	  if (ISBN) {
	  	var msgText = 'Searching for ISBN: "'+ISBN+'"';
	  	params = "&mode=search&srchBy=7&lookupVal="+ISBN;
		}
  	else {
	  	var msgText = 'Searching for<br />Title: "'+title+'",<br />by '+author;
	  	params = "&mode=search&srchBy=4&lookupVal="+title+"&srchBy2=1004&lookupVal2="+author;
		}
	  
		$('#onlineMsg').html(msgText+'.<br />this may take a moment.');
	  $.post(bs.urlLookup,params,function(response){
			var rslts = eval('('+response+')'); // JSON 'interpreter'
			var numHits = parseInt(rslts.ttlHits);
			var maxHits = parseInt(rslts.maxHits);
			if (numHits < 1) {
				$('#onlineMsg').html('nothing found');
			}
			else if (numHits >= maxHits) {
				$('#onlineMsg').html(numHits+'hits found, too many to process.');
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
			if (bs.srchType == 'barCd') bs.doBarCdSearch();
			else if (bs.srchType = 'phrase') bs.doPhraseSearch();
	  });
	  return false;
	},
	doItemDelete: function () {
	  //### FIXME - must have NO copies to allow delete !!!!!!
	  if (confirm('<?php echo T('Are you sure you want to delete this item ?'); ?>')) {
	  	var copyid = $(this).next().val();
	    var params = "&mode=deleteCopy&bibid="+bs.biblio.bibid+"&copyid="+copyid;
	  	$.post(bs.url,params, function(response){
	  	  $('#rsltMsg').html(response);
	  		bs.fetchCopyInfo(); // refresh copy display
	  	});
		};
	  $(this).parent().parent().removeClass('hilite');
		return false;
	},

	//------------------------------
	doCopyEdit: function (e) {
 	  $('#editRsltMsg').html('');
	  var copyid = $(this).next().next().val();
		for (nCopy in bs.copyJSON) {
			bs.crntCopy = eval('('+bs.copyJSON[nCopy]+')')
		  if (bs.crntCopy['copyid'] == copyid) break;
		}
		$('#copyTbl #barcode_nmbr').val(bs.crntCopy.barcode_nmbr);
		$('#copyTbl #copy_desc').val(bs.crntCopy.copy_desc);
		$('#copyTbl #status_cd').val(bs.crntCopy.status_cd);
		$('#copyEditorDiv fieldset legend').html('<?php echo T('Edit Copy Properties'); ?>');
		$('#editSubmitBtn').val('<?php echo T('Update'); ?>');
		$('#editSubmitBtn').bind('click',null,bs.doCopyUpdate);
		$('#editCancelBtn').val('<?php echo T('Go Back'); ?>');
		$('#editCancelBtn').bind('click',null,function () {
			$('#copyEditorDiv').hide();
			$('#biblioDiv').show();
		});
		$('#biblioDiv').hide();
		$('#copyEditorDiv').show();
		return false;
	},
	makeNewCopy: function () {
		$('#biblioDiv').hide();
		if ($('#autobarco:checked').length > 0) {
			bs.doGetBarcdNmbr();
		}
		$('#copyEditorDiv').show();
		$('#editSubmitBtn').bind('click',null,bs.doCopyNew);
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
//	    $('#editCancelBtn').val('Go Back');
	  });
	  return false;
	},
	doCopyUpdate: function () {
		if ($('#copyTbl #barcode_nmbr').attr('disabled')) {
	  	var barcdNmbr = bs.crntCopy.barcode_nmbr;
		} else {
	  	var barcdNmbr = $('#copyTbl #barcode_nmbr').val();
	  }
	  var copyDesc = $('#copyTbl #copy_desc').val();
	  var statusCd = $('#copyTbl #status_cd').val();
		params = "&mode=updateCopy&bibid="+bs.biblio.bibid+"&copyid="+bs.crntCopy.copyid
						+"&barcode_nmbr="+barcdNmbr+"&copy_desc="+copyDesc
						+"&status_cd="+statusCd;
	  $.post(bs.url,params, function(response){
	  	$('#editRsltMsg').html(response);
	  	bs.fetchCopyInfo(); // refresh copy display
	    $('#editCancelBtn').val('Go Back');
	  });
	  return false;
	},
	doCopyDelete: function () {
	  $(this).parent().parent().addClass('hilite');
	  if (confirm('<?php echo T('Are you sure you want to delete this copy ?'); ?>')) {
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
