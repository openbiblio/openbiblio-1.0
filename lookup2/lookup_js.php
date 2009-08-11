<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
//------------------------------------------------------------------------------
// lookup Javascript
lkup = {
<?php
//	echo 'editHdr 	 				:"'.T('lookup_optsSettings').'",'."\n";
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
?>
	init: function () {
		// get header stuff going first
		lkup.initWidgets();

		lkup.url = 'server.php';
		lkup.form = $('#lookupForm');
		
		// button on search screen gets special treatment
		lkup.srchBtn = $('#srchBtn');
		lkup.resetForm();

    $('.criteria').bind('change',null,lkup.enableSrchBtn);
		$('#quitBtn').bind('click',null,lkup.doAbandon);
		$('#retryBtn').bind('click',null,lkup.doBackToSrch);
		$('#choiceBtn1').bind('click',null,lkup.doBackToSrch);
		$('#choiceBtn2').bind('click',null,lkup.doBackToSrch);
		$('#lookupForm').bind('submit',null,function(){
			lkup.doValidate();
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
		$('#selectionDiv input[value="Cancel"]').attr('value',lkup.goBack);
		$('#submitBtn').val(lkup.accept);
		$('#newbiblioform').bind('submit',null,function(){
console.log('callnmbr='+$('#099a').val());
	  	var parms=$('#newbiblioform').serialize();
			console.log('submitting parms: '+parms);
			return true;
		});

		// FIXME - fl only '*' should be colored
		$('#selectionDiv font').css('color','red');
		$('#selectionDiv sup').css('color','red');
		lkup.inputColor = $('#99').css('color');
		$('#100a').bind('change',null,lkup.fixAuthor);
		$('#245a').bind('change',null,lkup.fixTitle);

		lkup.fetchHosts();  //on completion, search form will appear
		lkup.fetchOpts();  //for debug use
	},
	
	//------------------------------
	initWidgets: function () {
	},
	
	resetForm: function () {
	  //console.log('resetting Search Form');
		$('#help').hide();
	  $('#searchHdr').html(lkup.searchHdr);
		$('#searchDiv').show();
		$('#errMsgTxt').html(' ');
		$('#waitDiv').hide();
		$('#retryDiv').hide();
		$('#choiceDiv').hide();
		$('#selectionDiv').hide();

		$('#lookupVal').focus();
		lkup.disableSrchBtn();
	},
	
	disableSrchBtn: function () {
	  lkup.srchBtnBgClr = lkup.srchBtn.css('color');
	  lkup.srchBtn.css('color', '#888888');
		lkup.srchBtn.disable();
	},
	enableSrchBtn: function () {
	  lkup.srchBtn.css('color', lkup.srchBtnBgClr);
		lkup.srchBtn.enable();
	},

	doBackToSrch: function () {
		lkup.resetForm();
	},

	doBackToChoice: function () {
		$('#selectionDiv').hide();
		$('#choiceDiv').show();
	},
	//------------------------------
	fetchOpts: function () {
	  $.getJSON(lkup.url,{mode:'getOpts'}, function(data){
			lkup.opts = data;
		});
	},

	fetchHosts: function () {
	  $.getJSON(lkup.url,{mode:'getHosts'}, function(data){
			lkup.hostJSON = data;
			$('#waitDiv').hide();
			$('#searchDiv').show();
		});
	},

	doAbandon: function () {
	  $.getJSON(lkup.url,{mode:'abandon'}, function(data){
			$('#searchDiv').show();
		});
	},

	//------------------------------
	// search related stuff
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
	   	if ((isNaN(parseInt(test))) || (!lkup.chkIsbn(test))) {
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
		  lkup.doSearch();
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
		for (nHost in lkup.hostJSON) {
			theTxt += '&nbsp;&nbsp;&nbsp;'+n+'. '+lkup.hostJSON[nHost].name+'<br />';
			n++;
		}
		theTxt += '</h5>';
	  $('#waitText').html(theTxt);
	  
		$('#searchDiv').hide();
		$('#waitDiv').show();
		
		// note for this to work, all form fields MUST have a 'name' attribute
		$('lookupForm #mode').val('search');
		var srchParms = $('#lookupForm').serialize();
		$.post(lkup.url, srchParms, function(response) {
			$('#waitDiv').hide();
			
			if ($.trim(response).substr(0,1) != '{') {
				$('#retryHead').empty();
				$('#retryHead').html(lkup.searchError);
				$('#retryMsg').empty();
				$('#retryMsg').html(response);
				$('#retryDiv').show();
			}
			else {
				var rslts = eval('('+response+')'); // JSON 'interpreter'
				var numHits = parseInt(rslts.ttlHits);
				var maxHits = parseInt(rslts.maxHits);
				if (numHits < 1) {
					console.log('nothing found');
				  //{'ttlHits':$ttlHits,'maxHits':$postVars[maxHits],
					// 'msg':".$lookLoc->getText('lookup_NothingFound'),
					// 'srch1':['byName':$srchByName,'val':$lookupVal],
					// 'srch2':['byName':$srchByName2,'val':$lookupVal2]}
					var str = rslts.msg+':<br />&nbsp;&nbsp;&nbsp;'+rslts.srch1.byName+' = '+rslts.srch1.lookupVal;
					if (rslts.srch2.lookupVal != '')
						str += '<br />&nbsp;&nbsp;&nbsp;'+rslts.srch2.byName+' = '+rslts.srch2.lookupVal;
					$('#retryHead').empty();
					$('#retryHead').html(lkup.nothingFound);
					$('#retryMsg').empty();
					$('#retryMsg').html(str);
					$('#retryDiv').show();
				}
			
				else if (numHits >= maxHits) {
					console.log('too many hits');
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
					console.log('more than one hit');
					$('#choiceSpace').empty();
					$('#choiceSpace').append('<h3>Success!  <span id="ttlHits"></span></h3>');

					var nHits = 0;
					lkup.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  //$('#choiceSpace').append('<hr width="50%">');
					  if (typeof(hostData) != undefined) {
					  $('#choiceSpace').append('<h4>Repository: '+lkup.hostJSON[hostIndex].name+'</h4>');
					  $.each(hostData, function(hitIndex,hitData) {
					    nHits++;
					    html  = '<hr width="50%">';
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
							html += '</table></form>';
							$('#choiceSpace').append(html);
							$('#'+id).bind('click',{host:hostIndex,hit:hitIndex,data:hitData},lkup.doSelectOne);
						}); // $.each(hostData...
						} // if (lkup.hostJason[hostIndex])
					}); // $.each(rslts.data...
					$('#ttlHits').html(nHits+' hits found.')
					//console.log('all choices drawn')
					//$('#choiceSpace').append(response);
					$('#biblioBtn').bind('click',null,lkup.doBackToChoice);
					$('#biblioBtn2').bind('click',null,lkup.doBackToChoice);
					$('#choiceDiv').show();
				} // else if (rslts.ttlHits > 1)
				else if (rslts.ttlHits == 1){
				  var data;
					console.log('single hit found');
					lkup.hostData = rslts.data;
					$.each(rslts.data, function(hostIndex,hostData) {
					  $.each(hostData, function(hitIndex,hitData) {
					  	data = hitData;
					  });
					});
					$('#biblioBtn').bind('click',null,lkup.doBackToSrch);
					$('#biblioBtn2').bind('click',null,lkup.doBackToSrch);
					lkup.doShowOne(data);
				}
			} // else
		}); // .post
	},
	
	doSelectOne: function (e) {
	  var host = e.data.host;
	  var hit = e.data.hit;
	  var data = e.data.data;
		lkup.doShowOne(data);
	},

	doStriping: function () {
		$('#biblioFldTbl').each(function() {
			var $table = $(this);
				$table.find('tbody#marcBody tr:not(.hidden):even').addClass('altBG');
		});
	},

	doShowOne: function (data){
		// assure all are visible at start
    $(".marcBiblioFld").each(function(){
			$(this).parent().parent().show();
		});

		var tag;
		for (tag in data) {
			if (data[tag] != '') {
				$('#'+tag).val(data[tag]);
				$('#'+tag).css('color',lkup.inputColor);
			} else {
				$('#'+tag).val('entry required here');
				$('#'+tag).css('color','red');
			}
		}
		$('#opacFlg').val(['CHECKED']);

		// hide any unused MARC fields
    $(".marcBiblioFld").each(function(){
			if (($(this).val()).length == 0) {
				$(this).parent().parent().hide().addClass('hidden');
			}
		});
		
		lkup.setCallNmbr(data);
		lkup.setCollection(data);

		lkup.doStriping();
		$('#choiceDiv').hide();
		$('#selectionDiv').show();
	},
	
	setCallNmbr: function (data) {
		switch (lkup.opts['callNmbrType'].toLowerCase())  {
		case 'loc':
			$('#099a').val(data['050a']+' '+data['050b']);
			break;
		case 'dew':
		  var callNmbr = lkup.makeCallNmbr(data['082a']);
    	var cutter = lkup.makeCutter(data['100a'], data['245a']);
			$('#099a').val(callNmbr+cutter);
			break;
		case 'udc':
		  var callNmbr = lkup.makeCallNmbr(data['080a']);
			$('#099a').val(callNmbr+' '+data['080b']);
			break;
		case 'local':
			// leave the fields blank for user entry
			break;
		default:
		  break;
		}
		if ($('#099a').val() != '') {
			$('#099a').css('color',lkup.inputColor);
			$('#099a').parent().parent().show().removeClass('hidden');
		}
	},

	makeCallNmbr: function (code) {
		if ((code) && ((lkup.opts['callNmbrType']).toLowerCase() == "dew")) {
			var fictionDew = lkup.opts['fictionDew'].split(' ');
			if (lkup.opts['autoDewey']
					&&
					((code == "") || (code == "[Fic]"))
					&&
					(fictionDew.indexOf(code) >= 0)
				 ) {
				dew = lkup.opts['defaultDewey'];
			}

			var parts = code.split('.');
			var base1 = parts[0];
			var callNmbr = base1;
			if (parts[1]) {
				var base2 = parts[1].split('/');
				callNmbr += '.'+base2;
			}
			callNmbr = callNmbr.replace('/', '');

			return callNmbr;
		}
	},
	
	fixTitle: function () {
	  var titl = $('#245a').val();
		if (titl != '') {
    	$('#245a').css('color',lkup.inputColor);
    	var auth = $('#100a').val();
	  	lkup.makeCutter(auth, titl); // will post direct to screen
		}
		else {
    	$('#245a').css('color','red');
		}
	},
	
	fixAuthor: function () {
	  var auth = $('#100a').val();
		if (auth != '') {
    	$('#100a').css('color',lkup.inputColor);
    	var titl = $('#245a').val();
	  	lkup.makeCutter(auth, titl);
		}
		else {
    	$('#100a').css('color','red');
		}
	},

	makeCutter: function (auth,titl) {
		if ((auth != 'undefined') && (lkup.opts['autoCutter'])) {
	  	$.getJSON(lkup.url,{mode:'getCutter', author:auth}, function(data){
				var cutter = data['cutter'];
	  		if (lkup.opts['cutterType'] == 'Dew') {
			  	// suffix is first char of a specified word in title
					cutter += lkup.makeSuffix($('#245a').val());
				}
				else if (lkup.opts['cutterType'] == 'LoC') {
					// add copyright year as suffix -- FIXME numeric year only!!!
					//var cpyYr = $.trim($('#260c').val()).replace('.','');
					//cutter += ' '+cpyYr.substr(1,4);
					cutter += ' '+$('#260c').val();
				}
				$('#099a').val($('#099a').val()+cutter);
			});
		}
	},

	makeSuffix: function (s) {
		inputWords = s.split('/');

		var nWords = 0;
		var goodWords = '';
		for (var index in inputWords) {
			if ((inputWords[index] != ' ') && ((lkup.opts['noiseWords']).indexOf(inputWords[index]) < 0)) {
				goodWords+=' '+inputWords[index];
				nWords++;
			}
		}
		goodWords = $.trim(goodWords);
		wordArray = goodWords.split(/\s+/);

		if (nWords == 1)
			var sufx = (wordArray[0]).substr(0,1);
		else if (nWords <= lkup.opts['cutterWord'])
			sufx = (wordArray[nWords-1]).substr(0,1);
		else if (nWords > lkup.opts['cutterWord'])
			sufx = (wordArray[lkup.opts['cutterWord']-1]).substr(0,1);

		return sufx.toLowerCase();
	},

	setCollection: function (data) {
		//// -- attempt to determine proper collection from LOC call number
		//				this is experimental and may not be to your taste
		if (lkup.opts['autoCollect']) {
			var index = lkup.opts['fictionCode'];
			var collection = lkup.opts['defaultCollect'];

			if ((data['050a']) && (lkup.opts['callNmbrType'] == 'LoC')) {
				var locClass = (data['050a']).substr(0,2);
				if ((lkup.opts['fictionLoc']).indexOf(locClass) >= 0) {
					collection = $.trim(lkup.opts['fictionName']);
				}
			}
			else if ((data['082a']) && (lkup.opts['callNmbrType'] == 'Dew')) {
				var dewClass = (data['082a']).substr(0,3);
				if ((lkup.opts['fictionDew']).indexOf(dewClass) >= 0) {
					collection = $.trim(lkup.opts['fictionName']);
				}
			}
			$('#collectionCd').val(collection);
		}
	}
};

$(document).ready(lkup.init);

</script>
