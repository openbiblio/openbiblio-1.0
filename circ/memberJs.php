<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
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
		mf.url = 'memberServer.php';

		// get header stuff going first
		mf.initWidgets();
		mf.resetForms();
		mf.fetchOpts();
		mf.fetchCustomFlds();
		mf.fetchAcnttranTypes();
				
		$('form').on('submit',null,function (e) {
			e.preventDefault();
			e.stopPropagation();
			var theId = $('input[type="submit"]:focus').attr('id');
			//console.log('the btn id is: '+theId);
			switch (theId) {
				case 'barCdSrchBtn':	mf.doBarCdSearch();	break;
				case 'nameSrchBtn':		mf.doNameSearch();	break;
				case 'addMbrBtn':			mf.doMbrAdd();			break;
				case 'updtMbrBtn':		mf.doMbrUpdate();		break;
				case 'addTransBtn':		mf.doTransAdd();		break;
			}
		});
		
		$('.gobkBtn').on('click',null,mf.rtnToSrch);
		$('.gobkMbrBtn').on('click',null,mf.rtnToList);
		$('.gobkNewBtn').on('click',null,mf.rtnToSrch);
		$('.gobkUpdtBtn').on('click',null,mf.rtnToMbr);
		$('.gobkBiblioBtn').on('click',null,mf.rtnToMbr);
		$('.gobkAcntBtn').on('click',null,mf.rtnToMbr);
		$('.gobkHistBtn').on('click',null,mf.rtnToMbr);
		$('#addNewMbrBtn').on('click',null,mf.doShowMbrAdd);
		$('#mbrDetlBtn').on('click',null,mf.doShowMbrDetails);
		$('#mbrAcntBtn').on('click',null,mf.doShowMbrAcnt);
		$('#mbrHistBtn').on('click',null,mf.doShowMbrHist);
		$('#chkOutBtn').on('click',null,mf.doCheckout);
		$('#holdBtn').on('click',null,mf.doHold);
		$('#deltMbrBtn').on('click',null,mf.doDeleteMember);
		$('#cnclMbrBtn').on('click',null,function(){
			mf.doFetchMember(); 
			mf.rtnToMbr();
		});
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
	  $('#mbrDiv').hide();
	  $('#listDiv').show();
	},
	rtnToMbr: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  //mf.resetForms();
	  mf.doFetchMember();
	  $('#biblioDiv').hide();
	},

	//------------------------------
	fetchOpts: function () {
	  $.getJSON(mf.url,{mode:'getOpts'}, function(jsonData){
	    mf.opts = jsonData
		});
	},
	fetchCustomFlds: function () {
	  $.getJSON(mf.url,{mode:'getCustomFlds'}, function(jsonData){
	  	mf.cstmFlds = jsonData;
/*
			var html = '';
			$.each(mf.cstmFlds, function (name, value) {
				//console.log(data[item]);
	    	html += '<tr> \n';
	    	html += '	<td><label for="custom_'+name+'">'+value+'</label></td> \n';
	    	html += '	<td><input type="text" name="custom_'+name+'" id="custom_'+name+'" /></td> \n';
	    	html += '</tr> \n';
			});
			$('#customEntries').html(html);
*/
		});
	},
	fetchAcnttranTypes: function () {
	  $.getJSON(mf.url,{mode:'getAcntTranTypes'}, function(jsonData){
	  	mf.tranType = jsonData;
	    var html = '';
	    $.each(jsonData, function (name, value) {
	    	html += '<option value="'+name+'">'+value+'</option> \n';
			});
			$('#transaction_type_cd').html(html);
		});
	},
	
	//------------------------------
	doBarCdSearch: function () {
		var barcd = $.trim($('#searchByBarcd').val());
		barcd = flos.pad(barcd,mf.opts.mbr_barcode_width,'0');
		$('#searchByBarcd').val(barcd); // redisplay expanded value
		
	  mf.srchType = 'barCd';
	  var params = 'mode=doBarcdSearch&barcdNmbr='+barcd;
	  $.get(mf.url,params, mf.handleMbrResponse);
		return false;
	},
	
	doNameSearch: function () {
	  var params = {'mode':'doNameFragSearch', 'nameFrag':$('#nameFrag').val()};
	  $.get(mf.url,params, function (jsonInpt) {
			mf.mbrs = $.parseJSON(jsonInpt);
			var html = '';
			for (var nMbr in mf.mbrs) {
				var mbr = mf.mbrs[nMbr];
				html += '<tr>\n';
				html += '	<td>'+mbr.barcode_nmbr+'</td>\n';
				html += '	<td><a href="#" id="'+mbr.mbrid+'">'+mbr.last_name+', '+mbr.first_name+'</a></td>\n';
				html += '	<td>'+mbr.home_phone+'</td>\n';
				html += '</tr>\n';
			}
			$('#srchRslts').html(html);
	    $('#searchDiv').hide();
		  $('#listDiv').show();
			$('#srchRslts tr:odd td').addClass('altBG');
			$('#srchRslts tr:even td').addClass('altBG2');	
			$('#srchRslts a').on('click',null,function (e) {
				e.preventDefault(); e.stopPropagation();
				mf.mbrid = e.target.id;
				mf.doFetchMember();
		  	$('#listDiv').hide();
			});			
		});
	},
	
	doFetchMember: function () {
	  var params = 'mode=doGetMbr&mbrid='+mf.mbrid;
	  $.get(mf.url,params, mf.handleMbrResponse);
		return false;
	},
	handleMbrResponse: function (jsonInpt) {
			if ($.trim(jsonInpt).substr(0,1) != '{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.mbr = $.parseJSON(jsonInpt);
				if (mf.mbr == null) {
	  			$('#msgArea').html('<?php echo T("Nothing Found") ?>');
				}
				else {
					mf.multiMode = false;
					mf.showOneMbr(mf.mbr)
				}
	    }
		  $('#searchDiv').hide();
	    $('#mbrDiv').show();
	},
	
	//------------------------------
	showOneMbr: function (mbr) {
		$('#mbrName').val(mbr.last_name+', '+mbr.first_name);
		$('#mbrSite').val(mbr.siteid);
		$('#mbrCardNo').val(mbr.barcode_nmbr);
		mf.mbrid = mbr.mbrid;
		mf.doGetCheckOuts(mf.mbrid);
	},
	doGetCheckOuts: function () {
	  var params = 'mode=getChkOuts&mbrid='+mf.mbrid;
	  $.get(mf.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,2) != '[{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.cpys = $.parseJSON(jsonInpt);
				if (! mf.cpys) {
	  			$('#msgArea').html('<?php echo T("Nothing Found") ?>');
					$('#msgDiv').show();
				}
				else {
					var html = '';
					for (var nCpy in mf.cpys) {
						var cpy = mf.cpys[nCpy];
						var outDate = cpy.booking.out_dt.split(' ')[0];
						var dueDate = cpy.booking.due_dt.split(' ')[0];
						html += '<tr>'
						html += '	<td>'+outDate+'</td>';
						html += '	<td><img src="'+cpy.material_img_url+'" />'+cpy.material_type+'	</td>\n';
						html += '	<td>'+cpy.barcode_nmbr+'</td>';
						html += '	<td><a href="#" id="'+cpy.bibid+'">"'+cpy.title+'"</a></td>';
						html += '	<td>'+dueDate+'</td>';
						html += '	<td class="number">'+cpy.booking.days_late+'</td>';
						html += '</tr>\n';
					}
					mf.nmbrOnloan = nCpy+1;
					$('#chkOutList tBody').html(html);
					$('table tbody.striped tr:odd td').addClass('altBG');
					$('table tbody.striped tr:even td').addClass('altBG2');	
					$('#chkOutList a').on('click',null,function (e) {
						e.preventDefault(); e.stopPropagation();
						idis.init(mf.opts); // be sure all is ready	
						idis.doBibidSearch(e.target.id);
						$('#biblioDiv').show();
						$('#mbrDiv').hide();					
					});			
				}
	    }
		});
		mf.doGetHolds();
	},
	doGetHolds: function () {
	  var params = 'mode=getHolds&mbrid='+mf.mbrid;
	  $.get(mf.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,2) != '[{') {
				$('#errSpace').html(jsonInpt).show();
			} else {
				mf.holds = $.parseJSON(jsonInpt);
				if (! mf.holds) {
	  			$('#msgArea').html('<?php echo T("Nothing Found") ?>');
					$('#msgDiv').show();
				}
				else {
					var html = '';
					for (var nHold in mf.holds) {
						var hold = mf.holds[nHold];
						var holdDate = hold.hold_begin_dt.split(' ')[0];
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
						html += '	<td>'+hold.barcode_nmbr+'</td>';
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
	  $.get(mf.url,params, function(jsonInpt){
			if ($.trim(jsonInpt).substr(0,2) != '[{') {
				//$('#msgArea').html(jsonInpt);
				$('#msgDiv').show();
			} else {
				mf.trans = $.parseJSON(jsonInpt);
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
	doTransAdd: function () {
		$('#acntMbrid').val(mf.mbrid);
		var parms = $('#acntForm').serialize();
		//console.log('adding: '+parms);
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('Added!');
				$('#msgDiv').show().hide(10000);
				mf.doShowMbrAcnt();
			}
		});
	},
	doDelAcntTrans: function () {
		if (!confirm(mf.delConfirmMsg+' this transaction?')) return false;

  	var parms = {	'mode':'d-3-L-3-tAcntTrans', 'mbrid':mf.mbrid, 'transid':mf.transid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('transaction deleted!');
				$('#msgDiv').show().hide(10000);
		  	mf.rtnToMbr();
			}
		});
	},
	
	//------------------------------
	doCheckout: function () {
		var barcd = $.trim($('#ckoutBarcd').val());
		barcd = flos.pad(barcd,mf.opts.item_barcode_width,'0');
		$('#ckoutBarcd').val(barcd); // redisplay expanded value

		var parms = {'mode':'doCheckout', 'mbrid':mf.mbrid, 'barcodeNmbr':barcd};
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response) {
					$('#msgArea').html(response);
					$('#msgDiv').show();
				} else {
					$('#msgArea').html('Checkout Completed!');
					$('#msgDiv').show().hide(10000);
					$('#ckoutBarcd').val('')
					mf.showOneMbr(mf.mbr)
				}
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
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response) {
					$('#msgArea').html(response);
					$('#msgDiv').show();
				} else {
					$('#msgArea').html('Hold Completed!');
					$('#msgDiv').show().hide(10000);
					$('#holdBarcd').val('')
					mf.showOneMbr(mf.mbr)
				}
			}
		});
		return false;
	},
	doDelHold: function (event) {
		var $delBtn = $(event.target);
		$delBtn.parent().parent().addClass('hilite');
		if (!confirm(mf.delConfirmMsg+' this hold?')) return false;
		
		var holdid = $delBtn.next().val();
  	var parms = {	'mode':'d-3-L-3-tHold', 'mbrid':mf.mbrid, 'holdid':holdid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('hold deleted!');
				$('#msgDiv').show().hide(10000);
				mf.showOneMbr(mf.mbr)
			}
		});
	},
	
	//------------------------------
	doShowMbrHist: function () {
		$('#mbrDiv').hide();
		$('#histDiv').show();
	},
	
	//------------------------------
	doShowMbrDetails: function () {
		var mbr = mf.mbr;
		$('#addMbrBtn').hide();
		$('#updtMbrBtn').show().enable();
		$('#deltMbrBtn').show().enable();
		$('.gobkUpdtBtn').show();
		$('#editHdr').html('<?php echo T("Edit Member Info"); ?>');
		$('#editMode').val('updateMember');

		$('#mbrid').val(mbr.mbrid);
		$('#siteid').val(mbr.siteid);
		$('#barcd_nmbr').val(mbr.barcode_nmbr);
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

		$.each(mf.cstmFlds, function (name, value) {
			//console.log(name+'==>'+value);		
			$('#custom_'+name).val(mbr[name]);
		});
			
		$('#msgArea').html('Updated!');
		$('#mbrDiv').hide();
		$('#editDiv').show();
	},
	doShowMbrAdd: function () {
		$('#addMbrBtn').show();
		$('#updtMbrBtn').hide();
		$('#deltMbrBtn').hide();
		$('.gobkNewBtn').show();
		
		$('#searchDiv').hide();
		$('#editHdr').html('<?php T("Add New Member"); ?>');
		$('#editMode').val('addNewMember');
		$('#msgArea').html('Added!');
		$('#editDiv').show();
	},
	
	doMbrAdd: function () {
		$('#msgDiv').hide();
		var parms = $('#editForm').serialize();
		$.post(mf.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html('<?php echo T("Added");?>');
					$('#updateMsg').show();
				}
				$('#msgArea').html('Added!');
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
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				if (response.substr(0,1)=='1'){
					$('#updateMsg').html('<?php echo T("Updated");?>');
					$('#updateMsg').show();
				}
				//$('#msgArea').html('Updated!');
				$('#msgDiv').show().hide(10000);
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
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('member deleted!');
				$('#msgDiv').show().hide(10000);
		  	mf.rtnToSrch();
			}
		});
	},
};
$(document).ready(mf.init);

</script>

