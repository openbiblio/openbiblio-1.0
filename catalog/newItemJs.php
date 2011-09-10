<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
   
<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
ni = {
	<?php
	?>
	init: function () {
		// get header stuff going first
		ni.initWidgets();

		ni.url = '../catalog/onlineServer.php';
		ni.form = $('#lookupForm');
		ni.bs_url = '../catalog/catalogServer.php';
		
		ni.srchBtn = $('#srchBtn');
		ni.resetForm();

		// New On-Line Entry Item - search form functions
    $('.criteria').bind('change',null,ni.enableSrchBtn);
    $('#manualBtn').bind('click',null, function() {
			ni.doClearItemForm();
			ni.doMakeItemForm('');
			$('#searchDiv').hide();
			$('#selectionDiv').show();
		});
    
		$('#quitBtn').bind('click',null,ni.doAbandon);
		$('#retryBtn').bind('click',null,ni.doBackToSrch);
		$('#choiceBtn1').bind('click',null,ni.doBackToSrch);
		$('#choiceBtn2').bind('click',null,ni.doBackToSrch);
		$('#lookupForm').bind('submit',null,function(e){
			e.preventDefault();
			e.stopPropagation();
			ni.doValidate_n_Srch();
			return;
		});

		// New Manual Entry Item
		// modify original biblioFields form to better suit our needs
		$('#biblioDiv .itemGobkBtn').bind('click',null,ni.doBackToSrch);
		$('#newBiblioForm').bind('submit',null,function(e){
			e.preventDefault();
			e.stopPropagation();
			ni.doInsertNew();
			return false;
		});

		// for the copy editor functions
		// to handle startup condition
		$('#copyForm').bind('submit',null,function (e) {
			e.preventDefault();
			e.stopPropagation();
			ni.doCopyNew();
			return false;
		});
		$('#copyCancelBtn').bind('click',null,function () {
			return ni.doBackToSrch();
		});
		$('#barcode_nmbr').bind('change',null,ni.chkBarcdForDupe);
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

		// FIXME - fl only '*' should be colored
		//$('#selectionDiv font').css('color','red');
		//$('#selectionDiv sup').css('color','red');
		//ni.inputColor = $('#99').css('color');
		$('#100a').bind('change',null,ni.fixAuthor);
		$('#245a').bind('change',null,ni.fixTitle);

		ni.fetchHosts();  //on completion, search form will appear
		ni.fetchMaterialList(); // for new items
		ni.fetchCollectionList(); // for new items
		ni.fetchSiteList(); // for new copy use
		ni.fetchOpts();  //for debug use
		//ni.doMakeItemForm('');
	},
	
	//------------------------------
	initWidgets: function () {
//		if(ie) ie.init();
	},
	
	resetForm: function () {
	  //console.log('resetting Search Form');
		$('#help').hide();
		$('#searchDiv').show();
		$('#errMsgTxt').html(' ');
		$('#waitDiv').hide();
		$('#retryDiv').hide();
		$('#msgDiv').hide();
		$('#choiceDiv').hide();
		$('#selectionDiv').hide();
		$('#copyEditorDiv').hide();

		$('#lookupVal').focus();
		ni.disableSrchBtn();
	},
	
	disableSrchBtn: function () {
	  ni.srchBtnBgClr = ni.srchBtn.css('color');
	  ni.srchBtn.css('color', '#888888');
		ni.srchBtn.disable();
	},
	enableSrchBtn: function () {
	  ni.srchBtn.css('color', ni.srchBtnBgClr);
		ni.srchBtn.enable();
	},

	doBackToSrch: function () {
		ni.resetForm();
	},
	
	doBackToChoice: function () {
		if (ni.singleHit) {
			doBackToSrch();
		} else {
			$('#selectionDiv').hide();
			$('#choiceDiv').show();
		}
	},
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(ni.url,{mode:'getOpts'}, function(data){
			ni.opts = data;
		});
	},

	fetchHosts: function () {
		//console.log('svr:'+ni.url);	
	  $.getJSON(ni.url,{mode:'getHosts'}, function(data){
			ni.hostJSON = data;
			$('#waitDiv').hide();
			$('#searchDiv').show();
		});
	},

	fetchMaterialList: function () {
	  <?php // Set default material type
			$matTypes = new MediaTypes;
			$material_cd_value = $matTypes->getDefault();
	  ?>
	  $.get(ni.bs_url,{mode:'getMaterialList', selectedMt:'<?php echo $material_cd_value ?>'}, function(data){
			$('#srchMatTypes').html(data);
			$('#itemMediaTypes').html(data);
			$('#materialCd').bind('change',null,function () {
				ni.doMakeItemForm($('#materialCd').val());
			});
		});
	},
	
	fetchCollectionList: function () {
	  <?php // Set default collection type
			$colTypes = new Collections;
			$col_cd_value = $colTypes->getDefault();
	  ?>
	  $.get(ni.bs_url,{mode:'getCollectionList', selectedCt:'<?php echo $col_cd_value ?>'}, function(data){
			$('#itemEditColls').html(data);
		});
	},
	
	fetchSiteList: function () {
	  $.get(ni.bs_url,{mode:'getSiteList'}, function(data){
			$('#copy_site').html(data);
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
	  		ni.showCopyEditor();
	  	}
		});
		return false;
	},
	
	chkBarcdForDupe: function () {
		var barcd = $.trim($('#barcode_nmbr').val());
		barcd = flos.pad(barcd,<?php echo $defBarcodeDigits; ?>,'0');
		$('#barcode_nmbr').val(barcd);
	  $.get(ni.bs_url,{'mode':'chkBarcdForDupe','barcode_nmbr':barcd}, function (response) {
	  	$('#editRsltMsg').html(response).show();
		})
	},

	showCopyEditor: function () {
  	$('#selectionDiv').hide();
  	var crntsite = ni.opts.session.current_site
		//console.log('crnt site='+crntsite);
		$('#copyTbl #copy_site').val(crntsite);
		if ($('#autobarco:checked').length > 0) {
			ni.getNewBarcd(ni.bibid);
		}
		$('#copyEditorDiv').show();
	},

	doCopyNew: function () {
		var params= $('#copyForm').serialize()+ "&mode=newCopy&bibid="+ni.bibid;
		if ($('#autobarco:checked').length > 0) {
			params += "&barcode_nmbr="+$('#copyTbl #barcode_nmbr').val();
		}
	  $.post(ni.bs_url,params, function(response){
			if(response == '!!success!!') {
				ni.doBackToSrch();
			} else {
				$('#editRsltMsg').html(response).show();
			}
	  });
	  return false;
	},

	getNewBarcd: function () {
		$.getJSON(ni.bs_url,{'mode':'getNewBarcd'}, function(jsonInpt){
		  $('#copyTbl #barcode_nmbr').val(jsonInpt.barcdNmbr);
		});
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
		var nType = $('#srchBy').val();
	  var val = $('#lookupVal').val();
	  var rslt = true;
	  var test = val.replace(/-| /g, '');
	  switch (parseInt(nType)) {
	  case 4: // Text input
	  	if (!isNaN(parseInt(test))) {
				rslt = false;
				msg = "This appears to be either a ISBN, ISSN, or LCCN,<br />but you have selected 'Title'.";
			}
			break;
		case 7: //ISBN
	   	if ((isNaN(parseInt(test))) || (!ni.chkIsbn(test))) {
				rslt = false;
				msg = "This is not a valid ISBN.";
			}
			break;
		 case 8: // ISSN
	   	if (isNaN(parseInt(test))) {
				rslt = false;
				msg = "This is not a valid ISSN.";
			}
			break;
		case 9: // LCCN
	   	if (isNaN(parseInt(test))) {
				rslt = false;
				msg = "This is not a valid LCCN.";
			}
			break;
		}

		if (rslt) {
		  ni.doSearch();
		}
		else {
			$('#srchBy').focus();
			$('#errMsgTxt').prepend(msg);
			return rslt;
		}
	},
	doSearch: function () {
	  var srchBy = flos.getSelectBox($('#srchBy'),'getText');
	  var lookupVal = $('#lookupVal').val();

	  // advise user that this takes time to complete
	  var srchBy2 = flos.getSelectBox($('#srchBy2'),'getText');
	  var theTxt = '<h5>';
		theTxt += "Looking for "+srchBy+" '" + lookupVal + "'<br />";
	  if ($('#lookupVal2').val() != '')
			theTxt += "&nbsp;&nbsp;&nbsp;with "+srchBy2+" '"+$('#lookupVal2').val()+"'<br />";
		theTxt += 'at :<br />';
		var n=1;
		for (nHost in ni.hostJSON) {
			theTxt += '&nbsp;&nbsp;&nbsp;'+n+'. '+ni.hostJSON[nHost].name+'<br />';
			n++;
		}
		theTxt += '</h5>';
	  $('#waitText').html(theTxt);
	  
		$('#searchDiv').hide();
		$('#waitDiv').show();
		
		// note for this to work, all form fields MUST have a 'name' attribute
		$('lookupForm #mode').val('search');
		var srchParms = $('#lookupForm').serialize();
		$.post(ni.url, srchParms, function(response) {
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
					var str = rslts.msg+':<br />&nbsp;&nbsp;&nbsp;'+rslts.srch1.byName+' = '+rslts.srch1.lookupVal;
					if (rslts.srch2.lookupVal != '')
						str += '<br />&nbsp;&nbsp;&nbsp;'+rslts.srch2.byName+' = '+rslts.srch2.lookupVal;
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
					ni.singleHit = false;

					var nHits = 0;
					ni.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  if (typeof(hostData) != undefined) {
					  $('#choiceSpace').append('<h4>Repository: '+ni.hostJSON[hostIndex].name+'</h4>');
					  $.each(hostData, function(hitIndex,hitData) {
					    nHits++;
					    html  = '<fieldset>';
					    html += '<form class="hitForm"><table border="0">';
					    html += '<tr><td>LCCN</th><td>'+hitData['010a']+'</td></tr>';
					    html += '<tr><td>ISBN</th><td>'+hitData['020a']+'</td></tr>';
					    html += '<tr><td>Title</th><td>'+hitData['245a']+'</td></tr>';
					    html += '<tr><td>Author</th><td>'+hitData['100a']+'</td></tr>';
					    html += '<tr><td>Publisher</th><td>'+hitData['260b']+'</td></tr>';
					    html += '<tr><td>Location</th><td>'+hitData['260a']+'</td></tr>';
					    html += '<tr><td>Date</th><td>'+hitData['260c']+'</td>';
							var id = 'host'+hostIndex+'-hit'+hitIndex;
					    html += '<td id="'+id+'"><input type="button" value="This One" /></td></tr>';
							html += '</table></form></fieldset>';
							$('#choiceSpace').append(html);
							$('#'+id).bind('click',{host:hostIndex,hit:hitIndex,data:hitData},ni.doSelectOne);
						}); // $.each(hostData...
						} // if (ni.hostJason[hostIndex])
					}); // $.each(rslts.data...

					$('#ttlHits').html(numHits);
					//console.log('all choices drawn')
					$('#biblioBtn').bind('click',null,ni.doBackToChoice);
					$('#biblioBtn2').bind('click',null,ni.doBackToChoice);
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
		}); // .post
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

	doMakeItemForm: function (mediaType) {
	  // fill out empty form with MARC fields
	  $.get(ni.url,{'mode':'getBiblioFields', 'material_cd':mediaType}, function (response) {
			$('#marcBody').html(response);
			$('#selectionDiv td.filterable').hide();
			obib.reStripe2('biblioFldTbl','odd');
			$('#opacFlg').val(['CHECKED','Y']);
			//ie.init();
			ni.doShowOne(ni.crntData);
		});
		$('.itemGobkBtn').bind('click',null,ni.doBackToChoice);
	},
	
	doClearItemForm: function () {
		// assure all marc fields are empty & visible at start
		$('#newBiblioForm').each(function(){
	        this.reset();
		});
	},
	
	doShowOne: function (data){
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
		}
	  $('#selectionDiv input.online').disable();
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
