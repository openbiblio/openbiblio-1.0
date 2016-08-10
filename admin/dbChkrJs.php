<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Chkr {
    constructor () {
    		this.url = '../admin/adminSrvr.php';

        $('#chkNowBtn').on('click', null, $.proxy(this.doCheckDB,this));
        $('#chkAgnBtn').on('click', null, $.proxy(this.doCheckDB,this));
        $('#fixBtn').on('click', null, $.proxy(this.doFixDB,this));

    		this.initWidgets();
    		this.resetForms()
    };

    //------------------------------
	  initWidgets () {
	  };

    resetForms () {
        //console.log('resetting!');
        $('#editDiv').show();
        $('#rsltDiv').hide();
        $('#fixBtn').hide();
    };

	  //------------------------------
    doCheckDB () {
        $.post(this.url, {cat:'integrity', mode:'checkDB'}, function (data) {
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
        $.post(this.url, {cat:'integrity', mode:'fixDB'}, function (data) {
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

};

$(document).ready(function () {
	  var chkr = new Chkr();
});

</script>
