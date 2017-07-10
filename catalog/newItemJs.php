<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
   
<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
"use strict";

var ni = {
	<?php
		echo "empty: '".T("Nothing Found")."',\n";
	?>

	init: function () {
		// get header stuff going first
		//console.log("in newItemJs, ni.init():");
		ni.initWidgets();

		ni.url = '../catalog/onlineServer.php';
		ni.form = $('#lookupForm');
		ni.bs_url = '../catalog/catalogServer.php';
		ni.listSrvr = "../shared/listSrvr.php";

		ni.srchBtn = $('#srchBtn');
		ni.resetForm();

		// New On-Line Entry Item - search form functions
        $('.criteria').on('change',null, function () {
			ni.enableSrchBtn();
		});
        $('#manualBtn').on('click',null, function() {
			ni.doClearItemForm();
			ni.doMakeItemForm('');
			$('#searchDiv').hide();
			$('#selectionDiv').show();
		});
		$('#itemMediaTypes').on('change',null,function () {
	  	var mediaType = $('#itemMediaTypes option:selected').val();
			ni.doClearItemForm();
			ni.doMakeItemForm(mediaType);
        });
    
		$('#quitBtn').on('click',null,ni.doAbandon);
		$('#retryBtn').on('click',null,ni.doBackToSrch);
		$('#choiceBtn1').on('click',null,ni.doBackToSrch);
		$('#choiceBtn2').on('click',null,ni.doBackToSrch);
		$('#lookupForm').on('submit',null,function(e){
			e.preventDefault();
			e.stopPropagation();
			ni.doValidate_n_Srch();
			return;
		});

		// New Manual Entry Item
		// modify original biblioFields form to better suit our needs
		$('#biblioDiv .itemGobkBtn').on('click',null,ni.doBackToSrch);
		$('#newBiblioForm').on('submit',null,function(e){
			e.preventDefault();
			e.stopPropagation();
			ni.doInsertNew();
			return false;
		});

		// for the copy editor functions
		// to handle startup condition
		$('#copyForm').on('submit',null,function (e) {
			e.preventDefault();
			e.stopPropagation();
			ni.doNewCopy(e);
			return false;
		});
		$('#barcode_nmbr').on('change',null,ni.chkBarcdForDupe);
			if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').on('change',null,function (){
		    if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
			} else {
				$('#barcode_nmbr').enable();
			}
		});

		$('#100a').on('change',null,ni.fixAuthor);
		$('#245a').on('change',null,ni.fixTitle);

		ni.fetchOpts(); // starts chain to call the following
		//ni.fetchSiteList(); // for new copy use
		//ni.fetchMaterialList(); // for new items
		//ni.fetchCollectionList(); // for new items
		//ni.fetchHosts(); // for searches

		$('#lookupVal').focus();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	
	resetForm: function () {
	    //console.log('resetting Search Form');
		$('#help').hide();
		$('#searchDiv').show();
		$('#srchHosts').hide();// depends if multiple hosts available
		$('#errMsgTxt').html(' ');
		$('#waitDiv').hide();
		$('#retryDiv').hide();
		obib.hideMsg('now');
		$('#choiceDiv').hide();
		$('#selectionDiv').hide();
		$('#copyEditorDiv').hide();
		$('#photoEditorDiv').hide();
		ni.disableSrchBtn();
        $('span#ttlHits').html('');
		$('#lookupVal').focus();
	},
	
	disableSrchBtn: function () {
		ni.srchBtn.disable();
	},
	enableSrchBtn: function () {
		ni.srchBtn.enable();
	},

	doBackToSrch: function (e) {
		if(e) {
			e.stopPropagation();
			e.preventDefault();
		}
		ni.resetForm();
	},
	
	doBackToChoice: function () {
		var nmbr = $('span#ttlHits').html();
		if ((nmbr == '')||(nmbr == 0 )) {
			ni.doBackToSrch();
		} else {
			$('#selectionDiv').hide();
			$('#choiceDiv').show();
		}
	},

	doBackToShow: function () {
		$('#copyEditorDiv').hide();
		$('#photoEditorDiv').hide();
		$('#searchDiv').hide();
		$('#selectionDiv').show();
	},

	//------------------------------
	fetchOpts: function () {
		// Do not try to get from ListSrvr;
		// that will provide a small subset of what is needed here.
		//console.log('fetching Opts');
        $.post(ni.url,{mode:'getOpts'}, function(data){
			ni.postVars = data;
			ni.opts = ni.postVars['opts'][0];
			ni.numHosts = ni.postVars['numHosts'];
			ni.hosts = ni.postVars['hosts'];
			ni.session = ni.postVars['session'];
			ni.fetchDfltMedia(); // chaining
		}, 'json');
  	},
    fetchDfltMedia: function() {
		//console.log('fetching dfltMedia');
        $.post(ni.listSrvr,{mode:'getDefaultMaterial'}, function(data){
            ni.dfltMedia = data;
			ni.fetchMaterialList(); // chaining
        }, 'json');
    },
	fetchMaterialList: function () {
		//console.log('fetching matlList');
        $.post(ni.listSrvr,{mode:'getMediaList'}, function(data){
			var html = '';
            for (var n in data) {
				html+= '<option value="'+n+'" ';
                if (n == ni.dfltMedia) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[n]+'</option>';
			}
			$('#srchMatTypes').html(html);
			$('#itemMediaTypes').html(html);
			$('#materialCd').on('change',null,function () {
				ni.doMakeItemForm($('#materialCd').val());
			});
			ni.fetchDfltCollection(); // chaining
		}, 'json');
	},
    fetchDfltCollection: function() {
		//console.log('fetching dfltColl');
        $.post(ni.listSrvr,{mode:'getDefaultCollection'}, function(data){
            ni.dfltColl = data;
			ni.fetchCollectionList(); // chaining
        }, 'json');
    },
	fetchCollectionList: function () {
		//console.log('fetching collList');
	    $.post(ni.listSrvr,{mode:'getCollectionList'}, function(data){
			var html = '';
            for (var n in data) {
				html+= '<option value="'+n+'" ';
                if (n == ni.dfltColl) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[n]+'</option>';
			}
			$('#itemEditColls').html(html);
			ni.fetchSiteList(); // chaining
		}, 'json');
	},
	fetchSiteList: function () {
		//console.log('fetching siteList');
        var listHtml = list.getSiteList($('#copySite'));
		$('#copySite').html(listHtml);
		ni.fetchHosts(); // chaining
	},

	fetchHosts: function () {
		//console.log('fetching hostList');
	    $.post(ni.url,{mode:'getHosts'}, function(data){
	  		// return includes all ACTIVE marked hosts
			ni.hostJSON = data;
			ni.nHosts = data.length;
			if (ni.nHosts > 1) {
				var theTxt = '';
				for (var nHost in data) {
					theTxt += '<label><input type="checkbox" name="srchHost" id="hst'+nHost+'" checked value="'+data[nHost].id+'"\>'+data[nHost].name+'</label><br />\n';
				}
				$('#srchHosts span').html(theTxt);
				$('#srchHosts').show();

			} else {
				$('#srchHosts').hide();
			}
			$('#waitDiv').hide();
			$('#searchDiv').show();
		}, 'json');
	},

	doAbandon: function () {
	  $.post(ni.url,{mode:'abandon'}, function(data){
			$('#searchDiv').show();
		}, 'json');
	},

	//------------------------------------------------------------------------------------------
	// manual 'new biblio' related stuff
	doInsertNew: function () {
	 	var parms=$('#newBiblioForm').serialize();
		parms += '&mode=doInsertBiblio';
	    $.post(ni.url,parms, function(response){
    	  	if (typeof response == 'object') {
				var rslt = response;
				ni.bibid = rslt['bibid'];
				//console.log('posted #'+ni.bibid)
				obib.showMsg('new biblio #'+ni.bibid+' posted');
				ni.showCopyEditor(ni.bibid);
    		}
    		else {
				//console.log(response);
				obib.showMsg(response);
				$('#content').scrollTop(0);
			}
    	}, 'JSON');
	    return false;
	},
	
	//------------------------------------------------------------------------------------------
	// copy-editor support
	showCopyEditor: function (bibid) {
		//console.log('in newItemJs.php::showCopyEditor()');
      	$('#selectionDiv').hide();
      	var crntsite = ni.postVars.session.current_site
		$('#copyBibid').val(bibid);
		$('#copySite').val(crntsite);
		$('#copyEditorDiv').show();
		//ced.bibid = bibid;

		$('#copySubmitBtn').on('click',null, function () {
			ni.doPhotoAdd();
		});
		//$('#copyCancelBtn').on('click',null, function () {
		//	$('#copyEditorDiv').hide();
      	//	$('#selectionDiv').show();
		//});
		$('#copyCancelBtn').on('click',null,function () {
			ni.doBackToShow();
		});

		//e.preventDefault();
		ced.doCopyNew(bibid);

		/* prepare in advance for photo editing */
		if ((Modernizr.video) && (typeof(wc)) !== 'undefined') {
//			if (wc.video === undefined) wc.init();
			wc.init();
		}

	},

	//------------------------------------------------------------------------------------------
	// photo-editor support
	doPhotoAdd: function () {
		//console.log('in newItemJs.php::doPhotoAdd()');
		$('#copyEditorDiv').hide();
		$('#fotoHdr').val('<?php echo T("AddingNewFoto"); ?>')
		$('#fotoEdLegend').html('<?php echo T("EnterNewPhotoInfo"); ?>');
		$('#fotoBibid').val(ni.bibid);

		$('#updtFotoBtn').hide();
		$('#deltFotoBtn').hide();
		$('#addFotoBtn').show();
		$('.gobkFotoBtn').on('click',null, function () {
			wc.vidOff();    // disables camera
			ni.doBackToShow();
		});

		$('#fotoMode').val('addNewPhoto')
		$('#fotoSrce').attr({'required':true, 'aria-required':true});
	    $('#fotoSrce').val('')
	    $('#fotoBibid').val(ni.bibid);
		wc.eraseImage();
  	    $('#fotoName').val(ni.bibid+'.jpg');
        $('#searchDiv').hide();
		$('#photoEditorDiv').show();
	},

	//------------------------------------------------------------------------------------------
	// on-line search related stuff
	chkIsbn: function (isbnRaw) {
		//console.log('raw isbn input: '+isbnRaw);
		// validate isbn string; return TRUE if checksum is valid
		var nSum = 0,
			sSum = '',
			nAdr = 0,
			rslt = true,
			msg = '';
		let isbn = isbnRaw.replace(/-/, "");    // remove all '-'
		//console.log('clean isbn input: '+isbn);

		if (isbn.length < 10) {
			msg = "<br />(length is "+isbn.length+"; Not enough digits for isbn)";
			rslt = false;
		}
		else if (isbn.substr(0,3) == "978") {
			//console.log('found a isbn-13 entry');
			if (isNaN(parseInt(isbn.substr(9,1))) ) {
				msg = "(ISBN-13 Entry does not start with a digit)";
				rslt = false;
			}
		}
		else {	// test check digit
			for (var i=0; i<9; i++) {
				nAdr = isbn.substr(i,1);
				nSum += (10-i) * nAdr;
			}
			nSum = nSum % 11;
			nSum = 11 - nSum;
			if (nSum == 10)
				sSum = "X";
			else if (nSum == 11)
				sSum = "0";
			else
				sSum = nSum.toString();
			//console.log("\nisbn chk digit="+isbn.substr(9,1)+"\ncomputed ="+sSum);
			if (sSum != isbn.substr(9,1)) {
				msg = "<br />(ISBN checksum fails)";
				rslt = false;
			}
		}
		if (!rslt) {
			$('#srchBy').focus();
			$('#errMsgTxt').html(msg+"<br />Correct it and try again.");
		}
		return rslt;
	},
	
	doValidate_n_Srch: function () {
		var isbnPattern = /ISBN(-1(?:(0)|3))?:?\x20(\s)*[0-9]+[- ][0-9]+[- ][0-9]+[- ][0-9]*[- ]*[xX0-9]/;
		var issnPattern = /[0-9]{4}-[0-9]{4}/;
		var lccnPattern = /((a(c|fl?|gr)?|b[irs]|c(a?d?|lc|[sxy])|do?|es?|f(i[ae]?)?|g[ms]?|h(a|e[wx]?)?|in?t|j[ax]?|kx?|l(lh|tf)?|m([ams]|ap|ed|i[cdef]|pa?|us)?|n(cn|ex?|[tu]c)|or|p([aop]|h[opq])|r[aceu]?|s(ax?|[cdfgnsu])?|t(b|mp)|u(m|nk)|w(ar)?|[xz])(\b|-)?|20)?\d\d(-\d{1,5}|(\b|-)?\d{6})/;

		$('#errMsgTxt').html('');
		var msg = '';
		var nType = $('#srchBy').val();
	  	var val = $.trim($('#lookupVal').val());
	  	var rslt = true;
	  	switch (parseInt(nType)) {
	  	case 4: // Text input
			break;
		case 7: //ISBN
			if ( isbnPattern.test('ISBN: '+val) ) {
				console.log('isbn passes regExp');
			} else {
				console.log('isbn fails regExp');
				rslt = false;
				msg += "This is not a valid ISBN.<br />";
			}
	  		let test = val.replace(/-| /g, '');
        	$('#lookupVal').val(test); // update display with cleaned up ISBN
			break;
		case 8: // ISSN
	   		if ( issnPattern.test(val) ) {
				rslt = false;
				msg += "This is not a valid ISSN.<br />";
			}
			break;
		case 9: // LCCN
			/*
			On Wikidata, an optional space or hyphen may appear before the year.
			An optional hyphen may appear before the serial number;
			if the serial number is typed as less than 6 digits, the hyphen is required.
			The preferred and best formatting now is to remove all hyphens and spaces
			and insure that the serial is always 6 digits, as shown in the right column
			of http://lccn.loc.gov/#n9. (English)
			*/
			// currently under test only
			if ( lccnPattern.test(val) ) {
				console.log('lccn '+val+' passes regExp');
			} else {
				console.log('lccn '+val+' fails regExp');
				msg += "This is not a valid LCCN.<br />";
				return;
			}
			if (val.indexOf('-') >= 0) {
				var parts = val.split('-');
				val = doFixLccn(parts[0], parts[1])
			} else if (val.length < 8) {
                // prior to year 2000
				let partA = val.substr(0,2)
				let partB = val.substr(2)
				val = doFixLccn(partA, partB)
			}
			$('#lookupVal').val(val); // update display with cleaned up LCCN
			break;
		}

		if (rslt) {
		  	ni.doSearch();
		} else {
			$('#srchBy').focus();
			$('#errMsgTxt').html(msg);
			obib.showMsg(msg);
			return rslt;
		}
	},
	doFixLccn: function (part0, part1) {
		var temp = '00000'+part1;
		parts1 = temp.substr(-6,6);
		val = part0+part1;    // wikidata stated preference
		return val
	},
	doSearch: function () {
        var srchBy = flos.getSelectBox($('#srchBy'),'getText');
        var srchBy2 = flos.getSelectBox($('#srchBy2'),'getText');
        var srchBy3 = flos.getSelectBox($('#srchBy3'),'getText');
        var srchBy4 = flos.getSelectBox($('#srchBy4'),'getText');
		let val1 = $('#lookupVal').val()
		let val2 = $('#lookupVal2').val()
		let val3 = $('#lookupVal3').val()
		let val4 = $('#lookupVal4').val()

        var theTxt = '<h5>';
        theTxt += "Searching for :<br />&nbsp;&nbsp;&nbsp;"+srchBy+" '" + val1 + "'<br /><br />";
        if (val2 != '') {
        	theTxt += "&nbsp;&nbsp;&nbsp;with "+srchBy2+" '"+val2+"'<br /><br />";
		}
        if (val3 != '') {
        	theTxt += "&nbsp;&nbsp;&nbsp;with "+srchBy3+" '"+val3+"'<br /><br />";
		}
        if (val4 != '') {
        	theTxt += "&nbsp;&nbsp;&nbsp;with "+srchBy4+" '"+val4+"'<br /><br />";
		}

		// show host(s) being searched
		theTxt += 'at :<br />';
		//console.log('n hosts = '+ni.nHosts);
		if (ni.nHosts > 1){
			var n=1;
			$('#srchHosts :checkbox:checked').each(function () {
				//console.log('using host '+this.id.substr(3,10));			
				theTxt += '&nbsp;&nbsp;&nbsp;'+n+'. '+ni.hostJSON[this.id.substr(3,10)].name+'<br />';
				n++;
			});
		} else {
			theTxt += '&nbsp;&nbsp;&nbsp;'+ni.hostJSON[0].name+'<br />';
		}

		theTxt += '</h5><br />';
	    $('#waitText').html(theTxt);
	  
		$('#searchDiv').hide();
		$('#waitDiv').show();
		
		// note for this to work, all form fields MUST have a 'name' attribute
		$('lookupForm #mode').val('search');
		var srchParms = $('#lookupForm').serialize();
		$.post(ni.url, srchParms, ni.handleSrchRslts, 'json');
	},
	handleSrchRslts: function (response) {
			$('#waitDiv').hide();
			//console.log(typeof response);
			//console.log(response);

			if (typeof response != 'object') {
				console.log('unexpected response');
				$('#retryHead').empty();
				$('#retryHead').html(ni.searchError);
				$('#retryMsg').empty();
				$('#retryMsg').html(response);
				$('#retryDiv').show();
			}
			else {
				var rslts = response;
				if (typeof rslts.ttlHits == 'undefined') {
					numHits = rslts[0];
				} else {
					var numHits = parseInt(rslts.ttlHits);
					//console.log('ttlHits = '+numHits);
				}

                var maxHits = ni.opts.maxHits;
				//console.log('maxHits = '+maxHits);
				if (numHits < 1) {
                    console.log('nothing found');
				    //{'ttlHits':$ttlHits,'maxHits':$postVars[maxHits],
					// 'msg':".$lookLoc->getText('lookup_NothingFound'),
					// 'srch1':['byName':$srchByName,'val':$lookupVal],
					// 'srch2':['byName':$srchByName2,'val':$lookupVal2]}
					var srch1 = JSON.parse(rslts['srch1']),
						srch2 = JSON.parse(rslts['srch2']);
					var str = rslts.msg+':<br />&nbsp;&nbsp;for '+srch1["byName"]+' = '+srch1["lookupVal"];
					if ((srch2['lookupVal']) && (srch2['lookupVal'] != ''))
						str += '<br />&nbsp;&nbsp;&nbsp;'+srch2['byName']+' = '+srch2['lookupVal'];
//str = rslts[1]+'<br />'+rslts[3];
					$('#retryHead').empty();
					$('#retryHead').html('<?php echo T("Nothing Found"); ?>');
					$('#retryMsg').empty();
					$('#retryMsg').html(str);
					$('#retryDiv').show();
				}
			
				else if (numHits >= maxHits) {
                    console.log('too many hits: '+numHits+' > '+maxHits);
		  		    //{'ttlHits':'$ttlHits','maxHits':'$postVars[maxHits]',
					// 'msg':'$msg1', 'msg2':'$msg2'}
					var str = rslts.msg+' ('+rslts.ttlHits+' )<br />'+rslts.msg2;
					$('#retryHead').empty();
					$('#retryHead').html(rslts.msg);
					$('#retryMsg').empty();
					$('#retryMsg').html(str);
					$('#retryDiv').show();
				}
			
				else if (numHits > 1){
                    //console.log('more than one hit: '+numHits);
					$('#choiceSpace').empty();
					$('#ttlHits').html(numHits);
					ni.singleHit = false;

					var nHits = 0, html;
					ni.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  if (typeof(hostData) != undefined) {
						  $('#choiceSpace').append('<h4>Repository: '+ni.hostJSON[hostIndex].name+'</h4>');
						  if (!hostData) {
							  $('#choiceSpace').append('<fieldset>' + ni.empty + '</fieldset>');
							}
							else {
							  $.each(hostData, function(hitIndex,hitData) {
							    nHits++;
							    html  = '<fieldset>';
						if (hitData['err']) {
							html += hitData['err'];
						} else {
							    
							    html += '<form role="form" class="hitForm"><table border="0">';
							    html += '<tr><td>LCCN</th><td>'+hitData['010a']+'</td></tr>';
							    html += '<tr><td>ISBN</th><td>'+hitData['020a']+'</td></tr>';
							    html += '<tr><td>Title</th><td>'+hitData['245a']+'</td></tr>';
							    html += '<tr><td>Author</th><td>'+hitData['100a']+'</td></tr>';
							    html += '<tr><td>Publisher</th><td>'+hitData['260b']+'</td></tr>';
							    html += '<tr><td>Location</th><td>'+hitData['260a']+'</td></tr>';
							    html += '<tr><td>Date</th><td>'+hitData['260c']+'</td>';
									var id = 'host'+hostIndex+'-hit'+hitIndex;
							    html += '<td id="'+id+'"><input type="button" value="<?php echo T("This One"); ?>" /></td></tr>';
									html += '</table></form>';
						}
									html += '</fieldset>';
									$('#choiceSpace').append(html);
									$('#'+id).on('click',{host:hostIndex,hit:hitIndex,data:hitData},ni.doSelectOne);
								}); // $.each(hostData...
							}
						} // if (ni.hostJason[hostIndex])
					}); // $.each(rslts.data...

					//console.log('all choices drawn')
					$('#biblioBtn').on('click',null,ni.doBackToChoice);
					$('#biblioBtn2').on('click',null,ni.doBackToChoice);
					$('#choiceDiv').show();
				} // else if (rslts.ttlHits > 1)
				
				else if (rslts.ttlHits == 1){
				    var data;
				    ni.singleHit = true;
                    console.log('single hit found');
					ni.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  $.each(hostData, function(hitIndex,hitData) {
					  	data = hitData;
					  });
					});
					ni.crntData = data;
					ni.doClearItemForm();
					ni.doMakeItemForm();
				}
			} // else
	},
	
	doSelectOne: function (e) {
	  var host = e.data.host;
	  var hit = e.data.hit;
	  var data = e.data.data;
		ni.crntData = data;
		ni.doClearItemForm();
		ni.doMakeItemForm();
	},

	doStriping: function () {
		$('#biblioFldTbl').each(function() {
			var $table = $(this);
				$table.find('tbody#marcBody tr:not(:hidden):even').addClass('altBG');
		});
	},

	doClearItemForm: function () {
		// assure all marc fields are empty & visible at start
		$('#newBiblioForm').each(function(){
	        this.reset();
		});
	},
	
	doMakeItemForm: function (mediaType) {
	    // fill out empty form with MARC fields
	    if ((mediaType == '') || (mediaType === undefined)) mediaType = ni.dfltMedia;
	    $.post(ni.url,{'mode':'getBiblioFields', 'material_cd':mediaType}, function (response) {
			$('#marcBody').html(response);
			$('#selectionDiv td.filterable').hide();
			obib.reStripe2('biblioFldTbl','odd');
			$('#opacFlg').val(['CHECKED','Y']);

			ni.doShowOne(ni.crntData,mediaType);
		});
		$('.itemGobkBtn').on('click',null,ni.doBackToChoice);
	},
	
	doShowOne: function (data, media){
	    // display biblio item data in form
	    $('#searchDiv').hide();
		for (var tag in data) {
			if (data[tag] != '') {
				//console.log(tag+': '+data[tag]);
				$('#'+tag).val(data[tag]);
				$('#'+tag).css('color',ni.inputColor);
			}
		}
		if (data != null){
			ni.setCallNmbr(data);
			ni.setCollection(data);
		} else {
			$('#itemEditColls').val(ni.dfltColl);
		}
		$('#itemMediaTypes').val(media);

	    $('#selectionDiv input.online').disable();	/**/
	    $('itemSubmitBtn').enable();
		$('#choiceDiv').hide();
		$('#selectionDiv').show();
	},
	
	setCallNmbr: function (data) {
		switch (ni.opts['callNmbrType'].toLowerCase()) {
			case 'loc':
				$('#099a').val(data['050a'] + ' ' + data['050b']);
				break;
			case 'dew':
				var callNmbr = ni.makeCallNmbr(data['082a']);
				var cutter = ni.makeCutter(data['100a'], data['245a']);
				// LJ: Not sure what cutter is, so ignore if undefined.
				if (typeof(cutter) === 'undefined') {
					$('#099a').val(callNmbr);
				} else {
					$('#099a').val(callNmbr + cutter);
				}
			break;
		case 'udc':
		  var callNmbr = ni.makeCallNmbr(data['080a']);
			$('#099a').val(callNmbr+' '+data['080b']);
			break;
		case 'local':
			// leave the fields blank for user entry
			break;
		default:
		  break;
		}
		if ($('#099a').val() != '') {
			$('#099a').css('color',ni.inputColor);
			$('#099a').parent().parent().show().removeClass('hidden');
		}
	},

	makeCallNmbr: function (code) {
		if ((code) && (ni.opts.callNmbrType.toLowerCase() == "dew")) {
			var fictionDew = ni.opts.fictionDew.split(' ');
			if (ni.opts['autoDewey']
					&&
					((code == "") || (code == "[Fic]"))
					&&
					(fictionDew.indexOf(code) >= 0)
				 ) {
				dew = ni.opts['defaultDewey'];
			}

			var parts = code.split('.');
			var base1 = parts[0];
			var callNmbr = base1;
			if (parts[1]) {
				var base2 = parts[1].split('/');
				callNmbr += '.'+base2;
			}
			callNmbr = callNmbr.replace('/', '');
			//console.log('callNmbr()='+callNmbr)
			return callNmbr;
		}
	},
	
	fixTitle: function () {
	  var titl = $('#245a').val();
		if (titl != '') {
    	$('#245a').css('color',ni.inputColor);
    	var auth = $('#100a').val();
	  	ni.makeCutter(auth, titl); // will post direct to screen
		}
		else {
    	$('#245a').css('color','red');
		}
	},
	
	fixAuthor: function () {
	  var auth = $('#100a').val();
		if (auth != '') {
    	$('#100a').css('color',ni.inputColor);
    	var titl = $('#245a').val();
	  	ni.makeCutter(auth, titl);
		}
		else {
    	$('#100a').css('color','red');
		}
	},

	makeCutter: function (auth,titl) {
		//console.log('auth=<'+auth+'>; titl=<'+titl+'>');
	  if (ni.opts['autoCutter'] == 'n') return; // not wanted
	  
	  var cutter = '';
	  auth = auth.trim(); titl = titl.trim();
		if ((auth != '') && (auth != 'undefined')) {
	  	$.getJSON(ni.url,{mode:'getCutter', author:auth}, function(data){
				//console.log('data='+data)
				cutter = data['cutter'];
	  		if (ni.opts['cutterType'] == 'CS3') {
			  	// suffix is first char of a specified word in title
					cutter += ni.makeSuffix($('#245a').val());
				}
				else if (ni.opts['cutterType'] == 'LoC') {
					// add copyright year as suffix -- FIXME numeric year only!!!
					//var cpyYr = $.trim($('#260c').val()).replace('.','');
					//cutter += ' '+cpyYr.substr(1,4);
					cutter += ' '+$('#260c').val();
				}
				$('#099a').val($('#099a').val()+' '+cutter);
			});
		}
		else if ((auth == '') || (auth == 'undefined')) {
		  cutter = 'no Author found'
		}
		return cutter;
	},

	makeSuffix: function (s) {
		inputWords = (s.toLowerCase()).split(' ');

		var nWords = 0;
		var goodWords = '';
		for (var index in inputWords) {
			if ((inputWords[index] != ' ') &&
					((ni.opts['noiseWords']).indexOf(inputWords[index]) < 0)) {
				goodWords+=' '+inputWords[index];
				nWords++;
			}
		}
		goodWords = $.trim(goodWords);
		wordArray = goodWords.split(/\s+/);
		useWordNo = ni.opts['cutterWord'];
		if (nWords == 1)
			var sufx = (wordArray[0]).substr(0,1);
		else if (nWords <= useWordNo)
			sufx = (wordArray[nWords-1]).substr(0,1);
		else if (nWords > useWordNo)
			sufx = (wordArray[useWordNo-1]).substr(0,1);

		return sufx.toLowerCase();
	},

	setCollection: function (data) {
		//// -- attempt to determine proper collection from LOC call number
		//				this is experimental and may not be to your taste
		if (ni.opts['autoCollect'] == true || ni.opts['autoCollect'] == 'y') {
			var index = ni.opts['fictionCode'];
			var collection = ni.opts['defaultCollect'];

			if ((data['050a']) && (ni.opts['callNmbrType'] == 'LoC')) {
				var locClass = (data['050a']).substr(0,2);
				if ((ni.opts['fictionLoc']).indexOf(locClass) >= 0) {
					collection = $.trim(ni.opts['fictionName']);
				}
			}
			else if ((data['082a']) && (ni.opts['callNmbrType'] == 'Dew')) {
				var dewClass = (data['082a']).substr(0,3);
				if ((ni.opts['fictionDew']).indexOf(dewClass) >= 0) {
					collection = $.trim(ni.opts['fictionName']);
				}
			}
			$('#collectionCd').val($("#collectionCd option:contains('" + collection + "')").val());
		}
	}
};

$(document).ready(ni.init);

</script>
