<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document - .../shared/listJs.php
"use strict";

var list = {
	server: '../shared/listSrvr.php',

    init: function () {
//        list.server = '../shared/listSrvr.php';
		list.getMediaList()
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getPullDownList: function (listName, whereToPaste) {
        $.post(list.server, {mode:'get'+listName+'List', select:'true'}, function(data){
    		var html = '';
            for (var n in data) {
    			html += '<option value="'+n+'" ';
                var dflt= data[n].default;
                if (dflt == 'Y') html += 'SELECTED ';
                html += '>'+data[n].description+'</option>';
    		}
            whereToPaste.html(html);
			//console.log(html);
    		return html;
		}, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	mediaListPromise: null,
	getMediaList: function () {
		//console.log('in list::getMediaList()');
		if (!this.mediaListPromise) {
			//console.log('no mediaDevices yet available');
			this.mediaListPromise = navigator.mediaDevices.enumerateDevices();
		}
		return this.mediaListPromise;
	},

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getCalendarList: function (where) {
        $.post(list.server, {mode:'getCalendarList'}, function(data){
            return data;
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    // occasionally where getDayList() is too slow
    getDays: function () {
        $.post(list.server, {mode:'getDaysOfWeek'}, function(data){
          list.days = data;
          return list.days;
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    <?php $first_day = Settings::get('first_day_of_week') ? Settings::get('first_day_of_week') : 0; ?>
    getDayList: function (where) { 
        $.post(list.server, {mode:'getDaysOfWeek'}, function(data){
            //list.days = data;

            var html = '';
            for (var i=<?php echo $first_day; ?>;i< <?php echo 7+$first_day; ?>;i++) {
                html+= '<option value="'+i%7+'" >'+data[i%7]+'</option>';
            }
            where.html(html);
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getCollectionList: function (where, callback) { 
        $.post(list.server, {mode:'getCollectionList'}, function(data){
    		    var html = '';
            for (var n in data) {
        			  html+= '<option value="'+n+'" >'+data[n]+'</option>';
    		    }
            where.html(html);
            callback();
        }, 'json');
    },


    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getDueDateCalculatorList: function (where) { 
        $.post(list.server, {mode:'getDueDateCalculators'}, function(data){
    	    var html = '';
            for (var n in data) {
        	  html+= '<option value="'+data[n]+'" >'+data[n]+'</option>';
    	    }
            where.html(html);
        }, 'json');
    },
    getImportantDatePurposeList: function (where) { 
        $.post(list.server, {mode:'getImportantDatePurposes'}, function(data){
    	    var html = '';
            for (var n in data) {
        	  html+= '<option value="'+data[n]+'" >'+data[n]+'</option>';
    	    }
            where.html(html);
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getLocaleList: function (where) {
    	  $.post(list.server, {'mode':'getLocaleList'}, function(data){
    			  var html = '';
    			  $.each(data, function (key,value) {
    				    html += '<option ';
    				    if (key == '<?php echo $Locale ?>') {
    					      $('#crntLoc').html(value);
    					      html += 'selected ';
    				    }
    				    html += 'value="'+key+'">'+value+'</option>';
    			  });
    			  where.html(html);
    		}, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getMaterialList: function (where, callback) {
        $.post(list.server, {mode:'getMediaList'}, function(data){
            var html = '';
            for (var n in data) {
                html+= '<option value="'+n+'" >'+data[n]+'</option>';
            }
            where.html(html);
            callback();
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getOpts: function () {
        $.post(list.server, {mode:'getOpts'}, function(data){
          list.opts = data;
          return list.opts;
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    // occasionally where getSiteList() is too slow
    getSites: function () {
        $.post(list.server, {mode:'getSiteList'}, function(data){
          list.sites = data;
          return list.sites;
        }, 'json');
    },
	getSiteHoldings: function () {
        $.post(list.server, {mode:'getSiteHoldings'}, function(data){
		  list.holdings = [];
		  for (let idx in data) {
			list.holdings[data[idx].siteid] = data[idx].quan;
		  }
          return list.holdings;
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    // different structure than other pull-down tables
    getSiteList: function(where) {
		//console.log ("in list::getSiteList()");
        $.post(list.server, {mode:'getDefaultSite'}, function(data){
            list.dfltSite = data;
            list.siteListPt2(where); // chaining
        }, 'json');
    },
    siteListPt2: function (where) {
		//console.log ("in list::siteListPt2()");
        $.post(list.server, {mode:'getSiteList'}, function(data){
            list.sites = data;
    		var html = '';
            for (let n in data) {
        		html+= '<option value="'+n+'" ';
                if (n == list.dfltSite) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[n]+'</option>';
    		}
            where.html(html);
            return html;
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getStatusCds: function (where) {
        $.post(list.server,{'mode':'getStatusCds'}, function(data){
             var html = '';

            $.each(data, function(i, item) {
                html+= '<option value="'+i+'" ';
                if (item.default == "Y") {
                    html+= 'SELECTED '
                }
                html+= '>'+ item.description +'</option>';
            });

            where.html(html);
         }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
	  getStateList: function (where) { // deprecated
        var html = list.getPullDownList('State', where);
        return html;
  	},
}
$(document).ready(list.init);
</script>
