<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Strt {
    constructor ( ) {
        this.initWidgets();

        this.url = '../install/startupSrvr.php';
        this.dest = '../install/index.php';

        $('#constBtn').on('click',null, function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.doCreateConstFile();
        });
        $('#newDbBtn').on('click',null, function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.createDb();
        });
        $('#skipBtn').on('click',null,function() {
            $('#createDB').hide();
            $('#continue').show();
        });
        $('#contBtn').on('click',null,this.doContinue);
        $('#restartBtn').on('click',null, this.resetForms);
        $( document ).ajaxError(function() { $( ".log" ).text( "Triggered ajax Error handler." ); });
        this.resetForms();
$('#dbEngine').on('change',null, function () {
console.log($('#dbEngine').val());
});
    };

    //------------------------------
    initWidgets () {
    };
    resetForms () {
        //console.log('resetting!');
        $('#plsWait').hide();
        $('#const_editor').show();
        $('#createDB').hide();
        $('#continue').hide();
        $('#hostId').focus();
    };
    informUser (msg) {
        var html = '<li>'+msg+'</li>';
        $('#progressList').append(html)
    };
    showWait (msg) {
        $('#waitMsg').html(msg);
        $('#plsWait').show();
    };

    //------------------------------
    doCreateConstFile () {
        this.showWait('Creating file');
        this.informUser('Creating new database constant file');

        this.dbEngine = $('#dbEngine').val();
        this.host = $('#hostId').val();
        this.user = $('#userNm').val();
        this.pw = $('#passWd').val();
        this.db = $('#dbName').val();
        var params = "mode=createConstFile&host="+this.host+"&dbEngine="+this.dbEngine+"&user="+this.user+"&passwd="+this.pw+"&db="+this.db;

        $.post(this.url, params, function (response) {
            $('#plsWait').hide();
            if (response.hasOwnProperty('error')) {
              $.each( response, function( key, val ) { this.informUser(val); });
            } else  {
              this.informUser('A new database_constant file has been created');
              $('#const_editor').hide();
              $('#createDB').show();
              $('#adminNm').focus();
            }
        }, 'json');
    };

    createDb () {
        this.showWait('Creating file');
        this.informUser('Creating new database constant file');

        var adminNm = $('#adminNm').val();
        var adminPw = $('#adminPw').val();
        var params = "mode=createDatabase"+
                     "&host="+this.host+
                     "&user="+this.user+
                     "&passwd="+this.pw+
                     "&db="+this.db+
                     "&adminNm="+adminNm+
                     "&adminPw="+adminPw;

        $.post(this.url, params, function (response) {
            $('#plsWait').hide();
            if (response.indexOf('Error:') >= 0) {
              this.informUser('<p class="error">'+response+'</p>');
            } else if (response.indexOf('success') >= 0) {
              this.informUser('A new database has been created');
              $('#createDB').hide();
              $('#continue').show();
            }
        });
    };

    doContinue () {
        window.location=this.dest;
    };

}

$(document).ready(function () {
	var strt = new Strt();
});
</script>
