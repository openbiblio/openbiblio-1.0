<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>

<style>
h4 {
	margin: 0; padding: 0; text-align: left; color: blue;
	}
h5 {
	margin: 0; padding: 0; text-align: left; color: blue;
	}
h5#updateMsg {
	color: red;
	}
p#errMsgTxt {
	color: red; text-align: center;
	}
table#showList tr {
	height: 1.3em;
	}
th.colHead {
  white-space: nowrap;
	}
td.lblFld {
  white-space: nowrap;
	}
td.inptFld {
  vertical-align: top;
	}
td.btnFld {
  text-align: center;
	}
.editBtn {
	margin: 0; padding: 0; height: 1.5em; text-align:center;
	}
</style>

<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
ni = {
<?php
/*
	echo 'editHdr 	 				:"'.T('lookup_optsSettings').'",'."\n";
	echo 'searchHdr					:"'.T("lookup_z3950Search").'",'."\n";
	echo 'isbn							:"'.T("lookup_isbn").'",'."\n";
	echo 'issn							:"'.T("lookup_issn").'",'."\n";
	echo 'lccn							:"'.T("lookup_lccn").'",'."\n";
	echo 'title							:"'.T("lookup_title").'",'."\n";
	echo 'author						:"'.T("lookup_author").'",'."\n";
	echo 'keyword						:"'.T("lookup_keyword").'",'."\n";
	echo 'publisher					:"'.T("lookup_publisher").'",'."\n";
	echo 'pubLoc						:"'.T("lookup_pubLoc").'",'."\n";
	echo 'pubDate						:"'.T("lookup_pubDate").'",'."\n";
	echo 'andOpt						:"'.T("lookup_andOpt").'",'."\n";
	echo 'search						:"'.T("lookup_search").'",'."\n";
	echo 'abandon						:"'.T("lookup_abandon").'",'."\n";
	echo 'repository				:"'.T("lookup_repository").'",'."\n";
	echo 'yaz_setup_failed	:"'.T("lookup_yazSetupFailed").'",'."\n";
	echo 'badQuery					:"'.T("lookup_badQuery").'",'."\n";
	echo 'patience					:"'.T("lookup_patience").'",'."\n";
	echo 'resetInstr				:"'.T("lookup_resetInstr").'",'."\n";
	echo 'goBack						:"'.T("lookup_goBack").'",'."\n";
	echo 'accept						:"'.T("lookup_accept").'",'."\n";
	echo 'yazError					:"'.T("lookup_yazError").'",'."\n";
	echo 'nothingFound			:"'.T("lookup_nothingFound").'",'."\n";
	echo 'tooManyHits				:"'.T("lookup_tooManyHits").'",'."\n";
	echo 'refineSearch			:"'.T("lookup_refineSearch").'",'."\n";
	echo 'success						:"'.T("lookup_success").'",'."\n";
	echo 'hits							:"'.T("lookup_hits").'",'."\n";
	echo 'callNmbrType			:"'.T("lookup_callNmbrType").'",'."\n";
	echo 'useThis						:"'.T("lookup_useThis").'",'."\n";
	echo 'searchError				:"'.T("lookup_searchError").'",'."\n";
*/
?>
	init: function () {
		// get header stuff going first
		ni.initWidgets();

		ni.url = 'server.php';
		ni.form = $('#lookupForm');
		ni.bs_url = '../catalog/biblio_server.php';
		
		ni.srchBtn = $('#srchBtn');
		ni.resetForm();

		// search form functions
    $('.criteria').bind('change',null,ni.enableSrchBtn);
    $('#manualBtn').bind('click',null,ni.doShowOne);
    
		$('#quitBtn').bind('click',null,ni.doAbandon);
		$('#retryBtn').bind('click',null,ni.doBackToSrch);
		$('#choiceBtn1').bind('click',null,ni.doBackToSrch);
		$('#choiceBtn2').bind('click',null,ni.doBackToSrch);
		$('#lookupForm').bind('submit',null,function(){
			ni.doValidate();
			return false;
		});

		// modify original biblioFields form to better suit our needs
		$('#reqdNote').css({display:'inline', width:'10em'});
		$('<input type="button" id="biblioBtn2" class="button" value="<?php echo T('Go Back');?>" />')
			.insertAfter('#reqdNote');
		$('<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>')
			.insertBefore('#biblioBtn2');
		$('#selectionDiv input[value="Cancel"]').removeAttr('onClick');
		$('#selectionDiv input[value="Cancel"]').attr('id','biblioBtn');
		$('#selectionDiv input[value="Cancel"]').attr('value',ni.goBack);
		$('#newbiblioform #submitBtn').val(ni.accept);
		$('#newbiblioform #submitBtn').bind('click',null,function(){
			ni.doInsertNew();
			return false;
		});

		// for the copy editor functions
		// to handle startup condition
		$('#copySubmitBtn').bind('click',null,function () {
			return ni.doCopyNew();
			//return false;
		});
		$('#copyCancelBtn').bind('click',null,function () {
			return ni.doBackToSrch();
			//return false;
		});
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
		$('#selectionDiv font').css('color','red');
		$('#selectionDiv sup').css('color','red');
		ni.inputColor = $('#99').css('color');
		$('#100a').bind('change',null,ni.fixAuthor);
		$('#245a').bind('change',null,ni.fixTitle);

		ni.fetchHosts();  //on completion, search form will appear
		ni.fetchOpts();  //for debug use
	},
	
	//------------------------------
	initWidgets: function () {
	},
	
	resetForm: function () {
	  //console.log('resetting Search Form');
		$('#help').hide();
		$('#searchDiv').show();
		$('#errMsgTxt').html(' ');
		$('#waitDiv').hide();
		$('#retryDiv').hide();
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
		//$('#retryDiv').hide();
		//$('#searchDiv').show();
		//$('#lookupVal').focus();
		//ni.disableSrchBtn();
	},
	
	doBackToChoice: function () {
		$('#selectionDiv').hide();
		$('#choiceDiv').show();
	},
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(ni.url,{mode:'getOpts'}, function(data){
			ni.opts = data;
		});
	},

	fetchHosts: function () {
	  $.getJSON(ni.url,{mode:'getHosts'}, function(data){
			ni.hostJSON = data;
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
	 	var parms=$('#newbiblioform').serialize();
		parms += '&mode=doInsertBiblio';
	  $.post(ni.url,parms, function(jsonInpt){
	    var rslt = eval('('+jsonInpt+')');
	    ni.bibid = rslt.bibid;
	  	ni.showCopyEditor();
		});
	},
	
	showCopyEditor: function () {
  	$('#selectionDiv').hide();
		if ($('#autobarco:checked').length > 0) {
			ni.doGetBarcdNmbr(ni.bibid);
		}
		$('#copyEditorDiv').show();
	},

	doCopyNew: function () {
		var params= $('#copyForm').serialize() + "&mode=newCopy&bibid="+ni.bibid;
		if ($('#autobarco:checked').length > 0) {
			params += "&barcode_nmbr="+$('#copyTbl #barcode_nmbr').val();
		}
	  $.post(ni.bs_url,params, function(response){
			//console.log(response);
	  	ni.doBackToSrch();
	  });
	  return false;
	},

	doGetBarcdNmbr: function () {
		$.getJSON(ni.bs_url,{'mode':'getBarcdNmbr','bibid':ni.bibid}, function(jsonInpt){
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
	
	doValidate: function () {
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
				var rslts = eval('('+response+')'); // JSON 'interpreter'
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

					var nHits = 0;
					ni.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  //$('#choiceSpace').append('<hr width="50%">');
					  if (typeof(hostData) != undefined) {
					  $('#choiceSpace').append('<h4>Repository: '+ni.hostJSON[hostIndex].name+'</h4>');
					  $.each(hostData, function(hitIndex,hitData) {
					    nHits++;
					    html  = '<fieldset>';
					    html += '<form class="hitForm"><table border="0">';
					    html += '<tr><td class="primary">LCCN</th><td class="primary">'+hitData['010a']+'</td></tr>';
					    html += '<tr><td class="primary">ISBN</th><td class="primary">'+hitData['020a']+'</td></tr>';
					    html += '<tr><td class="primary">Title</th><td class="primary">'+hitData['245a']+'</td></tr>';
					    html += '<tr><td class="primary">Author</th><td class="primary">'+hitData['100a']+'</td></tr>';
					    html += '<tr><td class="primary">Publisher</th><td class="primary">'+hitData['260b']+'</td></tr>';
					    html += '<tr><td class="primary">Location</th><td class="primary">'+hitData['260a']+'</td></tr>';
					    html += '<tr><td class="primary">Date</th><td class="primary">'+hitData['260c']+'</td>';
							var id = 'host'+hostIndex+'-hit'+hitIndex;
					    html += '<td id="'+id+'" class="primary"><input type="button" value="This One" class="button" /></td></tr>';
							html += '</table></form></fieldset>';
							$('#choiceSpace').append(html);
							$('#'+id).bind('click',{host:hostIndex,hit:hitIndex,data:hitData},ni.doSelectOne);
						}); // $.each(hostData...
						} // if (ni.hostJason[hostIndex])
					}); // $.each(rslts.data...
					$('#ttlHits').html(nHits)
					//console.log('all choices drawn')
					//$('#choiceSpace').append(response);
					$('#biblioBtn').bind('click',null,ni.doBackToChoice);
					$('#biblioBtn2').bind('click',null,ni.doBackToChoice);
					$('#choiceDiv').show();
				} // else if (rslts.ttlHits > 1)
				else if (rslts.ttlHits == 1){
				  var data;
					//console.log('single hit found');
					ni.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  $.each(hostData, function(hitIndex,hitData) {
					  	data = hitData;
					  });
					});
					$('#biblioBtn').bind('click',null,ni.doBackToSrch);
					$('#biblioBtn2').bind('click',null,ni.doBackToSrch);
					ni.doShowOne(data);
				}
			} // else
		}); // .post
	},
	
	doSelectOne: function (e) {
	  var host = e.data.host;
	  var hit = e.data.hit;
	  var data = e.data.data;
		ni.doShowOne(data);
	},

	doStriping: function () {
		$('#biblioFldTbl').each(function() {
			var $table = $(this);
				$table.find('tbody#marcBody tr:not(:hidden):even').addClass('altBG');
		});
	},

	doShowOne: function (data){
	  $('#searchDiv').hide();
		// assure all are visible at start
    $(".marcBiblioFld").each(function(){
			$(this).parent().parent().show();
		});

		for (var tag in data) {
			if (data[tag] != '') {
				$('#'+tag).val(data[tag]);
				$('#'+tag).css('color',ni.inputColor);
			} else {
				$('#'+tag).val('entry required here');
				$('#'+tag).css('color','red');
			}
		}
		$('#opacFlg').val(['CHECKED']);

		// hide any unused MARC fields
//    $(".marcBiblioFld").each(function(){
//			if (($(this).val()).length == 0) {
//				$(this).parent().parent().hide().addClass('hidden');
//			}
//		});
		
		ni.setCallNmbr(data);
		ni.setCollection(data);

		ni.doStriping();
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
			//console.log('callNmbr='+callNmbr)
    	var cutter = ni.makeCutter(data['100a'], data['245a']);
			//console.log('cutter='+cutter)
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
	  var cutter = '';
	  auth = auth.trim(); titl = titl.trim();
		if ((ni.opts['autoCutter']) && ((auth != '') && (auth != 'undefined'))  ) {
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
		if (ni.opts['autoCollect']) {
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
			$('#collectionCd').val(collection);
		}
	}
};

$(document).ready(ni.init);

</script>
