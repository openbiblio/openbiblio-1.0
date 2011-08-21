<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document

ins = {
	<?php
		echo "crntDbVer:'".OBIB_LATEST_DB_VERSION."',\n";
		//echo "listHdr: '".T("List of Media Types")."',\n";
		//echo "editHdr: '".T("Edit Media")."',\n";
		//echo "newHdr: '".T("Add New Media")."',\n";
	?>
	
	init: function () {
		ins.initWidgets();

		ins.url = 'installSrvr.php';
		ins.editForm = $('#editForm');

		//$('#reqdNote').css('color','red');
		//$('.reqd sup').css('color','red');
		//$('#updateMsg').hide();

		$('#newForm #newBtn').bind('click',null,ins.doNewInstall);
		//$('#addBtn').bind('click',null,ins.doAddMedia);
		//$('#updtBtn').bind('click',null,ins.doUpdateMedia);
		//$('#cnclBtn').bind('click',null,ins.resetForms);
		//$('#deltBtn').bind('click',null,ins.doDeleteMedia);

		ins.resetForms()
	  $('#msgDiv').hide();
		ins.connectDb();
	},
	
	//------------------------------
	initWidgets: function () {
	},
	resetForms: function () {
		//console.log('resetting!');
		$('#plsWait').hide();
		$('#dbPblms').hide();
		$('#versionOK').hide();
		$('#newInstall').hide();
		$('#updateDB').hide();
		$('#startOB').hide();
	  //$('#listHdr').html(ins.listHdr);
	  //$('#mediaHdr').html(ins.editHdr);
		//$('#listDiv').show();
    //$('#cnclBtn').val('Cancel');
	},
//	doBackToList: function () {
		//$('#msgDiv').hide(10000);
		//ins.resetForms();
		//ins.fetchMedia();
//	},
	
	//------------------------------
	connectDb: function () {
		console.log('connecting to db');	
	  $.get(ins.url,{ 'mode':'connectDB'}, function(response){	  
	  	if (response.indexOf('OK') == -1) {
				console.log('no connection')
				$('#connectErr').html(response);
				$('#dbPblms').show();
			}
			else {
				//console.log('good connection')
				ins.getDbVersion();
			}
	  });
	},
	getDbVersion: function () {
		console.log('getting db version');	
	  $.get(ins.url,{ 'mode':'getDbVersion'}, function(response){
			//console.log('vers='+response);	  
			if (response == 'noDB') {
				ins.getLocales();
			}
	  	else if (response == '<?php echo H(OBIB_LATEST_DB_VERSION); ?>') {
				//console.log("Database tables are current version.");			
				$('#versionOK').show();
			}
			else {
				$('#updateDB').show();
			}

	  });
	},
	getLocales: function () {
		//console.log('getting locales');	
	  $.get(ins.url,{ 'mode':'getLocales'}, function(response){
			$('#locale').html(response);  
			$('#newInstall').show();
		});
	},
	
	//------------------------------
	doNewInstall: function () {
		var useTestData = $('#installTestData').prop('checked');
		if (useTestData) {
			console.log("installing with test data");
			var test = $('#installTestData').val();
		} else {
			console.log("installing without test data");
			var test = 'NO';
		}
			
		$.post(ins.url, {'mode':'doFullInstall', 'installTestData':test}, function (response) {
			console.log('OpenBiblio tables have been created successfully!');
			$('#newInstall').hide();
			$('#startOB').show();		
			return false;
		});
		return false;
	},
	
};

$(document).ready(ins.init);
</script>
