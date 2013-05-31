<script language="JavaScript" >
//------------------------------------------------------------------------------
// itemDisplay Javascript
"use strict";
<?php
	// If a circulation user and NOT a cataloging user the system should treat the user as opac
//	if(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))
	if(strtolower($tab) == 'opac' || strtolower($tab) == 'circulation' )
	  echo "var opacMode = true;";
	else
	  echo "var opacMode = false;";
?>

var idis = {
	<?php
		echo "showMarc: '".T("Show Marc Tags")."',\n";
		echo "hideMarc: '".T("Hide Marc Tags")."',\n";
	?>
	multiMode: false,

	init: function (opts) {
		idis.opts = opts;
		idis.url = '../catalog/catalogServer.php';
	},
	
	doBibidSearch: function (bibid) {
	  idis.srchType = 'bibid';
	  $('p.error').html('').hide();
	  var params = '&mode=doBibidSearch&bibid='+bibid;
	  $.post(idis.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				idis.biblio = $.parseJSON(jsonInpt);
				if (!idis.biblio.data) {
	  			$('#rsltMsg').html('<?php echo T("NothingFoundByBarcdSearch") ?>').show();
				}
				else {
					idis.showOneBiblio(idis.biblio)
				}
	    }
		  $('#searchDiv').hide();
	    $('#biblioDiv').show();
		});
		return false;
	},

	showOneBiblio: function (biblio) {
	  if(!biblio)
			idis.theBiblio = $(this).prev().val();
		else
	  	idis.theBiblio = biblio;
		if (typeof bs !== 'undefined') bs.theBiblio = idis.theBiblio;
		$('#theBibId').html(idis.theBiblio.bibid);

  	idis.crntFoto = null;
  	idis.crntBibid = idis.theBiblio.bibid;
		$('#photoEditBtn').hide();		
		$('#photoAddBtn').hide();		
		$('#bibBlkB').html('');

		if (idis.opts.showBiblioPhotos == 'Y') {
  		$.getJSON(idis.url,{ 'mode':'getPhoto', 'bibid':idis.theBiblio.bibid  }, function(data){
  			if (data == null) {
  				idis.crntFoto = data;
					$('#photoAddBtn').show();
					$('#bibBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" >');
  			} else {
  				idis.crntFoto = data[0];
					$('#photoEditBtn').show();
					var fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+idis.crntFoto.url;
					$('#bibBlkB').html($('<img src="'+fotoFile+'" id="biblioFoto" class="hover" >'));
				}
  		});
		}
		idis.fetchCopyInfo();

	  var txt = '';
		$.each(idis.theBiblio.data, function(fldIndex,fldData) {
		  var tmp = JSON.parse(fldData);
		  txt += "<tr>\n";
			txt += "	<td class=\"filterable hilite\">"+tmp.marcTag+"</td>\n";
			txt += "	<td>"+tmp.label+"</td>\n";
			txt += "	<td>"+tmp.value+"</td>\n";
			txt += "</tr>\n";
			if (tmp.marcTag == '245a') {
				idis.crntTitle = tmp.value;
			}
		});
		txt += "<tr>\n";
		txt += "	<td class=\"filterable hilite\">&nbsp</td>\n";
		txt += "	<td>Date Added</td>\n";
		txt += "	<td>"+idis.theBiblio.createDt+"</td>\n";
		txt += "</tr>\n";
  	$('tbody#biblio').html(txt);
		obib.reStripe2('biblioTbl','odd');
		$('#biblioDiv td.filterable').hide();
		$('#marcBtn').val(idis.showMarc);

		if (!idis.lookupAvailable)$('#onlnUpdtBtn').hide();
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
	  $.getJSON(idis.url,{'mode':'getCopyInfo','bibid':idis.theBiblio.bibid}, function(jsonInpt){
				idis.copyJSON = jsonInpt;
				if (!idis.copyJSON) {
					var msg = '(<?php echo T("No copies"); ?>)';
					$('tbody#copies').html('<tr><td colspan="9" class="hilite">'+msg+'</td></tr>');
					return false; // no copies found
				}
				
				var html = '';
				for (var nCopy in idis.copyJSON) {
				  idis.crntCopy = eval('('+idis.copyJSON[nCopy]+')')
				  html += "<tr>\n";
					if (!window.opacMode) {
						html += '	<td>\n';
						html += '		<input type="button" class="button editBtn" value="<?php echo T("edit"); ?>" />\n';
						html += '		<input type="button" class="button deltBtn" value="<?php echo T("del"); ?>" />\n';
						html += '		<input type="hidden" value="'+idis.crntCopy.copyid+'">\n';
						html += '	</td>\n';
					}

					if (idis.crntCopy.site) {
						html += "	<td>"+idis.crntCopy.site+"</td>\n";
					}
					else {
						$('#siteFld').hide();
					}

					html += "	<td>"+idis.crntCopy.barcode_nmbr+"</td>\n";

					html += "	<td>"+idis.crntCopy.status
					if (idis.crntCopy.mbrId) {
						var text = 'href="../circ/mbr_view.php?mbrid='+idis.crntCopy.mbrId+'"';
					  html += ' to <a '+text+'>'+idis.crntCopy.mbrName+'</a>';
					}
					html += "	</td>\n";

					html += "	<td>"+idis.makeDueDateStr(idis.crntCopy.last_change_dt)+"</td>\n";

					// Due back is onyl needed when checkked out - LJ
					if(idis.crntCopy.statusCd == "ln" || idis.crntCopy.statusCd == "out"){
						// Sometimes the info has to come out of an array (if coming from list) - LJ
						var daysDueBack = parseInt(idis.theBiblio.daysDueBack);
						if(isNaN(daysDueBack)) {			
							daysDueBack = parseInt(idis.theBiblio[idis.theBiblio.bibid].daysDueBack);
						}					
						html += "	<td>"+idis.makeDueDateStr(idis.crntCopy.last_change_dt,daysDueBack)+"</td>\n";
					} else {
						html += "<td>---</td>";
					}

					html += "	<td>"+idis.crntCopy.copy_desc+"</td>\n";
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
				obib.reStripe2('copyList','odd');

				// dynamically created buttons
				$('.editBtn').on('click',null,idis.doCopyEdit);
				$('.deltBtn').on('click',{'copyid':idis.crntCopy.copyid},idis.doCopyDelete);
	  });
	},

	doCopyNew: function () {
		$('#copyForm #bibid').val(idis.theBiblio.bibid);
		$('#copyForm #mode').val('newCopy');
		var params= $('#copyForm').serialize()+"&bibid="+idis.theBiblio.bibid+"&mode=newCopy";
		if ($('#autobarco:checked').length > 0) {
			params += "&barcode_nmbr="+$('#copyTbl #barcode_nmbr').val();
		}

		// post to DB
		idis.doPostCopy2DB(params);
	},

	doCopyEdit: function (e) {
		$('#editRsltMsg').html('');
		var copyid = $(this).next().next().val();
		for (var nCopy in idis.copyJSON) {
			idis.crntCopy = eval('('+idis.copyJSON[nCopy]+')')
		  if (idis.crntCopy['copyid'] == copyid) break;
		}
		$('#copyTbl #barcode_nmbr').val(idis.crntCopy.barcode_nmbr);
		$('#copyTbl #copy_desc').val(idis.crntCopy.copy_desc);
		$('#copyTbl #copy_site').val([idis.crntCopy.site]);
		$('#copyTbl #status_cd').val(idis.crntCopy.statusCd);
		$('#copyEditorDiv fieldset legend').html("<?php echo T("Edit Copy Properties"); ?>");

  	var crntsite = idis.opts.current_site
		$('#copy_site').val(crntsite);

		// custom fields
		for(var nField in idis.crntCopy.custFields){
			$('#copyTbl #custom_'+idis.crntCopy.custFields[nField].code).val(idis.crntCopy.custFields[nField].data);
		}

		// unbind & bind needed here because of button reuse elsewhere
		$('#copySubmitBtn').unbind('click');
		$('#copySubmitBtn').on('click',null,function () {
			idis.doCopyUpdate();
			// Moved to function
			//bs.rtnToBiblio();
			return false;
		});

		// Set 'update' button to enabled in case it wasn't from a previous edit
		$('#copySubmitBtn').enable();

		$('#biblioDiv').hide();
		$('#copyEditorDiv').show();
	  // prevent submit button from firing a 'submit' action
		return false;
	},
	doCopyUpdate: function () {
	  var barcdNmbr = $('#copyTbl #barcode_nmbr').val();

	  // serialize() ignores disabled fields, so cant reliably use in this case
	  var copyDesc = $('#copyTbl #copy_desc').val();
	  var statusCd = $('#copyTbl #status_cd').val();
	  var siteid = $('#copyTbl #copy_site').val();
		var params = "&mode=updateCopy&bibid="+idis.theBiblio.bibid+"&copyid="+idis.crntCopy.copyid
					 		 + "&barcode_nmbr="+barcdNmbr+"&copy_desc="+copyDesc
					 		 + "&status_cd="+statusCd+"&siteid="+siteid;

		// Custom fields
		for(var nField in idis.crntCopy.custFields){
			// Only add if has a value, or changed from a value to nothing
			if($('#copyTbl #custom_'+idis.crntCopy.custFields[nField].code).val() != idis.crntCopy.custFields[nField].data ||  $('#copyTbl #custom_'+idis.crntCopy.custFields[nField].code).val() != ""){
				params = params + '&custom_'+idis.crntCopy.custFields[nField].code+'='+$('#copyTbl #custom_'+idis.crntCopy.custFields[nField].code).val();
			}
		}
		// post to DB
		idis.doPostCopy2DB(params);
	},
	doPostCopy2DB: function (parms) {
		//console.log('parms='+parms);
	  $.post(idis.url,parms, function(response){
	  	if(response == '!!success!!') {
				idis.fetchCopyInfo(); // refresh copy display
				$('#editCancelBtn').val("Go Back");
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
	  if (confirm('<?php echo T("Are you sure you want to delete this copy?"); ?>')) {
	  	//var copyid = e.data.copyid;
			var copyid = $(this).next().val();
	    var params = "&mode=deleteCopy&bibid="+idis.theBiblio.bibid+"&copyid="+copyid;
	  	$.post(idis.url,params, function(response){
	  	  $('#rsltMsg').html(response);
	  		idis.fetchCopyInfo(); // refresh copy display
	  	});
		};
	  $(this).parent().parent().removeClass('hilite');
		return false;
	}

};
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
