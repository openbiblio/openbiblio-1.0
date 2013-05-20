<script language="JavaScript" >
//------------------------------------------------------------------------------
// itemDisplay Javascript
"use strict";

var idis = {
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
					idis.fetchCopyInfo();
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

  	idis.crntFoto = null;
  	idis.crntBibid = idis.theBiblio.bibid;
		$('#photoEditBtn').hide();		
		$('#photoAddBtn').hide();		
		$('#bibBlkB').html('');
		if (idis.opts.show_item_photos == 'Y') {
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
				//console.log('title==>>'+idis.crntTitle);				
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
	  $.getJSON(idis.url,{'mode':'getCopyInfo','bibid':idis.biblio.bibid}, function(jsonInpt){
				idis.copyJSON = jsonInpt;
				if (!idis.copyJSON) {
					var msg = '(<?php echo T("No copies"); ?>)';
					$('tbody#copies').html('<tr><td colspan="9" class="hilite">'+msg+'</td></tr>');
					return false; // no copies found
				}
				
				var html = '';
				for (var nCopy in idis.copyJSON) {
				  var crntCopy = eval('('+idis.copyJSON[nCopy]+')')
				  html += "<tr>\n";
					if (!window.opacMode) {
						html += '	<td>\n';
						html += '		<button class="editBtn" value="<?php echo T("edit"); ?>" />\n';
						html += '		<button class="deltBtn" value="<?php echo T("del"); ?>" />\n';
						html += '		<input type="hidden" value="'+crntCopy.copyid+'">\n';
						html += '	</td>\n';
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
						var text = 'href="../circ/mbr_view.php?mbrid='+crntCopy.mbrId+'"';
					  html += ' to <a '+text+'>'+crntCopy.mbrName+'</a>';
					}
					html += "	</td>\n";
					html += "	<td>"+idis.makeDueDateStr(crntCopy.last_change_dt)+"</td>\n";
					// Due back is onyl needed when checkked out - LJ
					if(crntCopy.statusCd == "ln" || crntCopy.statusCd == "out"){
						// Sometimes the info has to come out of an array (if coming from list) - LJ
						var daysDueBack = parseInt(idis.biblio.daysDueBack);
						if(isNaN(daysDueBack)) {			
							daysDueBack = parseInt(idis.biblio[idis.biblio.bibid].daysDueBack);
						}					
						html += "	<td>"+idis.makeDueDateStr(crntCopy.last_change_dt,daysDueBack)+"</td>\n";
					} else {
						html += "<td>---</td>";
					}
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
				obib.reStripe2('copyList','odd');

				// dynamically created buttons
				$('.editBtn').on('click',null,idis.doCopyEdit);
				$('.deltBtn').on('click',{'copyid':crntCopy.copyid},idis.doCopyDelete);
	  });
	},

};
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
