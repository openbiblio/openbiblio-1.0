<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
//	$types = $mattypes->getAllWithStats();

med = {
	<?php
		echo "delConfirmMsg: '".T("Are you sure you want to delete ")."',\n";
		echo "listHdr: '".T("List of Media Types")."',\n";
		echo "editHdr: '".T("Edit Media")."',\n";
		echo "newHdr: '".T("Add New Media")."',\n";
	?>
	
	init: function () {
		med.initWidgets();

		med.url = 'adminSrvr.php';
		med.editForm = $('#editForm');

		$('#reqdNote').css('color','red');
		$('.reqd sup').css('color','red');
		$('#updateMsg').hide();

		$('#showForm .newBtn').bind('click',null,med.doNewMedia);
		$('#addBtn').bind('click',null,med.doAddMedia);
		$('#updtBtn').bind('click',null,med.doUpdateMedia);
		$('#cnclBtn').bind('click',null,med.resetForms);
		$('#deltBtn').bind('click',null,med.doDeleteMedia);

		med.resetForms()
	  $('#msgDiv').hide();
		med.fetchMedia();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
	  $('#listHdr').html(med.listHdr);
	  $('#mediaHdr').html(med.editHdr);
		$('#editDiv').hide();
		$('#listDiv').show();
    $('#cnclBtn').val('Cancel');
	},
	doBackToList: function () {
		$('#msgDiv').hide(10000);
		med.resetForms();
		med.fetchMedia();
	},
	
	//------------------------------
	fetchMedia: function () {
	  $.getJSON(med.url,{ 'cat':'media', 'mode':'getAllMedia'}, function(dataAray){
	    med.json = dataAray;
			var html = '';
			for (obj in dataAray) {
				var item = dataAray[obj];
	    	html += '<tr>\n';
    		html += '	<td valign="top">\n';
				html += '		<input type="button" class="editBtn" value="'+<?php echo "'".T("edit")."'"; ?>+'" />\n';
				html += '		<input type="hidden" value="'+item['code']+'"  />\n';
    		//html += '	 <input type="button" id="marcBtn" value="MARC Fields" />\n';//
	    	html += '	</td>\n';
    		html += '	<td valign="top">'+item['description']+'</td>\n';
    		html += '	<td valign="top" class="number">'+item['adult_checkout_limit']+'</td>\n';
    		html += '	<td valign="top" class="number">'+item['juvenile_checkout_limit']+'</td>\n';
				html += '	<td valign="top">'
							 +'		<img src="../images/'+item['image_file']+'" width="20" height="20" align="middle">'
							 + 		item['image_file'] + '</td>\n';							 
				html += ' <td valign="top" class="center">'+item['default_flg']+'</td>\n';
				html += '	<td valign="top"  class="number" >' + item['count'] + '</td>\n';
	    	html += '</tr>\n';
			}
			$('#showList tBody').html(html);

			$('.editBtn').bind('click',null,med.doEdit);
			/*$('#marcBtn').bind('click',null,med.doMarcFlds);*/
			$('table tbody.striped tr:odd td').addClass('altBG');
			$('table tbody.striped tr:even td').addClass('altBG2');
		});
	},

	doEdit: function (e) {
	  var code = $(e.target).next().val();
		//console.log('you wish to edit code: '+code);
		for (n in med.json) {
		  if (med.json[n]['code'] == code) {
				med.showMedia(med.json[n]);
			}
		}
		return false;
	},
	/*
	doMarcFlds: function (e) {
	  var code = $(e.target).prior().val();
		//console.log('you wish to edit code: '+code);
		for (n in med.json) {
		  if (med.json[n]['code'] == code) {
				med.showMedia(med.json[n]);
			}
		}
		return false;
	},
	*/
	
	showMedia: function (media) {
		//console.log('showing : '+media['description']);
	  $('#editHdr').html(med.editHdr);
	  $('#addBtn').hide();
	  $('#updtBtn').show();
	  $('#deltBtn').show();
	  $('#description').focus();
	  if (media['count'] < 1) 
			$('#deltBtn').show(); 
		else 
			$('#deltBtn').hide();

		$('#description').val(media['description']);
		$('#code').val(media['code']);
		$('#default_flg').val(media['default_flg']);
		$('#adult_checkout_limit').val(media['adult_checkout_limit']);
		$('#juvenile_checkout_limit').val(media['juvenile_checkout_limit']);
		$('#image_file').val(media['image_file']);

		$('#listDiv').hide();
		$('#editDiv').show();
	},
	
	doNewMedia: function (e) {
	  document.forms['editForm'].reset();
	  $('#editHdr').html(med.newHdr);
		$('#deltBtn').hide();
		$('#updtBtn').hide();
	  $('#addBtn').show();
		$('#listDiv').hide();
		$('#editDiv').show();
	  document.getElementById('description').focus();
		return false;
	},
	
	doAddMedia: function () {
		$('#mode').val('addNewMedia');
		var parms = $('#editForm').serialize();
		//console.log('adding: '+parms);
		$.post(med.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('Added!');
				$('#msgDiv').show();
			  med.doBackToList();
			}
		});
		return false;
	},

	doUpdateMedia: function () {
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#mode').val('updateMedia');
		var parms = $('#editForm').serialize();
		//console.log('updating: '+parms);
		$.post(med.url, parms, function(response) {
			if (response.substr(0,1)=='<') {
				//console.log('rcvd error msg from server :<br />'+response);
				$('#msgArea').html(response);
				$('#msgDiv').show();
			}
			else {
				$('#msgArea').html('response');
				$('#msgDiv').show();
			  med.doBackToList();
			}
		});
		return false;
	},
	
	doDeleteMedia: function (e) {
		var msg = med.delConfirmMsg+'\n>>> '+$('#description').val()+' <<<';
	  if (confirm(msg)) {
	  	var parms = { 'cat':'media', 'mode':'d-3-L-3-tMedia', 'code':$('#code').val(), 'description':$('#description').val() };
	  	$.post(med.url, parms, function(response){
				if (($.trim(response)).substr(0,1)=='<') {
					//console.log('rcvd error msg from server :<br />'+response);
					$('#msgArea').html(response);
					$('#msgDiv').show();
				}
				else {
					$('#msgArea').html('response');
					$('#msgDiv').show();
			  	med.doBackToList();
				}
			});
		}
		return false;
	},
};

$(document).ready(med.init);
</script>
