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
    getCollectionList: function () {
        $.getJSON(list.server,{mode:'getDefaultCollection'}, function(data){
            list.dfltColl = data[0];
			      list.getCollListPt2(); // chaining
        });
    },
    getCollListPt2: function () {
        $.getJSON(list.server,{mode:'getCollectionList'}, function(data){
        	  var html = '';
            for (var n in data) {
        		html+= '<option value="'+n+'" ';
                if (n == list.dfltColl) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[n]+'</option>';
        	  }
            return html
        });
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getMaterialList: function () {
        $.getJSON(list.server,{mode:'getDefaultMateria'}, function(data){
            list.dfltMatl = data;
			      list.matlListPt2(); // chaining
        });
    },
    matlListPt2: function () {
        $.getJSON(list.server,{mode:'getMediaList'}, function(data){
        	  var html = '';
            for (var n in data) {
        		html+= '<option value="'+n+'" ';
                if (n == list.dfltMatll) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[n]+'</option>';
        	  }
            return html
        });
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getOpts: function () {
        $.getJSON(list.server,{mode:'getOpts'}, function(data){
          list.opts = data;
console.log(list.opts);
          return list.opts;
        });
    },

    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getSiteList: function() {
        $.getJSON(list.server,{mode:'getDefaultSite'}, function(data){
            list.dfltSite = data;
			      list.siteListPt2(); // chaining
        });
    },
    siteListPt2: function () {
        $.getJSON(list.server, {mode:'getSiteList'}, function(data){
    		    var html = '';
            for (var n in data) {
        			  html+= '<option value="'+n+'" ';
                if (n == list.dfltSite) {
                    html+= 'SELECTED '
                }
                html+= '>'+data[n]+'</option>';
    		    }
            return html;
        });
    },
    //-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-//
    getStatusCds: function (where) {
        $.getJSON(list.server,{mode:'getDefaultStatusCd'}, function(data){
            list.dfltCd = data;
			      list.StatusListPt2(where); // chaining
        });
    },
    StatusListPt2: function (where) {
    	  $.getJSON(list.server,{'mode':'getStatusCds'}, function(data){
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
        });
    },
}
$(document).ready(list.init);
</script>
