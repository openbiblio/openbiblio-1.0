<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document - copyEditorJs.php
"use strict";

var list = {
    init: function () {
        list.server = '../shared/listSrvr.php';
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getPullDownList: function (listName, whereToPaste) {
        $.post(list.server, {mode:'get'+listName+'List'}, function(data){
    			  var html = '';
            for (var n in data) {
    				    html += '<option value="'+n+'" ';
                var dflt= data[n].default;
                if (dflt == 'Y') html += 'SELECTED ';
                html += '>'+data[n].description+'</option>';
    			  }
            whereToPaste.html(html);
    		    return html;
		    }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getCalendarList: function (where) { // deprecated
        var html = list.getPullDownList('Calendar', where);
        return html
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getCollectionList: function (where) { // deprecated
        var html = list.getPullDownList('Collection', where);
        return html
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
    getMaterialList: function (where) { // deprecated
        var html = list.getPullDownList('Media', $('#media_cd'));
        return html;
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getOpts: function () {
        $.post(list.server, {mode:'getOpts'}, function(data){
          list.opts = data;
          return list.opts;
        }, 'json');
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    // different structure than other pull-down tables
    getSiteList: function(where) {
        $.post(list.server, {mode:'getDefaultSite'}, function(data){
            list.dfltSite = data;
			      list.siteListPt2(where); // chaining
        }, 'json');
    },
    siteListPt2: function (where) {
        $.post(list.server, {mode:'getSiteList'}, function(data){
    		    var html = '';
            for (var n in data) {
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
        $.post(list.server,{mode:'getDefaultStatusCd'}, function(data){
            list.dfltCd = data;
			      list.StatusListPt2(where); // chaining
        }, 'json');
    },
    StatusListPt2: function (where) {
    	  $.post(list.server,{'mode':'getStatusCds'}, function(data){
            var html = '';
            for (var cd in data) {
        			  html+= '<option value="'+cd+'" ';
                if (cd == list.dfltCd) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[cd]+'</option>';
    		    }
            where.html(html);
            return html;
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
