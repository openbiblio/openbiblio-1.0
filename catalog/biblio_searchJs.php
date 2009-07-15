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

		bs.url = 'biblio_searchSrvr.php';

		$('#advancedSrch').hide();
		$('#advanceQ').bind('click',null,function(){
			if ($('#advanceQ:checked').val() == 'Y')
				$('#advancedSrch').show();
			else
				$('#advancedSrch').hide();
		});
		
		// for the search results section
		$('#srchByBarcd').bind('click',null,bs.doBarcdSearch);
		$('#srchByPhrase').bind('click',null,bs.doPhraseSearch);
		$('#addNewBtn').bind('click',null,bs.makeNewCopy);

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

		$('#editCancelBtn').bind('click',null,bs.rtnFmCopyEdit);

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
		$('#errSpace').hide();
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').hide();
	  $('#copyEditorDiv').hide();
	},
	
	rtnToSrch: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').hide();
	  $('#searchDiv').show();
	},

	rtnToList: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#biblioDiv').hide();
	  $('#biblioListDiv').show();
	  $('#searchDiv').hide();
	},

	//------------------------------
	fetchCrntMbrInfo: function () {
	  $.get(bs.url,{mode:'getCrntMbrInfo'}, function(data){
			$('#crntMbrDiv').empty().html(data).show();
		});
	},
	fetchMaterialList: function () {
	  $.get(bs.url,{mode:'getMaterialList'}, function(data){
			$('#matTypes').html(data);
		});
	},
	
	//------------------------------
	doBarcdSearch: function (e) {
	  $('#errSpace').html('');
	  var params = $('#barcodeSearch').serialize();
		params += '&mode=doBarcdSearch';
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				bs.biblio = eval('('+jsonInpt+')'); // JSON 'interpreter'
				if (bs.biblio.data == null) {
	  			$('#search_results').html('<p>Nothing Found</p>');
				}
				else {
					$('#biblioDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
					bs.showOneBiblio(bs.biblio)
					bs.fetchCopyInfo();
				}
	    }
		});
		return false;
	},
	doPhraseSearch: function (e) {
	  $('#errSpace').html('');
		$('#srchRsltsDiv').html('');
	  var params = $('#phraseSearch').serialize();
		params += '&mode=doPhraseSearch';
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				var biblioList = eval('('+jsonInpt+')'); // JSON 'interpreter'
				if (biblioList.length == 0) {
	  			$('#rsltQuan').html('<p>Nothing Found</p>');
				}
				else {
					$('#rsltQuan').html(biblioList.length+' items found');
					bs.biblio = Array();
				  for (var nBiblio in biblioList) {
				    var html = "<fieldset>\n<table>\n<tr> \n";
						var biblio = eval('('+biblioList[nBiblio]+')');
						bs.biblio[biblio.bibid] = biblio;
						var callNo = ''; var title = '';
						$.each(biblio.data, function (fldIndex, fldData) {
		  				var tmp = eval('('+fldData+')');
				      if (tmp.label == 'Title') 			title = tmp.value;
				      if (tmp.label == 'Call Number') callNo = tmp.value;
						});
						html += '<td class="biblioImage"><img src=\"../images/shim.gif\" /></td>'+"\n";
						html += '<td><img src="../images/'+biblio.imageFile+'" />'+title+"\n";
						html += '<br />'+callNo+"\n";
						html += '<div class="biblioBtn">'+"\n";
						html += '	<input type="hidden" value="'+biblio.bibid+'" />'+"\n";
						html += '	<input type="button" class="moreBtn" value="This One!" />'+"\n";
						html += "</div> \n";
				    html += "</tr>\n</table>\n</fieldset> \n";
				    $('#srchRsltsDiv').append(html);
					}
				}
				$('.moreBtn').bind('click',null,bs.getPhraseSrchDetails);
				$('#biblioListDiv .gobkBtn').bind('click',null,bs.rtnToSrch);
		  	$('#searchDiv').hide();
        $('#biblioListDiv').show()
	    }

		});
		return false;
	},
	getPhraseSrchDetails: function () {
	  var bibid = $(this).prev().val();
		bs.biblio.bibid = bibid;
		$('#biblioDiv .gobkBtn').bind('click',null,bs.rtnToList);
		bs.showOneBiblio(bs.biblio[bibid]);
		bs.fetchCopyInfo();
	},
	showOneBiblio: function (biblio) {
	  var txt = '';
		$.each(biblio.data, function(fldIndex,fldData) {
		  var tmp = eval('('+fldData+')');
		  txt += "<tr>\n";
			txt += "	<td>"+tmp.label+"</td>\n";
			txt += "	<td>"+tmp.value+"</td>\n";
			txt += "</tr>\n";
		});
		txt += "<tr>\n";
		txt += "	<td>Date Added</td>\n";
		txt += "	<td>"+biblio.createDt+"</td>\n";
		txt += "</tr>\n";
  	$('tbody#biblio').html(txt);
		obib.reStripe();
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
					html += "	<td>"+crntCopy.status_cd+"</td>\n";
					html += "	<td>"+crntCopy.status_begin_dt+"</td>\n";
					html += "	<td>"+bs.makeDueDateStr(crntCopy.status_begin_dt)+"</td>\n";
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
				obib.reStripe();
				$('.editBtn').bind('click',null,bs.doCopyEdit);
				$('.deltBtn').bind('click',null,bs.doCopyDelete);
	  });
	},
	makeNewCopy: function () {
		$('#biblioDiv').hide();
		if ($('#autobarco:checked').length > 0) {
			bs.doGetBarcdNmbr();
		}
		$('#copyEditorDiv').show();
		$('#editSubmitBtn').bind('click',null,bs.doCopyNew);
	},
	doCopyDelete: function () {
	  $(this).parent().parent().addClass('hilite');
	  if (confirm('Are you sure you want to delete this copy ?')) {
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
		$('#editSubmitBtn').val('Update');
		$('#editSubmitBtn').bind('click',null,bs.doCopyUpdate);
		$('#biblioDiv').hide();
		$('#copyEditorDiv').show();
		return false;
	},
	rtnFmCopyEdit: function () {
		$('#copyEditorDiv').hide();
		$('#biblioDiv').show();
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
	    $('#editCancelBtn').val('Go Back');
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
	}
};
$(document).ready(bs.init);

</script>
