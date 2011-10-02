<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>

<script language="JavaScript" defer>
// JavaScript Document
//------------------------------------------------------------------------------

var opacMode = true;

mf = {
	<?php
	if ($_SESSION['mbrBarcode_flg'] == 'Y') 
		echo "showBarCd: true, \n";
	else
		echo "showBarCd: false, \n ";

	echo "delConfirmMsg: '".T("Are you sure you want to delete ")."', \n";
	echo "editHdr: '".T("Edit Member Info")."', \n";
	echo "newHdr: '".T("Add New Member")."', \n";
	?>
	multiMode: false,
	
	init: function () {
		mf.url = 'memberServer.php';

		// get header stuff going first
		mf.initWidgets();
		mf.resetForms();
		mf.fetchOpts();
		mf.fetchCustomFlds();
		
		$('form').bind('submit',null,mf.doSubmits);
		$('.gobkBtn').bind('click',null,mf.rtnToSrch)
		$('.gobkBiblioBtn').bind('click',null,mf.rtnToMbr)
		$('#mbrDetlBtn').bind('click',null,mf.doShowMbrDetails);
		$('#deltMbrBtn').bind('click',null,mf.doDeleteMember);
		$('#cnclMbrBtn').bind('click',null,function(){
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
	  $('#searchDiv').show();
	  $('#listDiv').hide();
	  $('#mbrDiv').hide();
	  $('#biblioDiv').hide();
	  $('#editDiv').hide();
		$('#msgDiv').hide();
	},
	rtnToSrch: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  mf.resetForms();
	},
	rtnToMbr: function () {
	  $('#rsltMsg').html('');
	  $('#editRsltMsg').html('');
	  $('#searchDiv').hide();
	  $('#listDiv').hide();
	  $('#mbrDiv').show();
	  $('#biblioDiv').hide();
	  $('#editDiv').hide();
	},

	doSubmits: function (e) {
		e.preventDefault();
		e.stopPropagation();
		var theId = $('input[type="submit"]:focus').attr('id');
		//console.log('the btn id is: '+theId);
		switch (theId) {
			case 'barCdSrchBtn':	mf.doBarCdSearch();	break;
			case 'nameSrchBtn':		mf.doNameSearch();	break;
			case 'addMbrBtn':			mf.doMbrAdd();			break;
			case 'updtMbrBtn':		mf.doMbrUpdate();		break;
		}
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
			var html = '';
			$.each(mf.cstmFlds, function (name, value) {
				//console.log(data[item]);
	    	html += '<tr> \n';
	    	html += '	<td><label for="custom_'+name+'">'+value+'</label></td> \n';
	    	html += '	<td><input type="text" name="custom_'+name+'" id="custom_'+name+'" /></td> \n';
	    	html += '</tr> \n';
			});
			$('#customEntries').html(html);
		});
	},
	
	//------------------------------
	doBarCdSearch: function () {
		var barcd = $.trim($('#searchByBarcd').val());
		barcd = flos.pad(barcd,mf.opts.mbr_barcode_width,'0');
		$('#searchByBarcd').val(barcd); // redisplay expanded value
		
	  mf.srchType = 'barCd';
	  $('p.error').html('').hide();
	  var params = 'mode=doBarcdSearch&barcdNmbr='+barcd;
	  $.get(mf.url,params, mf.handleMbrResponse);
		return false;
	},
	
	doNameSearch: function () {
console.log('you clicked nameSrch');
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
				  var msgTxt =
	  			$('#rsltMsg').html('<?php echo T('Nothing Found') ?>').show();
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
				  var msgTxt =
	  			$('#rsltMsg').html('<?php echo T('Nothing Found') ?>').show();
				}
				else {
					var html = '';
					for (var nCpy in mf.cpys) {
						var cpy = mf.cpys[nCpy];
						var outDate = cpy.booking.out_dt.split(' ')[0];
						var dueDate = cpy.booking.due_dt.split(' ')[0];
						html += '<tr>'
						html += '<td>'+outDate+'</td>';
						html += '<td>'
								 +		'<img src="'+cpy.material_img_url+'" />'
								 +		cpy.material_type
								 +'	</td>';
						html += '<td>'+cpy.barcode_nmbr+'</td>';
						html += '<td><a href="#" id="'+cpy.bibid+'">"'+cpy.title+'"</a></td>';
						html += '<td>'+dueDate+'</td>';
						html += '<td class="number">'+cpy.booking.days_late+'</td>';
						html += '</td>\n';
					}
					mf.nmbrOnloan = nCpy+1;
					$('#chkOutList tBody').html(html);
					$('table tbody.striped tr:odd td').addClass('altBG');
					$('table tbody.striped tr:even td').addClass('altBG2');	
					$('#chkOutList a').bind('click',null,function (e) {
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
				  var msgTxt =
	  			$('#rsltMsg').html('<?php echo T('Nothing Found') ?>').show();
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
						html += '<td>&nbsp;</td>';
						html += '<td>'+holdDate+'</td>';
						html += '<td>'+hold.barcode_nmbr+'</td>';
						html += '<td><a href="#" id="'+hold.bibid+'">"'+hold.title+'"</a></td>';
						html += '<td>'+hold.status+'</td>';
						html += '<td>'+dueDate+'</td>';
						html += '</td>\n';

					}
					$('#holdList tBody').html(html);
					$('table tbody.striped tr:odd td').addClass('altBG');
					$('table tbody.striped tr:even td').addClass('altBG2');
					$('#holdList a').bind('click',null,function (e) {
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
	doShowMbrDetails: function () {
		var mbr = mf.mbr;
		$('#addMbrBtn').hide();
		$('#updtMbrBtn').enable();
		$('#deltMbrBtn').enable();

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
			$('#custom_'+name).val(mbr[name]);
		});
			
		$('#mbrDiv').hide();
		$('#editDiv').show();
	},
	doMbrUpdate: function () {
		$('#editHdr').html(mf.editHdr);
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateMember');
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
					$('#updateMsg').html('<?php echo T('Updated');?>');
					$('#updateMsg').show();
				}
				$('#msgArea').html('Updated!');
				$('#msgDiv').show().hide(10000);
			}
		});
		$('#updtMbrBtn').disable();
		$('#deltMbrBtn').disable();
		return false;
	},
	doDeleteMember: function () {
		if (mf.nmbrOnloan > 0) {
			alert('<?php echo T('You must settle all outstanding loans before deleting a member.'); ?>');
			return false;
		}
		if (!confirm(' '+mf.delConfirmMsg+ mf.mbr.first_name+' '+mf.mbr.last_name+'?')) return false;

  	var parms = {	'mode':'d-3-L-3-tMember', 'mbrid':mf.mbrid };
  	$.post(mf.url, parms, function(response){
			if (($.trim(response)).substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('deleted!');
				$('#msgDiv').show().hide(10000);
		  	mf.rtnToSrch();
			}
		});
	},
};
$(document).ready(mf.init);

</script>

