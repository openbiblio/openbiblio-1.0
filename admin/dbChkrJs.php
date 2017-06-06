<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Chkr {
    constructor () {
    	this.url = '../admin/adminSrvr.php';

        this.tab = $('#tab').val();
        this.rtnTo = $('#rtnTo').val();

    	this.initWidgets();
    	this.resetForms();

        if (this.tab == 'auto') {
            this.doFixDB();
        }

        $('#chkNowBtn').on('click', null, $.proxy(this.doCheckDB,this));
        $('#chkAgnBtn').on('click', null, $.proxy(this.doCheckDB,this));
        $('#fixBtn').on('click', null, $.proxy(this.doFixDB,this));
    };

    //------------------------------
	  initWidgets () {
	  };

    resetForms () {
        //console.log('resetting!');
        if (this.tab == 'admin') {
            $('#editDiv').show();
        } else {
            $('#editDiv').hide();
        }
        $('#plsWait').hide();
        $('#rsltDiv').hide();
        $('#fixBtn').hide();
    };

	//------------------------------
    doCheckDB () {
        $('#plsWait').show();
        $.post(this.url, {cat:'integrity', mode:'checkDB'}, function (data) {
            $('#plsWait').hide();
            if (data.length > 0) {
                var list = "";
                for (var n in data) {
            		var item = data[n]['msg'];
                    list += '<li>'+item+'</li>';
                }
                var content = list;
                $('#fixBtn').show();
            } else {
                var content = "<?php echo '<p>'.T("No errors found").'</p>'; ?>";
                $('#fixBtn').hide();
            }
            $('#rslts').html(content)
        }, 'json');

        $('#editDiv').hide();
        $('#rsltDiv').show();
    };

	  //------------------------------
    doFixDB () {
        var tab = this.tab;
        var rtnTo = this.rtnTo;
        $('#plsWait').show();
        $.post(this.url, {cat:'integrity', mode:'fixDB'}, function (data) {
            $('#plsWait').hide();
                if (data.length > 0) {
                    var list = "";
                    for (var n in data) {
                		var item = data[n]['msg'];
                        list += '<li>'+item+'</li>';
                    }
                    var content = list;
                    $('#fixBtn').show();
                } else {
                    var content = "<?php echo '<p>'.T("No errors found").'</p>'; ?>";
                    $('#fixBtn').hide();
                }
                if (tab == 'auto') {
                    location = rtnTo;
                    return false;   // DO NOT REMOVE
                }
                $('#rslts').html(content)
            }, 'json');
        if (tab == 'admin') {
            $('#editDiv').hide();
            $('#rsltDiv').show();
        }
    };

};

$(document).ready(function () {
	  var chkr = new Chkr();
});

</script>
