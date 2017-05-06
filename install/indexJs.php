<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document

ins = {
	init: function () {
		ins.initWidgets();

		ins.url = '../install/installSrvr.php';
		ins.listSrvr = '../shared/listSrvr.php';
		ins.editForm = $('#editForm');

		$('#newBtn').on('click',null,ins.doNewInstall);
		$('#updtBtn').on('click',null,ins.doDbUpdate);
		$('#startBtn').on('click',null,ins.startOB);

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
		ins.informUser('<?php echo T("TestingConnectionToDBServer"); ?>');
		ins.showWait('<?php echo T("TestingDatabaseConnection"); ?>');
        $.get(ins.url,{ 'mode':'connectDBServer'}, function(response){
			$('#plsWait').hide();
            if ((response.indexOf('Unknown') > 0 ) || (response.indexOf('Denied') > 0) || (response.indexOf('error') > 0)){
                ins.informUser('<?php echo T("Unable to connect to a MySQL server"); ?>' );
            } else {
                ins.informUser('<?php echo T("Connected to MySQL version"); ?> '+response);
                ins.dbTest();
            }
	    });
	},
	dbTest: function () {
		ins.informUser('<?php echo T("Looking for Database tables"); ?>');
		ins.showWait('<?php echo T("Checking for Database Content"); ?>');
	    $.get(ins.url,{ 'mode':'getSettings'}, function(response){
	  	    if (response.indexOf('noTbl') >= 0) {
						$('#newInstall').show();
						$('#plsWait').hide();
					} else {
						$('#plsWait').hide();
						ins.getDbVersion();
					}
	    });
	},
	getDbVersion: function () {
		ins.showWait('<?php echo T("CheckingDatabaseVersion"); ?>');
		ins.informUser('<?php echo T("Looking for Database Version"); ?>');
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
		ins.showWait('<?php echo T("FetchingLocales"); ?>');
		ins.informUser('<?php echo T("Fetching list of available languages"); ?>');
	    $.getJson(ins.listSrvr,{ 'mode':'getLocales'}, function(response){
			$('#plsWait').hide();
			$('#locale').html(response);
			$('#newInstall').show();
		});
	},
	
  //------------------------------
  doCreateDB: function () {
		ins.showWait('<?php echo T("Creating empty database"); ?>');
		ins.informUser('<?php echo T("Creating new empty database"); ?>');
        var params = "mode=createNewDB&db="+ins.db+"&user="+ins.user+"&passwd="+ins.pw;
		$.post(ins.url, params, function (response) {
			 $('#plsWait').hide();
			 if (response.indexOf('Error:') >= 0) {
			 	ins.informUser('<p class="error">'+response+'</p>');
			 } else if (response.indexOf('success') > 0) {
		        ins.informUser('<?php echo T("A new database has been created."); ?>');
			 	ins.informUser('-.-.-.-.-.-');
        $('#const_editor').hide();
		    ins.connectDb();
       }
		});
  },

	//------------------------------
	doNewInstall: function () {
		ins.showWait('<?php echo T("InstallingTables"); ?>');
		var useTestData = $('#installTestData').prop('checked');
		if (useTestData) {
			ins.informUser('<?php echo T("Installing DB tables with test data"); ?>');
			var test = $('#installTestData').val();
		} else {
			ins.informUser('<?php echo T("Installing DB tables without test data"); ?>');
			var test = 'NO';
		}
		$.post(ins.url, {'mode':'doFullInstall', 'installTestData':test },
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
		ins.showWait('<?php echo T("UpdatingTables"); ?>');
		ins.informUser('<?php echo T("Updating Database Tables"); ?>');
		//ins.startVer='0.7.1';
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

	//-------------------------------
	startOB: function() {
		$('#plsWait').show();
		$.post(ins.url, {'mode':'getStartPage', 'user':1}, function (response) {
console.log('switching control to'+response);
		}
        window.location = "../response";
	},
			
};

$(document).ready(ins.init);
</script>
