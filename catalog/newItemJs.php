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
		$colTypes = new Collections;
		echo "dfltColl: '".$colTypes->getDefault()."',\n";
		$medTypes = new MediaTypes;
		echo "dfltMedia: '".$medTypes->getDefault()."',\n";
	?>

	init: function () {
		// get header stuff going first
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
			//ni.doCopyNew();
			ni.doNewCopy(e);
			return false;
		});
		$('#copyCancelBtn').on('click',null,function () {
			return ni.doBackToSrch();
		});
		$('#barcode_nmbr').on('change',null,ni.chkBarcdForDupe);
		if ($('#autobarco:checked').length > 0) {
			$('#barcode_nmbr').disable();
		}
		// if user changes his/her mind
		$('#autobarco').on('change',null,function (){
		  if ($('#autobarco:checked').length > 0) {
				$('#barcode_nmbr').disable();
			}
			else {
				$('#barcode_nmbr').enable();
			}
		});

		$('#100a').on('change',null,ni.fixAuthor);
		$('#245a').on('change',null,ni.fixTitle);

		ni.fetchHosts(); // for searches
		ni.fetchOpts(); // starts chain to call the following
		//ni.fetchSiteList(); // for new copy use
		//ni.fetchMaterialList(); // for new items
		//ni.fetchCollectionList(); // for new items
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
		$('#msgDiv').hide();
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
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(ni.url,{mode:'getOpts'}, function(data){
			ni.opts = data;
			ni.fetchMaterialList(); // chaining
		});
	},
	fetchMaterialList: function () {
	  <?php // get default material type
			$matTypes = new MediaTypes;
			echo "var dfltMedia = ".$matTypes->getDefault().";";
	  ?>
	  $.getJSON(ni.listSrvr,{mode:'getMediaList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#srchMatTypes').html(html);
			$('#itemMediaTypes').html(html);
			$('#materialCd').on('change',null,function () {
				ni.doMakeItemForm($('#materialCd').val());
			});
			ni.fetchCollectionList(); // chaining
		});
	},
	fetchCollectionList: function () {
	  $.getJSON(ni.listSrvr,{mode:'getCollectionList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#itemEditColls').html(html);
			ni.fetchSiteList(); // chaining
		});
	},
	fetchSiteList: function () {
	  $.getJSON(ni.listSrvr,{mode:'getSiteList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#copySite').html(html);
		});
	},

	fetchHosts: function () {
		//console.log('svr:'+ni.url);
	  $.getJSON(ni.url,{mode:'getHosts'}, function(data){
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
		});
	},

	doAbandon: function () {
	  $.getJSON(ni.url,{mode:'abandon'}, function(data){
			$('#searchDiv').show();
		});
	},

	//------------------------------------------------------------------------------------------
	// manual 'new biblio' related stuff
	doInsertNew: function () {
	 	var parms=$('#newBiblioForm').serialize();
		parms += '&mode=doInsertBiblio';
	  $.post(ni.url,parms, function(response){
	  	if (response.substr(1) == '<') {
				$('#msgDiv').html(response).show();
			}
			else {
	    	var rslt = $.parseJSON(response);
	    	ni.bibid = rslt.bibid;
	  		ni.showCopyEditor(ni.bibid);
	  	}
		});
		return false;
	},
	
	//------------------------------------------------------------------------------------------
	// copy-editor support
	showCopyEditor: function (e) {
  	//e.stopPropagation();
  	$('#selectionDiv').hide();
  	var crntsite = ni.opts.session.current_site
		$('#copyBibid').val(ni.bibid);
		$('#copySite').val(crntsite);
		$('#copyEditorDiv').show();
		ced.doCopyNew(e);
		//e.preventDefault();
		$('#copyCancelBtn').on('click',null, function () {
			ni.doPhotoAdd();
		});
	},

	//------------------------------------------------------------------------------------------
	// photo-editor support
	doPhotoAdd: function () {
		$('#copyEditorDiv').hide();
		$('#fotoHdr').val('<?php echo T("AddingNewFoto"); ?>')
    $('#fotoMsg').hide();
		$('#fotoEdLegend').html('<?php echo T("EnterNewPhotoInfo"); ?>');

		$('#updtFotoBtn').hide();
		$('#deltFotoBtn').hide();
		$('#addFotoBtn').show();
		$('.gobkFotoBtn').on('click',null, function () {
			ni.doBackToSrch();
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
	chkIsbn: function (isbn) {
		// validate isbn string; return TRUE if checksum is valid
		var nSum = 0;
		var sSum = '';
		var nAdr = 0;
		var rslt = true;
		var msg = '';
		if (isbn.length < 10) {
			msg = "<br />(length is "+isbn.length+"; Not enough digits for isbn)";
			rslt = false;
		}
		else if (isbn.substr(0,3) == "978") {
			// this is a bar code reader input
			if (isNaN(parseInt(isbn.substr(9,1))) ) {
				msg = "(Bar-Code ISBN Entry does not start with a digit)";
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
		$('#errMsgTxt').html('');
		var msg = '';
		var nType = $('#srchBy').val();
	  var val = $('#lookupVal').val();
	  var rslt = true;
	  var test = val.replace(/-| /g, '');
	  switch (parseInt(nType)) {
	  case 4: // Text input
	  	if (!isNaN(parseInt(test))) {
				rslt = false;
				msg += "This appears to be either a ISBN, ISSN, or LCCN,<br />but you have selected 'Title'.<br />";
			}
			break;
		case 7: //ISBN
	   	if ((isNaN(parseInt(test))) || (!ni.chkIsbn(test))) {
				rslt = false;
				msg += "This is not a valid ISBN.<br />";
			}
			break;
		 case 8: // ISSN
	   	if (isNaN(parseInt(test))) {
				rslt = false;
				msg += "This is not a valid ISSN.<br />";
			}
			break;
		case 9: // LCCN
	   	if (isNaN(parseInt(test))) {
				rslt = false;
				msg += "This is not a valid LCCN.<br />";
			}
			break;
		}

		if (rslt) {
		  ni.doSearch();
		}
		else {
			$('#srchBy').focus();
			$('#errMsgTxt').html(msg);
			return rslt;
		}
	},
	doSearch: function () {
	  var srchBy = flos.getSelectBox($('#srchBy'),'getText');
	  var lookupVal = $('#lookupVal').val();

	  // advise user that this will take some time to complete
	  var srchBy2 = flos.getSelectBox($('#srchBy2'),'getText');
	  var theTxt = '<h5>';
		theTxt += "Looking for "+srchBy+" '" + lookupVal + "'<br />";
	  if ($('#lookupVal2').val() != '') {
			theTxt += "&nbsp;&nbsp;&nbsp;with "+srchBy2+" '"+$('#lookupVal2').val()+"'<br />";
		}

		// show host(s) being searched
		theTxt += 'at :<br />';
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

		theTxt += '</h5>';
	  $('#waitText').html(theTxt);
	  
		$('#searchDiv').hide();
		$('#waitDiv').show();
		
		// note for this to work, all form fields MUST have a 'name' attribute
		$('lookupForm #mode').val('search');
		var srchParms = $('#lookupForm').serialize();
		$.post(ni.url, srchParms, ni.handleSrchRslts);
	},
	handleSrchRslts: function (response) {
			$('#waitDiv').hide();
			
			if ($.trim(response).substr(0,1) != '{') {
				$('#retryHead').empty();
				$('#retryHead').html(ni.searchError);
				$('#retryMsg').empty();
				$('#retryMsg').html(response);
				$('#retryDiv').show();
			}
			else {
				var rslts = $.parseJSON(response);
				var numHits = parseInt(rslts.ttlHits);
				var maxHits = parseInt(rslts.maxHits);
				if (numHits < 1) {
					//console.log('nothing found');
				  //{'ttlHits':$ttlHits,'maxHits':$postVars[maxHits],
					// 'msg':".$lookLoc->getText('lookup_NothingFound'),
					// 'srch1':['byName':$srchByName,'val':$lookupVal],
					// 'srch2':['byName':$srchByName2,'val':$lookupVal2]}
					var srch1 = $.parseJSON(rslts['srch1']), srch2 = $.parseJSON(rslts['srch2']);
					var str = rslts.msg+':<br />&nbsp;&nbsp;for '+srch1["byName"]+' = '+srch1["lookupVal"];
					if ((srch2['lookupVal']) && (srch2['lookupVal'] != ''))
						str += '<br />&nbsp;&nbsp;&nbsp;'+srch2['byName']+' = '+srch2['lookupVal'];
					$('#retryHead').empty();
					$('#retryHead').html('<?php echo T("Nothing Found"); ?>');
					$('#retryMsg').empty();
					$('#retryMsg').html(str);
					$('#retryDiv').show();
				}
			
				else if (numHits >= maxHits) {
					//console.log('too many hits');
		  		//{'ttlHits':'$ttlHits','maxHits':'$postVars[maxHits]',
					// 'msg':'$msg1', 'msg2':'$msg2'}
					var str = rslts.msg+' ('+rslts.ttlHits+' ).<br />'+rslts.msg2;
					$('#retryHead').empty();
					$('#retryHead').html(rslts.msg);
					$('#retryMsg').empty();
					$('#retryMsg').html(str);
					$('#retryDiv').show();
				}
			
				else if (numHits > 1){
					//console.log('more than one hit');
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
							    
							    html += '<form class="hitForm"><table border="0">';
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
					//console.log('single hit found');
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
	  $.get(ni.url,{'mode':'getBiblioFields', 'material_cd':mediaType}, function (response) {
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
				$('#'+tag).val(data[tag]);
				$('#'+tag).css('color',ni.inputColor);
			}
		}
		if (data != null){
			ni.setCallNmbr(data);
			ni.setCollection(data);
			$('#itemMediaTypes').val(media)
		} else {
			$('#itemEditColls').val(ni.dfltColl);
			$('#itemMediaTypes').val(ni.dfltMedia);
		}

	  $('#selectionDiv input.online').disable();	/**/
	  $('itemSubmitBtn').enable();
		$('#choiceDiv').hide();
		$('#selectionDiv').show();
	},
	
	setCallNmbr: function (data) {
		switch (ni.opts['callNmbrType'].toLowerCase())  {
		case 'loc':
			$('#099a').val(data['050a']+' '+data['050b']);
			break;
		case 'dew':
		  var callNmbr = ni.makeCallNmbr(data['082a']);
    		var cutter = ni.makeCutter(data['100a'], data['245a']);
				$('#099a').val(callNmbr+cutter);
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
		//console.log('code='+code)
		if ((code) && ((ni.opts['callNmbrType']).toLowerCase() == "dew")) {
			var fictionDew = ni.opts['fictionDew'].split(' ');
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
