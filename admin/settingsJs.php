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
	echo 'failureMsg: "'.T("Update failed").'",';
	?>

	init: function () {
		set.url = '../admin/adminSrvr.php';
		set.listSrvr = '../shared/listSrvr.php'

		set.initWidgets();
    	$('#charset').val(set.charSet);

		list.getDayList($('#first_day_of_week'));
		$('#editSettingsForm').on('submit',null,set.doUpdate);
		$('#fotoTestBtn').on('click',null,set.doFotoTest);
		$('#fotoDoneBtn').on('click',null,set.doTestDone);

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
		obib.hideMsg('now');
		$('#editDiv').show();
		$('#photoEditorDiv').hide();
		$('#site_cd').focus();
		$('#updtBtn').show();

		set.prepareFotoTest();
	},
	prepareFotoTest: function () {
		if ((Modernizr.video) && (typeof(wc)) !== 'undefined') {
			$('#fotoTestBtn').show();
			$('#fotoDoneBtn').hide();
			$('#fotoHdr').hide();
			$('#reqdNote').hide();
			$('#fotoInfo').hide();
		}
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
			set.fetchCameras();
		}, 'json');
	},
	//------------------------------
	fetchCameras: function () {
		var html = '<option value="0">Please select a camera</option>';

		list.getMediaList()  // returns a Promise, obviously
		.then(devices => {
			devices.forEach(function(device) {
				if (device.kind == "videoinput") {
    				html+= '<option value="'+device.deviceId+'">'+device.label+'</option>';
    			}
			});
    		$('#camera').html(html).show();
			set.fetchFormData();
		})
        .catch(e => console.error(e));
	},

	/* not in use at this time - June2017
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
			set.fetchCameras();
		}, 'json');
	},
	*/

	//------------------------------
	fetchFormData: function () {
		//console.log('getting data from db')
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

				/* set form field using downloaded data */
				//console.log(fields[n].name+' ==>> '+fields[n].value);
				if (fields[n].type == 'textarea') {
                    $id.html(fields[n].value).attr('rows',fields[n].width)
				} else if ((fields[n].type != 'select') && (fields[n].type != 'checkbox')) {
					$id.val(fields[n].value).attr('size',fields[n].width);
				} else if ((fields[n].type == 'checkbox') || (fields[n].type == 'select')) {
					//console.log('we have a select box');
                    $id.val([fields[n].value]);
				}
			}
		}, 'json');
	},

	//------------------------------
	doFotoScreenUpdate: function () {
		wc.fotoWidth = $('#thumbnail_width').val();
		wc.fotoHeight = $('#thumbnail_height').val();
		wc.fotoRotate = $('#thumbnail_rotation').val();
	},
	doFotoTest: function (e) {
		//console.log('test btn pressed');
		set.doFotoScreenUpdate();
		wc.init();  // to insure that current settings are in use by photoEditor

		$('#updtBtn').hide();
		$('#editDiv').hide();
		$('#fotoTestBtn').hide();
		$('#fotoDoneBtn').show();
		$('#photoEditorDiv').show();
		$('#fotoDiv').show();

	},
	doTestDone: function (e) {
		//console.log('test done btn pressed');
		wc.eraseImage();
		$('#photoEditorDiv').hide();
		$('#fotoTestBtn').show();
		$('#fotoDoneBtn').hide();
		$('#updtBtn').show();
		$('#editDiv').show();
	},

	//------------------------------
	doUpdate: function (e) {
		e.stopPropagation();
		e.preventDefault();
		set.doFotoScreenUpdate();
		$('#mode').val('update_settings');
		var params = $('#editSettingsForm').serialize();
        if (!($('#show_lib_info').is(':checked'))) params += "&show_lib_info=N";
        if (!($('#block_checkouts_when_fines_due').is(':checked'))) params += "&block_checkouts_when_fines_due=N";
        if (!($('#use_image_flg').is(':checked'))) params += "&use_image_flg=N";
        if (!($('#opac_site_mode').is(':checked'))) params += "&opac_site_mode=N";
		//console.log('in doUpdate');
		$.post(set.url, params, function (response) {
			//console.log(response);
			if (response !== null)
				obib.showError(set.failureMsg);
			else
				obib.showMsg(set.successMsg);
		}, 'JSON');
	},

};

$(document).ready(set.init);
</script>
