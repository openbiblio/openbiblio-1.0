<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

strt = {
    init: function () {
        strt.initWidgets();

        strt.url = 'startupSrvr.php';
        strt.dest = 'index.php';

        $('#constBtn').on('click',null, function (e) {
                e.preventDefault();
                e.stopPropagation();
                strt.doCreateConstFile();
            });
        $('#contBtn').on('click',null,strt.doContinue);
        $( document ).ajaxError(function() { $( ".log" ).text( "Triggered ajax Error handler." ); });
        strt.resetForms()
    },

    //------------------------------
    initWidgets: function () {
    },
    resetForms: function () {
        //console.log('resetting!');
        $('#plsWait').hide();
        $('#continue').hide();
        $('#const_editor').show();
        $('#hostId').focus();
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
    doCreateConstFile: function() {
        strt.showWait('Creating file');
        strt.informUser('Creating new database constant file');

        strt.host = $('#hostId').val();
        strt.user = $('#userNm').val();
        strt.pw = $('#passWd').val();
        strt.db = $('#dbName').val();
        var params = "mode=createConstFile&host="+strt.host+"&user="+strt.user+"&passwd="+strt.pw+"&db="+strt.db;

        $.post(strt.url, params, function (response) {
            $('#plsWait').hide();
            if (response.indexOf('Error:') >= 0) {
              strt.informUser('<p class="error">'+response+'</p>');
            } else if (response.indexOf('success') >= 0) {
              strt.informUser('A new database_constant file has been created');
              $('#const_editor').hide();
              $('#continue').show();
            }
        });
    },
    doContinue: function() {
        window.location=strt.dest;
    },
};

$(document).ready(strt.init);
</script>
