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
		ins.listSrvr = '..\shared\listSrvr.php';
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
		ins.showWait('<?php echo T("Testing Database connection"); ?>');
	  $.get(ins.url,{ 'mode':'connectDB'}, function(response){	  
			$('#plsWait').hide();
			ins.informUser('<?php echo T("Connected to MySQL version"); ?> '+response);
			ins.dbTest();
	  });
	},
	dbTest: function () {
		ins.informUser('<?php echo T("Looking for Database tables"); ?>');
		ins.showWait('<?php echo T("Checking for Database Content"); ?>');
	  $.get(ins.url,{ 'mode':'getSettings'}, function(response){
	  	if (response == 'noTbl') {
				$('#newInstall').show();
			} else {
				$('#plsWait').hide();
				ins.getDbVersion();
			}
	  });
	},
	getDbVersion: function () {
		ins.informUser('<?php echo T("Looking for Database Version"); ?>');
		ins.showWait('<?php echo T("Checking Database Version"); ?>');
	  $.get(ins.url,{ 'mode':'getDbVersion'}, function(response){
			$('#plsWait').hide();
			//console.log('vers='+response);
			if (response == 'noDB') {
				ins.informUser('<?php echo T("Database not found"); ?>');
				ins.getLocales();
			} else if (response == '<?php echo H(OBIB_LATEST_DB_VERSION); ?>') {
				ins.informUser(response+', '+'<?php echo T("DatabaseUpToDate"); ?>');
				$('#versionOK').show();
			} else {
				ins.informUser(response+', '+'<?php echo T("Database needs upgrading"); ?>');
				ins.startVer = response;
				$('#verTxt').html(response);
				$('#updateDB').show();
			}
	  });
	},
	getLocales: function () {
		ins.showWait('<?php echo T("Fetching Locales"); ?>');
		ins.informUser('<?php echo T("Fetching list of available languages"); ?>');
	  $.getJson(ins.listSrvr,{ 'mode':'getLocales'}, function(response){
			$('#plsWait').hide();
			$('#locale').html(response);
			$('#newInstall').show();
		});
	},
	
	//------------------------------
	doNewInstall: function () {
		ins.showWait('<?php echo T("Installing Tables"); ?>');
		var useTestData = $('#installTestData').prop('checked');
		if (useTestData) {
			ins.informUser('<?php echo T("Installing DB tables with test data"); ?>');
			var test = $('#installTestData').val();
		} else {
			ins.informUser('<?php echo T("Installing DB tables without test data"); ?>');
			var test = 'NO';
		}
		$.post(ins.url, {'mode':'doFullInstall',
										 'installTestData':test,
										},
			function (response) {
				$('#plsWait').hide();
				if(response) {
					$('#connectErr').html(response);
					$('#dbPblms').show();
				} else {
					ins.informUser('<?php echo T("Table installation complete"); ?>');
					$('#newInstall').hide();
					$('#startOB').show();
					return false;
				}
		});
		return false;
	},
	
	//------------------------------
	doDbUpdate: function () {
		ins.showWait('<?php echo T("Updating Tables"); ?>');
		ins.informUser('<?php echo T("Updating Database Tables"); ?>');
		$.post(ins.url, {'mode':'doDbUpgrade','startVer':ins.startVer,}, function (response) {
			$('#plsWait').hide();
			if ((response.indexOf('!-!') >= 0) || (response.indexOf('error') >= 0)) {
				ins.informUser('<p class="error">'+response+'</p>');
				$('#updateDB').hide();
			} else {
				ins.informUser('<?php echo T("Table update complete"); ?>');
				$('#updateDB').hide();
				$('#startOB').show();
			}
			return false;
		});
		return false;
	},
			
};

$(document).ready(ins.init);
</script>
