<script language="JavaScript" >
//------------------------------------------------------------------------------
// Javascript documant - itemDisplayJs.php

"use strict";
<?php
	// If a circulation user and NOT a cataloging user the system should treat the user as opac
//	if(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))
	$tab = strtolower($tab);
	if ($tab == 'opac' || $tab == 'circulation' )
	  echo "var opacMode = true;";
	else
	  echo "var opacMode = false;";

    if ($_SESSION['multi_site_func'] > 0)
      echo "var multiSite = true;";
    else
      echo "var multiSite = false;";

?>

var idis = {
	<?php
		echo "showMarc: '".T("Show Marc Tags")."',\n";
		echo "hideMarc: '".T("Hide Marc Tags")."',\n";
	?>
	multiMode: false,

	init: function (opts, sites) {
		idis.opts = opts;
		idis.sites = sites;
		idis.url = '../catalog/catalogServer.php';
	},
	
	/* ====================================== */
	doBibidSearch: function (bibid) {
	  idis.srchType = 'bibid';
	  $('p.error').html('').hide();
	  var params = '&mode=doBibidSearch&bibid='+bibid;
	  $.post(idis.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				idis.biblio = $.parseJSON(jsonInpt);
				if (!idis.biblio.hdr) {
	  			$('#rsltMsg').html('<?php echo T("NothingFoundByBibidSearch") ?>').show();
				}
				else {
					idis.showOneBiblio(idis.biblio);
				}
	    }
		  $('#searchDiv').hide();
	    $('#biblioDiv').show();
		});
		return false;
	},

	/* ====================================== */
	showOneBiblio: function (biblio) {
	  if(!biblio)
			idis.theBiblio = $(this).prev().val();
		else
	  	idis.theBiblio = biblio;
		if (typeof bs !== 'undefined') bs.theBiblio = idis.theBiblio;
		idis.bibid = idis.theBiblio.hdr.bibid;
		$('#theBibId').html(idis.bibid);

  	     idis.crntFoto = null;
  	     idis.crntBibid = idis.bibid;
		$('#photoEditBtn').hide();		
		$('#photoAddBtn').hide();		
		$('#bibBlkB').html('');

		var showFoto = '<?php echo Settings::get('show_item_photos'); ?>';
		if (showFoto == 'Y'){
			<?php if ($tab == 'cataloging') { ?>
				if ((Modernizr.video) && (typeof(wc)) !== 'undefined') {
					if (wc.video === undefined) wc.init();
				}
			<?php } ?>

  		    $.post(idis.url,{ 'mode':'getPhoto', 'bibid':idis.bibid  }, function(data){
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
  		    }, 'json');
		}

	    var txt = '';
		$.each(idis.theBiblio.marc, function(key,value) {
		  var tmp = value;
			tmp.marcTag = key.substr(0,5);
			if (tmp.line && tmp.value) {
		  	txt += "<tr>\n";
				txt += '	<td class="filterable hilite">'+tmp.marcTag+"</td>\n";
				txt += "	<td>"+tmp.lbl+"</td>\n";
				if (tmp.marcTag == '024$a') {
					txt += '	<td><a class="hotDoi" href="http://dx.doi.org/'+escape(tmp.value)+'">'+tmp.value+'</a></td>\n';
				} else if (tmp.marcTag == '505$a') {
					txt += '	<td><textarea wrap="soft" readonly cols="50" >'+tmp.value+"</textarea></td>\n";
				} else if (tmp.marcTag == '856$u') {
					txt += '	<td><a href="'+tmp.value+'">'+tmp.value+'</a></td>\n';
				} else {
					txt += '	<td><p>'+tmp.value+'</p></td>\n';
				}
				txt += "</tr>\n";
				if (tmp.marcTag == '245$a') {
					idis.crntTitle = tmp.value;
				}
			}
		});
		txt += "<tr>\n";
		txt += "	<td class=\"filterable hilite\">&nbsp</td>\n";
		txt += "	<td>Date Added</td>\n";
		txt += "	<td>"+idis.theBiblio.hdr.createDt+"</td>\n";
		txt += "</tr>\n";

		idis.fetchCopyInfo();

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

	doItemDelete: function (biblio) {
        //console.log(biblio);
        idis.bibid = biblio.hdr.bibid;
		if (bs.copyJSON) {
			alert('You must delete all copies before you can delete an item!');
		}
		else {
    	  	if (confirm('<?php echo T("Are you sure you want to delete this item?"); ?>: #'+idis.bibid)) {
    	    	var params = "&mode=deleteBiblio&bibid="+idis.bibid;
    	  		$.post(idis.url,params, function(response){
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
	  $('tbody#copies').html('<tr><td colspan="9">'+
                             '  <p class="error">'+
                             '    <img src="../images/please_wait.gif" width="26" />'+
                             '    <?php echo T("Searching"); ?>'+
                             '  </p>'+
                             '</td></tr>'
                            );
	  $.post(idis.url,
            {'mode':'getCopyInfo',
             'bibid':idis.bibid,
            },
            function(jsonInpt){
				idis.copys = jsonInpt;
				if (!idis.copys) {
					var msg = '(<?php echo T("No copies"); ?>)';
					$('tbody#copies').html('<tr><td colspan="9" class="hilite">'+msg+'</td></tr>');
					return false; // no copies found
				}

				var html = '';
				for (var n in idis.copys) {
					var copy = idis.copys[n];
				    idis.crntCopy = copy;
				    html += "<tr>\n";
    				<?php if (!($tab == 'opac' || $tab == 'working' || $tab == 'user' || $tab == 'rpt' || $tab == 'circulation' )){ ?>
    						html += '	<td>\n';
    						html += '		<input type="button" id="edit-'+copy.copyid+'" class="button editBtn" value="<?php echo T("edit"); ?>" />\n';
    						html += '		<input type="button" id="delt-'+copy.copyid+'" class="button deltBtn" value="<?php echo T("del"); ?>" />\n';
    						html += '		<input type="hidden" value="'+copy.copyid+'" />\n';
    						html += '	</td>\n';
    				<?php } ?>
					if ((copy.siteid) && (multiSite == true)) {
						html += "	<td>"+idis.sites[copy.siteid]+"</td>\n";
					} else {
						$('#siteFld').hide();
					}

					html += "	<td>"+copy.barcode+"</td>\n";

					html += "	<td>"+copy.status
					if (copy.ckoutMbr) {
						var text = 'href="../circ/mbr_view.php?mbrid='+copy.ckoutMbr+'"';
					  html += ' to <a '+text+'>'+copy.mbrName+'</a>';
					}
					html += "	</td>\n";

					if (copy.status == 'out') {
						html += "	<td>"+copy.out_dt+"</td>\n";
						html += "	<td>"+copy.due_dt+"</td>\n";
					} else {
                        var dt_parts = [];
                        if (copy.status_dt) {
						  dt_parts = copy.status_dt.split(' ');
                        } else {
                          dt_parts[0] = '';
                        }
						html += "<td>"+dt_parts[0]+"</td>";
						html += "<td>- - - - - - - -</td>";
					}

					html += "	<td>"+copy.desc+"</td>\n";
					html += "</tr>\n";
				}
  			$('tbody#copies').html(html);
			obib.reStripe2('copyList','odd');

			// dynamically created buttons
			$('.editBtn').on('click',null,idis.doCopyEdit);
			//$('.deltBtn').on('click',{'copyid':copy.copyid},idis.doCopyDelete);
			$('.deltBtn').on('click',{'copyid':copy.copyid},ced.doCopyDelete);
	    }, 'json');
	},

	doCopyEdit: function (e) {
  	e.stopPropagation();
		$('#biblioDiv').hide();
  	var crntsite = idis.opts.current_site
		$('#copy_site').val(crntsite);

		ced.bibid = idis.bibid;
		ced.doCopyEdit(e);
		$('#copyEditorDiv').show();
		e.preventDefault();
	},
};
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
