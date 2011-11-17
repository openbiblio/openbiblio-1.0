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

		$('#newBtn').on('click',null,ins.doNewInstall);
		$('#updtBtn').on('click',null,ins.doDbUpdate);

		ins.resetForms()
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
	},
	
	informUser: function (msg) {
		var html = '<li>'+msg+'</li>';
		$('#progressList').append(html)
	},
	showWait: function (msg) {
		$('#waitMsg').html(msg);
		$('#plsWait').show();
	},
	
	//------------------------------
	connectDb: function () {
		ins.informUser('<?php echo T("Testing Connection to DB server"); ?>');
		ins.showWait('Testing Database connection');
	  $.get(ins.url,{ 'mode':'connectDB'}, function(response){	  
	  	if (response.indexOf('OK') == -1) {
				console.log('no connection')
				ins.informUser('<?php echo T("Connection to DB server failed"); ?>');
				$('#connectErr').html(response);
				$('#dbPblms').show();
			}
			else {
				//console.log('good connection')
				ins.informUser('<?php echo T("Connection to DB server OK"); ?>');
				ins.getDbVersion();
			}
			$('#plsWait').hide();
	  });
	},
	getDbVersion: function () {
		ins.informUser('<?php echo T("Looking for Database"); ?>');
		ins.showWait('Checking Database');
	  $.get(ins.url,{ 'mode':'getDbVersion'}, function(response){
			//console.log('vers='+response);	  
			if (response == 'noDB') {
				ins.informUser('<?php echo T("Database not found"); ?>');
				ins.getLocales();
			}
	  	else if (response == '<?php echo H(OBIB_LATEST_DB_VERSION); ?>') {
				ins.informUser('<?php echo T("Database is uptodate"); ?>');
				$('#versionOK').show();
			}
			else {
				ins.informUser('<?php echo T("Database needs upgrading"); ?>');
				$('#verTxt').html(response);
				$('#updateDB').show();
			}
			$('#plsWait').hide();
	  });
	},
	getLocales: function () {
		ins.showWait('Fetching Locales');
		ins.informUser('<?php echo T("Fetching list of available languages"); ?>');
	  $.get(ins.url,{ 'mode':'getLocales'}, function(response){
			$('#locale').html(response);  
			$('#newInstall').show();
			$('#plsWait').hide();
		});
	},
	
	//------------------------------
	doNewInstall: function () {
		ins.showWait('Installing Tables');
		var useTestData = $('#installTestData').prop('checked');
		if (useTestData) {
			ins.informUser('<?php echo T("Installing DB tables with test data"); ?>');
			var test = $('#installTestData').val();
		} else {
			ins.informUser('<?php echo T("Installing DB tables without test data"); ?>');
			var test = 'NO';
		}
			
		$.post(ins.url, {'mode':'doFullInstall', 'installTestData':test}, function (response) {
			ins.informUser('<?php echo T("Table installation complete"); ?>');
			$('#newInstall').hide();
			$('#startOB').show();		
			$('#plsWait').hide();
			return false;
		});
		return false;
	},
	
	//------------------------------
	doDbUpdate: function () {
		ins.showWait('Updating Tables');
		ins.informUser('<?php echo T("Updating Database Tables"); ?>');
		$.post(ins.url, {'mode':'doDbUpgrade'}, function (response) {
			ins.informUser('<?php echo T("Table update complete"); ?>');
			
			$('#updateDB').hide();
			$('#startOB').show();		
			$('#plsWait').hide();
			return false;
		});
		return false;
	},
			
};

$(document).ready(ins.init);
</script>
