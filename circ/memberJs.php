<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
$current_timestamp = time();
?>

<script language="JavaScript" defer>
// JavaScript Document
//------------------------------------------------------------------------------
"use strict";

var mf = {
	<?php
	if ($_SESSION['mbrBarcode_flg'] == 'Y') 
		echo "showBarCd: true, \n";
	else
		echo "showBarCd: false, \n ";
	?>
	multiMode: false,
	
	init: function () {
		mf.url = '../circ/memberServer.php';
		mf.listSrvr = "../shared/listSrvr.php";

		// get header stuff going first
		mf.initWidgets();
		mf.resetForms();
		mf.fetchOpts();
		mf.fetchCustomFlds();
		mf.fetchAcnttranTypes();
		mf.prepareCopyBarcdValidation();
				
		
		$('.gobkBtn').on('click',null,mf.rtnToSrch);
		$('.gobkMbrBtn').on('click',null,mf.rtnToList);
		$('.gobkNewBtn').on('click',null,mf.rtnToSrch);
		$('.gobkUpdtBtn').on('click',null,mf.rtnToMbr);
		$('.gobkBiblioBtn').on('click',null,mf.rtnToMbr);
		$('.gobkAcntBtn').on('click',null,mf.rtnToMbr);
		$('.gobkHistBtn').on('click',null,mf.rtnToMbr);

		$('#barCdSrchBtn').on('click',null,mf.doBarCdSearch);
		$('#nameSrchBtn').on('click',null,mf.doNameSearch);
		$('#addNewMbrBtn').on('click',null,mf.doShowMbrAdd);

		$('#addMbrBtn').on('click',null,mf.doMbrAdd);
		$('#updtMbrBtn').on('click',null,mf.doMbrUpdate);
		$('#deltMbrBtn').on('click',null,mf.doDeleteMember);
		$('#cnclMbrBtn').on('click',null,function(){
			mf.doFetchMember();
			mf.rtnToMbr();
		});

		$('#mbrDetlBtn').on('click',null,mf.doShowMbrDetails);

		$('#mbrAcntBtn').on('click',null,mf.doShowMbrAcnt);
		$('#addTransBtn').on('click',null,mf.doTransAdd);

		$('#mbrHistBtn').on('click',null,mf.doShowMbrHist);

		$('#chkOutBtn').on('click',null,mf.doCheckout);
		$('#holdBtn').on('click',null,mf.doHold);

		// prepare pull-down lists
		mf.fetchMbrTypList();
		mf.fetchSiteList();
		mf.fetchStateList();
		<?php
		if ($_GET['mbrid']) { 
			echo "mf.doMbridSearch (".$_GET['mbrid'].");";
		}
		?>
	},
	
	//------------------------------
	initWidgets: function () {
	},
	
	resetForms: function () {
	  //console.log('resetting Search Form');
		if (!mf.showBarCd) $('#barCdSrchForm').hide();
		$('p.error, input.error').html('').hide();
		$('.gobkNewBtn').hide();
		$('.gobkUpdtBtn').hide();
	  $('#searchDiv').show();
	  $('#listDiv').hide();
	  $('#mbrDiv').hide();
	  $('#biblioDiv').hide();
	  $('#newDiv').hide();
	  $('#editDiv').hide();
	  $('#acntDiv').hide();
	  $('#histDiv').hide();
		$('#msgDiv').hide();
		$('#chkOutBtn').enable();
		$('#chkOutMsg').html('').hide();
		if(mf.showBarCd) {
			$('#searchByBarcd').focus();
		} else {
			$('#nameFrag').focus();
		}
	},
	prepareCopyBarcdValidation: function (){
		$('#ckoutBarcd').attr('pattern', "[0-9]{<?php echo Settings::get('item_barcode_width');?>}" );
	},
	rtnToSrch: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
		$('#ckOutBarcd').val('')
	  mf.resetForms();
	  $('#searchDiv').show();
	},
	rtnToList: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
		$('#ckOutBarcd').val('')
	  //mf.resetForms();
		$('#chkOutBtn').enable();
		$('#chkOutMsg').html('').hide();
	  $('#mbrDiv').hide();
	  $('#listDiv').show();
		$('#msgDiv').hide();
	},
	rtnToMbr: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  mf.resetForms();
	  mf.doFetchMember();
	  $('#biblioDiv').hide();
	},

	showMsg: function (msg) {
		$('#msgArea').html(msg);
		$('#msgDiv').show();
	},

	//------------------------------
	fetchOpts: function () {
	  $.post(mf.url,{mode:'getOpts'}, function(jsonData){
	    mf.opts = jsonData
		}, 'json');
	},
	getNewBarCd: function () {
	  $.post(mf.url,{mode:'getNewBarCd', width:4}, function(data){
			$('#barcode_nmbr').val(data);
		});
	},
	fetchMbrTypList: function () {
	  $.post(mf.listSrvr,{mode:'getMbrTypList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n].description+'</option>';
			}
			$('#classification').html(html);
		}, 'json');
	},
	fetchSiteList: function () {
	  $.post(mf.listSrvr,{mode:'getSiteList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#siteid').html(html);
		}, 'json');
	},
	fetchStateList: function () {
	  $.post(mf.listSrvr,{mode:'getStateList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n].description+'</option>';
			}
			$('#state').html(html);
		}, 'json');
	},
	fetchCustomFlds: function () {
	  $.post(mf.url,{mode:'getCustomFlds'}, function(jsonData){
			if ((jsonData.trim()).substr(0,1) == '<') {
				mf.showMsg(jsonData);
				return false;
			} else {
	  		mf.cstmFlds = JSON.parse(jsonData);
			}
		});
	},
	fetchAcnttranTypes: function () {
	  $.post(mf.url,{mode:'getAcntTranTypes'}, function(jsonData){
	  	mf.tranType = jsonData;
	    var html = '';
	    $.each(jsonData, function (name, value) {
	    	html += '<option value="'+name+'">'+value+'</option> \n';
			});
			$('#transaction_type_cd').html(html);
		}, 'json');
	},
	
	//------------------------------
	doMbridSearch: function (mbrid) {
	  mf.srchType = 'mbrid';
		mf.mbrid = mbrid;
	  var params = 'mode=doGetMbr&mbrid='+mbrid;
	  $.post(mf.url,params, mf.handleMbrResponse);
		return false;
	},
	doFetchMember: function () {
	  var params = 'mode=doGetMbr&mbrid='+mf.mbrid;
	  $.post(mf.url,params, mf.handleMbrResponse);
		return false;
	},

	doBarCdSearch: function () {
		var barcd = $.trim($('#searchByBarcd').val());
		barcd = flos.pad(barcd,mf.opts.mbr_barcode_width,'0');
		$('#searchByBarcd').val(barcd); // redisplay expanded value

	  mf.srchType = 'barCd';
	  var params = 'mode=doBarcdSearch&barcdNmbr='+barcd;
	  $.post(mf.url,params, mf.handleMbrResponse);
		return false;
	},
	doNameSearch: function () {
		var params = {'mode':'doNameFragSearch',
			'nameFrag':$('#nameFrag').val(),
			'timestamp': <?php echo $current_timestamp; ?>,
			'username': '<?php echo $_SESSION['username'] ?>'
			};
    	$.ajax({
			url: mf.url,
			type: 'POST',
			dataType: 'json',
			headers: {
				'Authcheck':'Token token="<?php echo hash_hmac('md5',
				'doNameFragSearch-'.$_SESSION['username'].'-'.$current_timestamp,
				$_SESSION['secret_key']); ?>"'
			},
			data: params,
			error: function(xhr, textStatus, errorThrown) {
				$('#errSpace').html('Error ' + xhr.responseText).show();
			},
			success: function (results) {
				//console.log("Done with " + results);
				var html = '';
				if (results.length == 0) {
					html = '<tr><td><?php echo T('no results') ?></td></tr>';
				} else {
					for (var i in results) {
						var mbr = results[i];
						html += '<tr>\n';
						html += '	<td>' + mbr.barcode_nmbr + '</td>\n';
						if (mbr.hasOwnProperty('first_legal_name') || mbr.hasOwnProperty('last_legal_name')) {
							html += '	<td><i>' + mf.doConcatLegalName(mbr) + ', <?php echo T('see'); ?> </i><a href="#" id="' + mbr.mbrid + '">' + mbr.last_name + ', ' + mbr.first_name + '</a></td>\n';
						} else {
							html += '	<td><a href="#" id="' + mbr.mbrid + '">' + mbr.last_name + ', ' + mbr.first_name + '</a></td>\n';
						}
						html += '	<td>' + mbr.home_phone + '</td>\n';
						html += '</tr>\n';
					}
				}

				$('#srchRslts').html(html);
				$('#searchDiv').hide();
				$('#listDiv').show();
				$('#srchRslts tr:odd td').addClass('altBG');
				$('#srchRslts tr:even td').addClass('altBG2');
				$('#srchRslts a').on('click', null, function (e) {
					e.preventDefault();
					e.stopPropagation();
					mf.mbrid = e.target.id;
					mf.doFetchMember();
					$('#listDiv').hide();
				});
			}
		});
	},

	handleMbrResponse: function (jsonInpt) {
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.mbr = JSON.parse(jsonInpt);
				if (mf.mbr == null) {
	  			mf.showMsg('<?php echo T("Nothing Found") ?>');
				}
				else {
					mf.multiMode = false;
					mf.getMbrSite();
				}
	    }
		  $('#searchDiv').hide();
	    $('#mbrDiv').show();
	},
	getMbrSite: function () {
		$.post(mf.url,{mode:'getSite', 'siteid':mf.mbr.siteid}, function (response) {
			mf.calCd = response['calendar'];
			mf.getMbrType();
		}, 'json');
	},
	getMbrType: function () {
		$.post(mf.url,{mode:'getMbrType', 'classification':mf.mbr.classification}, function (response) {
			mf.typeInfo = response;
			mf.showOneMbr(mf.mbr)
		}, 'json');
	},

	//------------------------------
	showOneMbr: function (mbr) {
		$('#mbrName').val(mbr.last_name+', '+mbr.first_name);
		$('#mbrSite').val("...");
		$('#mbrCardNo').val(mbr.barcode_nmbr);
		mf.mbrid = mbr.mbrid;
		mf.doGetCheckOuts(mf.mbrid);
		$.post(mf.url,{mode:'getSite', 'siteid':mbr.siteid}, function (response) {
			$('#mbrSite').val(response.name);
		}, 'json');
	},
	doGetCheckOuts: function () {
		$('#msgDiv').hide();
		$('#msgArea').html('');
		var ttlOwed = 0.00,
			maxFines = mf.typeInfo.max_fines,
	  		params = 'mode=getChkOuts&mbrid='+mf.mbrid;
	    $.post(mf.url,params, function(jsonInpt){
			if (jsonInpt.substr(0,1) == '<') {
				$('#msgArea').html(jsonInpt);
				$('#msgDiv').show();
			} else if ($.trim(jsonInpt) == '[]') {
				mf.cpys = [];
				$('#chkOutList tBody').html('');
			} else if ($.trim(jsonInpt).substr(0,2) != '[{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.cpys = JSON.parse(jsonInpt);
				var html = '';
				for (var nCpy in mf.cpys) {
					var cpy = mf.cpys[nCpy],
						outDate = cpy.out_dt,
						dueDate = cpy.due_dt,
						daysLate = cpy.daysLate,
						lateFee = ((cpy.lateFee === null) ? '0': (cpy.lateFee).toLocaleString()),
						owed = (cpy.daysLate*cpy.lateFee).toFixed(2),
						owedAmnt = owed.toLocaleString();
					html += '<tr>';
					html += '	<td>'+outDate+'</td>';
					//html += '	<td><img src="'+cpy.material_img_url+'" />'+cpy.material_type+'	</td>\n';
					html += '	<td>'+cpy.media+'	</td>\n';
					html += '	<td>'+cpy.barcode+'</td>';
					html += '	<td><a href="#" id="'+cpy.bibid+'">"'+cpy.title+'"</a></td>';
					html += '	<td>'+dueDate+'</td>';
					html += '	<td class="number">'+daysLate+'@'+lateFee+'</td>';
					html += '	<td class="number">'+owedAmnt+'</td>';
					html += '</tr>\n';
					ttlOwed += owed;
				}
				mf.nmbrOnloan = nCpy+1;
				$('#chkOutList tBody').html(html);
				$('table tbody.striped tr:odd td').addClass('altBG');
				$('table tbody.striped tr:even td').addClass('altBG2');	

				if (ttlOwed >= maxFines) {
					$('#chkOutBtn').disable();
					$('#ckoutBarcd').disable();
					$('#chkOutMsg').html('<?php echo T("NotAllowed");?>').show();
				}
				$('#maxFine').html((Number(maxFines).toFixed(2)).toLocaleString());
				$('#ttlOwed').html((Number(ttlOwed).toFixed(2)).toLocaleString());

				$('#chkOutList a').on('click',null,function (e) {
					e.preventDefault(); e.stopPropagation();
					idis.init(mf.opts); // be sure all is ready	
					idis.doBibidSearch(e.target.id);
					$('#biblioDiv').show();
					$('#mbrDiv').hide();					
				});			
	    }
		});
		mf.doGetHolds();
	},
	doGetHolds: function () {
    $('#holdList tBody').html('');
	  var params = 'mode=getHolds&mbrid='+mf.mbrid;
	  $.post(mf.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,1) == '<') {
				mf.showMsg(jsonInpt);
			} else {
				mf.holds = JSON.parse(jsonInpt);
				if (! mf.holds) {
	  			mf.showMsg('<?php echo T("Nothing Found") ?>');
				}
				else {
					var html = '';
					for (var nHold in mf.holds) {
						var hold = mf.holds[nHold];
						var holdDate = hold.hold_dt.split(' ')[0];
						if (hold.due_dt) 
							var dueDate = hold.due_dt.split(' ')[0];
						else
							var dueDate = 'n/a';
						html += '<tr>'
						html += '	<td> \n';
						html += '		<input type="button" class="holdDelBtn" value="<?php echo T("del");?>" /> \n';
						html += '		<input type="hidden" value="'+hold.holdid+'" /></td> \n';
						html += '	</td> \n';
						html += '	<td>'+holdDate+'</td>';
						html += '	<td>'+hold.barcode+'</td>';
						html += '	<td><a href="#" id="'+hold.bibid+'">"'+hold.title+'"</a></td>';
						html += '	<td>'+hold.status+'</td>';
						html += '	<td>'+dueDate+'</td>';
						html += '</tr>\n';

					}
					$('#holdList tBody').html(html);
					$('table tbody.striped tr:odd td').addClass('altBG');
					$('table tbody.striped tr:even td').addClass('altBG2');
					$('.holdDelBtn').on('click',null,mf.doDelHold);
					$('#holdList a').on('click',null,function (e) {
						e.preventDefault(); e.stopPropagation();
						idis.init(mf.opts); // be sure all is ready	
						idis.doBibidSearch(e.target.id);
						$('#biblioDiv').show();
						$('#mbrDiv').hide();					
					});			
				}
	    }
		});
	},
	
	//------------------------------
	doShowMbrAcnt: function () {
	  var params = 'mode=getAcntActivity&mbrid='+mf.mbrid;
	  $.post(mf.url,params, function(jsonInpt){
			$('#tranList tBody').html(''); // clear any residue from past displays
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#msgArea').html(jsonInpt);
				$('#msgDiv').show();
			} else {
				mf.trans = JSON.parse(jsonInpt);
				var html = '';
				if (!mf.trans) {
					html += '<tr>'
					html += '<td colspan="6"><?php echo T("No transactions found."); ?></td> \n';
					html += '</tr>\n';
				} else {
					var bal = parseFloat(0.0);
					html += '<tr> \n';
					html += '	<td colspan="3">&nbsp</td> \n';
					html += '	<td colspan="2" class="smallType center"><?php echo T("Opening Balance"); ?></td> \n';
					html += '	<td class="number">'+bal.toFixed(2)+'</td> \n';
					html += '</tr> \n';
					for (var nTran in mf.trans) {
						var tran = mf.trans[nTran];
						bal += parseFloat(tran.amount);
						html += '<tr> \n';
						html += '	<td> \n';
						html += '		<input type="button" class="acntTranDelBtn" value="<?php echo T("del");?>" /> \n';
						html += '		<input type="hidden" value="'+tran.transid+'" /></td> \n';
						html += '	</td> \n';
						html += '	<td class="date">'+tran.create_dt.split(' ')[0]+'</td> \n';
						html += '	<td>'+mf.tranType[tran.transaction_type_cd]+'</td> \n';
						html += '	<td>'+tran.description+'</td> \n';
						html += '	<td class="number">'+(parseFloat(tran.amount)).toFixed(2)+'</td> \n';
						html += '	<td class="number">'+bal.toFixed(2)+'</td> \n';
						html += '</tr> \n';
					}
					$('#tranList tBody').html(html);
					$('#tranList tbody.striped tr:odd td').addClass('altBG');
					$('#tranList tbody.striped tr:even td').addClass('altBG2');
					$('.acntTranDelBtn').on('click',null,mf.doDelAcntTrans);
				};			
			}
		});
		$('#mbrDiv').hide();
		$('#acntDiv').show();
	},
	doTransAdd: function (e) {
		e.preventDefault;
		e.stopPropagation;
		$('#acntMbrid').val(mf.mbrid);
		var parms = $('#acntForm').serialize();
		//console.log('adding: '+parms);
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				document.forms.acntForm.reset();
				mf.showMsg('Added!');
				mf.doShowMbrAcnt();
			}
		});
		return false;
	},
	doDelAcntTrans: function (e) {
		var transid = $(this).next().val();
		if (!confirm('<?php echo T("Are you sure you want to delete "); ?>'+'this transaction?')) return false;

  	var parms = {	'mode':'d-3-L-3-tAcntTrans', 'mbrid':mf.mbrid, 'transid':transid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				mf.showMsg('transaction deleted!');
		  	//mf.rtnToMbr();
				mf.doShowMbrAcnt();
			}
		});
	},
	
	//------------------------------
	doCheckout: function () {
		$('#msgArea').html('');
		$('#msgDiv').hide();

		var barcd = $.trim($('#ckoutBarcd').val());
		if (barcd == '') {
      		mf.showMsg('Please enter a number');
			return false;
		}
		barcd = flos.pad(barcd,mf.opts.item_barcode_width,'0');
		$('#ckoutBarcd').val(barcd); // redisplay expanded value

		var parms = {'mode':'doCheckout', 'mbrid':mf.mbr.mbrid, 'barcodeNmbr':barcd, 'calCd':mf.calCd };
		$.post(mf.url, parms, function(response) {
			if (response == '') {
				mf.showMsg('Checkout Completed!');
				$('#ckoutBarcd').val('')
				mf.showOneMbr(mf.mbr);  // refresh member with latest checkout list
			} else {
				mf.showMsg(response);
			}
		});
		return false;
	},

	//------------------------------
	doHold: function () {
		var barcd = $.trim($('#holdBarcd').val());
		barcd = flos.pad(barcd,mf.opts.item_barcode_width,'0');
		$('#holdBarcd').val(barcd); // redisplay expanded value

		var parms = {'mode':'doHold', 'mbrid':mf.mbrid, 'barcodeNmbr':barcd};
		$.post(mf.url, parms, function(response) {
			if (response == '<') {
				mf.showMsg(response);
			} else {
				mf.showMsg('Hold Completed!');
				$('#holdBarcd').val('')
				mf.showOneMbr(mf.mbr)
			}
		});
		return false;
	},
	doDelHold: function (event) {
		var $delBtn = $(event.target);
		$delBtn.parent().parent().addClass('hilite');
		if (!confirm('<?php echo T("Are you sure you want to delete this");?>'+' hold?')) return false;
		
		var holdid = $delBtn.next().val();
  	var parms = {	'mode':'d-3-L-3-tHold', 'mbrid':mf.mbrid, 'holdid':holdid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				mf.showMsg('hold deleted!');
				mf.showOneMbr(mf.mbr)
			}
		});
	},
	
	//------------------------------
	doShowMbrHist: function () {
		$('#mbrDiv').hide();
		$('#histDiv').show();
		var statMap = {'crt':'IN', 'in':'IN', 'out':'OUT'};
	  $.post(mf.url,{mode:'getHist', 'mbrid':mf.mbrid}, function(jsonInpt){
			$('#histList tBody').html(''); // clear any residue from past displays
			if ($.trim(jsonInpt).substr(0,1) != '[') {
				$('#msgArea').html(jsonInpt);
				$('#msgDiv').show();
			} else {
				mf.hist = JSON.parse(jsonInpt);
				var html = '';
				if (!mf.hist) {
					html += '<tr>'
					html += '<td colspan="6"><?php echo T("No transactions found."); ?></td> \n';
					html += '</tr>\n';
				} else {
					for (var nHist in mf.hist) {
						var hist = mf.hist[nHist];
						html += '<tr> \n';
						html += '	<td>'+hist.title+'</td> \n';
						html += '	<td>'+statMap[hist.status_cd]+'</td> \n';
						html += '	<td class="date">'+hist.status_begin_dt.split(' ')[0]+'</td> \n';
						html += '</tr> \n';
					}
					$('#histList tBody').html(html);
					$('#histList tbody.striped tr:odd td').addClass('altBG');
					$('#histList tbody.striped tr:even td').addClass('altBG2');
				};
			}
		});
		return false;
	},
	
	//------------------------------
	doShowMbrDetails: function (e) {
		var mbr = mf.mbr;
		$('#addMbrBtn').hide();
		$('#updtMbrBtn').show().enable();
		$('#deltMbrBtn').show().enable();
		$('.gobkUpdtBtn').show();
		$('#editHdr').html('<?php echo T("Edit Member Info"); ?>');
		$('#editMode').val('updateMember');

		$('#mbrid').val(mbr.mbrid);
		$('#siteid').val(mbr.siteid);

		// folowing 'readonly' if existing member
		$('#barcode_nmbr').val(mbr.barcode_nmbr);
		if (mbr.barcode_nmbr) {
			$('#barcode_nmbr').attr('readonly','readonly');
		} else {
			$('#barcode_nmbr').removeAttr('readonly');
		}

		$('#last_name').val(mbr.last_name);
		$('#first_name').val(mbr.first_name);
		$('#address1').val(mbr.address1);
		$('#address2').val(mbr.address2);
		$('#city').val(mbr.city);
		$('#state').val(mbr.state);
		$('#zip').val(mbr.zip);
		$('#zip_ext').val(mbr.zip_ext);
		$('#home_phone').val(mbr.home_phone);
		$('#work_phone').val(mbr.work_phone);
		$('#email').val(mbr.email);
		$('#classification').val(mbr.classification);

		$.each(mf.cstmFlds, function (n, value) {
			var fld = value,
					code=fld.code;
			$('#custom_'+code).val(mbr[code]);
		});

		$('#mbrDiv').hide();
		$('#editDiv').show();
	},
	doShowMbrAdd: function () {
		$('#addMbrBtn').show();
		$('#updtMbrBtn').hide();
		$('#deltMbrBtn').hide();
		$('#msgDiv').hide();
		$('.gobkNewBtn').show();
		document.forms.editForm.reset();

		$('#siteid').val([$('#crntSite').val()]);
		$('#city').val([$('#crntCity').val()]);
		mf.getNewBarCd();  // posts directly to screen

		$('#searchDiv').hide();
		$('#editHdr').html('<?php T("Add New Member"); ?>');
		$('#editMode').val('addNewMember');
		//mf.showMsg('Added!');
		$('#editDiv').show();
	},
	
	doMbrAdd: function () {
		$('#msgDiv').hide();
		var parms = $('#editForm').serialize();
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='0') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html('<?php echo T("Added");?>');
					$('#updateMsg').show();
				}
				mf.showMsg('Added!');
				$('#msgDiv').show().hide(10000);
			}
		});
		return false;
	},
	doMbrUpdate: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html('<?php echo T("Updated");?>');
					$('#updateMsg').show();
				}
				mf.showMsg('Updated!');
				mf.doFetchMember();
				$('#editDiv').hide();
			}
		});
		$('#updtMbrBtn').disable();
		$('#deltMbrBtn').disable();
		return false;
	},
	doDeleteMember: function () {
		if (mf.nmbrOnloan > 0) {
			alert('<?php echo T("You must settle all outstanding loans before deleting a member."); ?>');
			return false;
		}
		var delConfirmMsg = '<?php echo T("Are you sure you want to delete "); ?>';
		if (!confirm(mf.delConfirmMsg+ mf.mbr.first_name+' '+mf.mbr.last_name+'?')) return false;

  	var parms = {	'mode':'d-3-L-3-tMember', 'mbrid':mf.mbrid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				mf.showMsg(response);
			}
			else {
				mf.showMsg('member deleted!');
		  	mf.rtnToSrch();
			}
		});
	},
	doConcatLegalName: function (mbr) {
		if (mbr['first_legal_name']) {
			if (mbr['last_legal_name']) {
				return mbr.first_legal_name+' '+mbr.last_legal_name;
			} else {
				return mbr.first_legal_name+' '+mbr.last_name;
			}
		} else if (mbr['last_legal_name']) {
			return mbr.first_name+' '+mbr.last_legal_name;
		} else {
			return mbr.first_name+' '+mbr.last_name;
		}
	},
};
$(document).ready(mf.init);

</script>

