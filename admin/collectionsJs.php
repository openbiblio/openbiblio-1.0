<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// JavaScript Document
col = {
	<?php
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("List of Collections")."',\n";
		echo "editHdr: '".T("Edit Collection")."',\n";
		echo "newHdr: '".T("Add New Collection")."',\n";
	?>
	
	init: function () {
		col.initWidgets();

		col.url = 'adminSrvr.php';
		col.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('.newBtn').bind('click',null,col.doNewCollection);
		$('#addBtn').bind('click',null,col.doAddCollection);
		$('#updtBtn').bind('click',null,col.doUpdateCollection);
		$('#cnclBtn').bind('click',null,col.resetForms);
		$('#deltBtn').bind('click',null,col.doDeleteCollection);
	  $('#type').bind('change',null,function () {
	  	//Collection['type'] = $('#type').val();
			col.setTypeDisplay();
		});

		col.resetForms()
	  $('#msgDiv').hide();
		col.fetchTypes(); // will chain to fetchCircList(), fetchDistList() & fetchCollections()
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#listHdr').html(col.listHdr);
	  $('#CollectionHdr').html(col.editHdr);
		$('#editDiv').hide();
		$('#listDiv').show();
    $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		$('#msgDiv').hide(10000);
		col.resetForms();
		col.fetchCollections();
	},
	
	//------------------------------
	fetchTypes: function () {
	  $.getJSON(col.url,{ 'cat':'collect', 'mode':'getTypes' }, function(data){
			var html = '';
			for (item in data) {
				//console.log(data[item]);
	    	html += '<option value="'+item+'"';
    		html += '">'+item+'</option>\n';
			}
			$('#type').html(html);
			col.fetchCircList();
		});
	},
	fetchCircList: function () {
	  $.getJSON(col.url,{ 'cat':'collect', 'mode':'getCircList' }, function(data){
	  	col.circList = data;
	  	col.fetchDistList();
		});
	},
	getCirc: function (code) {
		for (item in col.circList) {
			if (col.circList[item]['code'] == code) {
				return col.circList[item];
			}
		}
	},
	fetchDistList: function () {
	  $.getJSON(col.url,{ 'cat':'collect', 'mode':'getDistList' }, function(data){
	  	col.distList = data;
	  	col.fetchCollections();
		});
	},
	getDist: function (code) {
		for (item in col.distList) {
			if (col.distList[item]['code'] == code) {
				return col.distList[item];
			}
		}
	},
	formatType: function (data) {
		// FIXME - i18n
		var str = data.type+': ';
		switch (data.type) {
			case 'Circulated':
				var circ = col.getCirc(data.code);
				str += circ.days_due_back + <?php echo "'".T("days")."'"; ?>;
				str += ' @ $' + circ.daily_late_fee + '/' + <?php echo "'".T("day")." '"; ?>;
				break;
			case 'Distributed':
				var dist = col.getDist(data['code']);
				str += <?php echo "'".T("Restock at ")."'"; ?> + dist['restock_threshold'];
				break;
			default:
				str += '???';
		};
		return str;
	},
	fetchCollections: function () {
	  $.getJSON(col.url,{ 'cat':'collect', 'mode':'getAllCollections' }, function(data){
	    col.json = data;
			$('#showList').html('');
			for (var item=0; item<data.length; item++) {
    		var typeStr = col.formatType(data[item]);
	    	html = '<tr>\n'
    					+'<td valign="top">\n'
							+'	<input type="button" class="editBtn" value="edit" />\n'
							+'	<input type="hidden" value="'+data[item]['code']+'"  />\n'
							+'</td>\n'
    					+'<td valign="top">'+data[item]['description']+'</td>\n'
    					+'<td valign="top">'+typeStr+'</td>\n'
    					+'<td valign="top" class="center">'+data[item]['default_flg']+'</td>\n'
    					+'<td valign="top" class="number">'+data[item]['count']+'</td>\n'
							+'</tr>\n';	
				$('#showList').append(html);
			}
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
			$('.editBtn').bind('click',null,col.doEdit);
		});
	},

	doEdit: function (e) {
	  var code = $(e.target).next().val();
		for (item in col.json) {
		  if (col.json[item]['code'] == code) {
		  	col.choice = col.json[item];
				col.showCollection();
			}
		}
	},
	
	setTypeDisplay: function () {
		var type = $('#type').val();
		if (type == 'Circulated') {
			$('.distOnly').hide();
			$('.circOnly').show();
		}
		else if (type == 'Distributed') {
			$('.circOnly').hide();
			$('.distOnly').show();
		}
		else {
			$('#msgArea').html('Invalid Collection Type');
			$('#msgDiv').show();
		}
	},
	showCollection: function () {
	  $('#editHdr').html(col.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#description').focus();
	  
		var Collection = col.choice;
	  if (Collection['count'] < 1) 
			$('#deltBtn').show(); 
		else 
			$('#deltBtn').hide();
	  if (Collection['type'] == 'Circulated') {
			var typeData = col.getCirc(Collection['code']);
	  } else {
			var typeData = col.getDist(Collection['code']);
		}
		col.setTypeDisplay();

		$('#description').val(Collection['description']);
		$('#code').val(Collection['code']);
		$('#type').val(Collection['type']);
		$('#onHand').html(Collection['count']);
		$('#default_flg').val(Collection['default_flg']);
		$('#days_due_back').val(typeData['days_due_back']);
		$('#daily_late_fee').val(typeData['daily_late_fee']);
		$('#restock_threshold').val(typeData['restock_threshold']);

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewCollection: function (e) {
	  document.forms['editForm'].reset();
	  $('#editHdr').html(col.newHdr);
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('.distOnly').val('n/a').hide();
		$('.circOnly').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('description').focus();
	  return false; // prevent normal 'submit' action'
	},
	
	doAddCollection: function () {
		$('#mode').val('addNewCollection');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(col.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('response');
				$('#msgDiv').show();
			  col.doBackToList();
			}
		});
		return false;
	},

	doUpdateCollection: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateCollection');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(col.url, parms, function(response) {
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
				$('#msgDiv').show();
			  col.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteCollection: function (e) {
		var msg = col.delConfirmMsg+'\n>>> '+$('#description').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = {	'cat':'collect', 'mode':'d-3-L-3-tCollections', 'code':$('#code').val() };
	  	$.post(col.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html(response);
					$('#msgDiv').show();
			  	col.doBackToList();
				}
			});
		}
		return false;
	},
};

$(document).ready(col.init);
</script>
