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

		var showFoto = '<?php echo Settings::get('show_item_photos'); ?>';
		if ((showFoto == 'Y') && (Modernizr.video)){
  		$.getJSON(idis.url,{ 'mode':'getPhoto', 'bibid':idis.theBiblio.bibid  }, function(data){
				var fotoHt = <?php echo Settings::get('thumbnail_height'); ?>;
				var fotoWid = <?php echo Settings::get('thumbnail_width'); ?>;

  			if (data == null) {
  				idis.crntFoto = data;
					$('#photoAddBtn').show();
					$('#bibBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" '
      			+ 'height="'+fotoHt+'" width="'+fotoWid+'" >');
  			} else {
  				idis.crntFoto = data[0];
					$('#photoEditBtn').show();
					var fotoFile = '<?php echo OBIB_UPLOAD_DIR; ?>'+idis.crntFoto.url;
					$('#bibBlkB').html('<img src="'+fotoFile+'" id="biblioFoto" class="hover" '
      			+ 'height="'+fotoHt+'" width="'+fotoWid+'" >');
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
			if (tmp.marcTag == '024a') {
				txt += '	<td><a class="hotDoi" href="http://dx.doi.org/'+escape(tmp.value)+'">'+tmp.value+'</td>\n';
			} else if (tmp.marcTag == '505a') {
				txt += '	<td><textarea wrap="soft" readonly cols="50" >'+tmp.value+"</textarea></td>\n";
			} else {
				txt += '	<td><input type="text" readonly size="50" maxlength="256" value="'+tmp.value+'" \></td>\n';
			}
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

		/* support for doi search link */
		$('.hotDoi').on('click',null,function () {
			var Qr = $(this).val();
			if(Qr){
				if(Qr.indexOf('doi://')==0)Qr=Qr.substr(6);
				if(Qr.indexOf('doi:')==0)Qr=Qr.substr(4)

				//example doi: 10.1007/s10531-011-0143-8
				var	newLoc = 'http://dx.doi.org/'+escape(Qr);
				window.open(newLoc,'doiWin');
				return false;
			}
		});

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
				$('.deltBtn').on('click',{'copyid':idis.crntCopy.copyid},ced.doCopyDelete);
	  });
	},

	doCopyEdit: function (e) {
  	e.stopPropagation();
		$('#biblioDiv').hide();
  	var crntsite = idis.opts.current_site
		$('#copy_site').val(crntsite);

		$('#copyEditorDiv').show();
		ced.doCopyEdit(e);
		e.preventDefault();
	},

};
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
