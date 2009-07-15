<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

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
		$('.gobkBtn').bind('click',null,bs.rtnToSrch);

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

		$('#editSubmitBtn').val('Update');
		$('#editSubmitBtn').bind('click',null,bs.doCopyUpdate);

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
					bs.showOneBiblio(bs.biblio.data)
					bs.fetchCopyInfo();
				}
	    }
		});
		return false;
	},
	doPhraseSearch: function (e) {
	  $('#errSpace').html('');
	  var params = $('#phraseSearch').serialize();
		params += '&mode=doPhraseSearch';
	  $.post(bs.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				bs.biblioList = eval('('+jsonInpt+')'); // JSON 'interpreter'
				if (bs.biblioList.length == 0) {
	  			$('#rsltQuan').html('<p>Nothing Found</p>');
				}
				else {
					$('#rsltQuan').html(bs.biblioList.length+' items found')
				  for (var nBiblio in bs.biblioList) {
				    var html = '<fieldset>';
						var biblio = eval('('+bs.biblioList[nBiblio]+')');
				    var imgFile = biblio.image_file;
				    var data = biblio.data;
console.log('data:'+data);
						var callNo = ''; var title = '';
						$.each(data, function (fldIndex, fldData) {
console.log('item:'+fldData);
		  				var tmp = eval('('+fldData+')');
				      if (tmp.label == 'Title') 			title = tmp.value;
				      if (tmp.label == 'Call Number') callNo = tmp.value;
						});
						html+='<img src="../images/'+imgFile+'" />';
						html+=callNo+'<br />';
						html+=title+'<br />';
				    html +='</fieldset>';
				    $('#srchRsltsDiv').append(html);
					}
				}
		  	$('#searchDiv').hide();
        $('#biblioListDiv').show()
	    }
		});
		return false;
	},
	showOneBiblio: function (data) {
	  var txt = '';
		$.each(data, function(fldIndex,fldData) {
		  var tmp = eval('('+fldData+')');
		  txt += "<tr>\n";
			txt += "	<td>"+tmp.label+"</td>\n";
			txt += "	<td>"+tmp.value+"</td>\n";
			txt += "</tr>\n";
		});
		txt += "<tr>\n";
		txt += "	<td>Date Added</td>\n";
		txt += "	<td>"+bs.biblio.createDt+"</td>\n";
		txt += "</tr>\n";
  	$('tbody#biblio').html(txt);
		obib.reStripe();
	  $('#searchDiv').hide();
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
		$('#biblioDiv').hide();
		$('#copyEditorDiv').show();
		return false;
	},
	rtnFmCopyEdit: function () {
		$('#copyEditorDiv').hide();
		$('#biblioDiv').show();
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
	    $('#editCancelBtn').val('Go Back');
	  });
	  bs.fetchCopyInfo(); // refresh copy display
	  return false;
	}
};
$(document).ready(bs.init);

</script>
