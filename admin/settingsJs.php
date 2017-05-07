<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

var set = {
	<?php
	require_once(REL(__FILE__, "../classes/Localize.php"));
	//$jsLoc = new Localize(OBIB_LOCALE,$tab);
	echo 'locs: '.json_encode($LOC->getLocales()).',';
    echo 'locale: "'.Settings::get('locale').'",';
    echo 'charSet: "'.Settings::get('charset').'",';
	echo 'successMsg: "'.T("Update successful").'",';
	?>

	init: function () {
		set.url = '../admin/adminSrvr.php';
		set.listSrvr = '../shared/listSrvr.php'

		set.initWidgets();
    	$('#charset').val(set.charSet);

		list.getDayList($('#first_day_of_week'));
		$('#editSettingsForm').on('submit',null,set.doUpdate);

		set.resetForms()
		set.setLocaleList();
	},

	//------------------------------
	initWidgets: function () {
		/* simple css3 tab support */
		$('#tabs').find('.controls').find('a').click( function(e){
			e.preventDefault();
			var el = jQuery(this);
			/* deal with actual 'tabs' */
			$('#tabs').find('.controls').find('li').removeClass('active');
			el.parent('li').addClass('active');
			/* deal with the tab 'pages' */
			$('#tabs').find('div').removeClass('active');
			$('#tabs').find(el.attr('href')).addClass('active').find(':first-child').focus();
		} );
	},

	resetForms: function () {
		//console.log('resetting!');
		$('#updateMsg').hide();
		$('#msgDiv').hide();
		$('#editDiv').show();
		$('#site_cd').focus();
	},

	setLocaleList: function () {
		var html = '';
        for (var key in set.locs) {
			html += '<option value="'+key+'"';
            if (key == set.locale) html += ' selected';
            html += ' >'+set.locs[key]+'</option>';
		}
		$('#locale').html(html).show();
		set.fetchSiteList();
	},
	fetchSiteList: function () {
	    $.post(set.listSrvr,{'mode':'getSiteList'}, function(data){
            var html = '';
            for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#libraryName').html(html).show();
			//set.fetchThemeDirs();  // not yet supported
			set.fetchFormData();
		}, 'json');
	},
	fetchThemeDirs: function () {
        $.post(set.url,{'cat':'themes', 'mode':'getThemeDirs'}, function(data){
    		var html = '';
            for (var n in data) {
    			html+= '<option value="'+n+'">'+data[n]+'</option>';
    		}
    		$('#theme_dir_url').html(html).show();
    		set.fetchThemeList();
		}, 'json');
	},
	fetchThemeList: function () {
	   $.post(set.listSrvr,{'mode':'getThemeList'}, function(data){
			var html = '';
            for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#themeid').html(html).show();
			set.fetchFormData();
		}, 'json');
	},

	//------------------------------
	fetchFormData: function () {
		$.post(set.url, {'cat':'settings', 'mode':'getFormData'}, function (fields) {
			for (var n in fields) {
				/* this mapping needed due to same ids being used in <aside> */
				if (fields[n].name == 'library_name') fields[n].name = 'libraryName'
				if (fields[n].name == 'library_hours') fields[n].name = 'libraryHours'
				if (fields[n].name == 'library_phone') fields[n].name = 'libraryPhone'

				/* map out deprecated values */
				if (fields[n].type == 'int') fields[n].type = 'number';
				if (fields[n].type == 'bool') fields[n].type = 'checkbox';

				var $id = $('#'+fields[n].name);
                //$id.attr('type',fields[n].type).prev().html(fields[n].title+':');
				/*
                switch (fields[n].type) {
					case 'select':
					case 'checkbox': $id.val([fields[n].value]); break;
					case 'number': $id.attr('pattern','/^\\d+$/'); break;
					case 'date': $id.attr('pattern', flos.patterns.date); break;
					case 'tel': $id.attr('pattern', flos.patterns.tel); break;
					case 'url': $id.attr('pattern', flos.patterns.url); break;
					case 'email': $id.attr('pattern', flos.patterns.email); break;
				}
                */
				if (fields[n].type == 'textarea') {
                    $id.html(fields[n].value).attr('rows',fields[n].width)
				} else if ((fields[n].type != 'select') && (fields[n].type != 'checkbox')) {
					$id.val(fields[n].value).attr('size',fields[n].width);
				}
			}
		}, 'json');
	},

	//------------------------------
	doUpdate: function (e) {
		e.stopPropagation();
		e.preventDefault();
		$('#mode').val('update_settings');
		var params = $('#editSettingsForm').serialize();
        if (!($('#show_lib_info').is(':checked'))) params += "&show_lib_info=N";
        if (!($('#block_checkouts_when_fines_due').is(':checked'))) params += "&block_checkouts_when_fines_due=N";
        if (!($('#use_image_flg').is(':checked'))) params += "&use_image_flg=N";

        //--- suggest to prevent someone could update as null these fields and cause list of srchForms disappear --CelsoC--
        if ($('#items_per_page').val(null)) params += "&items_per_page=25";
        if ($('#thumbnail_width').val(null)) params += "&thumbnail_width=100";
        if ($('#thumbnail_height').val(null)) params += "&thumbnail_height=120";
        if ($('#thumbnail_rotation').val(null)) params += "&thumbnail_rotation=0";
        //-------------

		$.post(set.url, params, function (response) {
			//if (response === null)
				$('#updateMsg').html(set.successMsg).show();
			//else
			//	$('#updateMsg').html(response);
		});
	},

};

$(document).ready(set.init);
</script>
