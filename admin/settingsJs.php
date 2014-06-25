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

	echo 'successMsg : "'.T("Update successful").'",'."\n";
	?>

	init: function () {
		set.url = 'adminSrvr.php';
		set.listSrvr = '../shared/listSrvr.php'

		set.initWidgets();

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
				html+= '<option value="'+key+'">'+set.locs[key]+'</option>';
			}
			$('#locale').html(html).show();
			set.fetchSiteList();
	},
	fetchSiteList: function () {
	  $.getJSON(set.listSrvr,{'mode':'getSiteList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#libraryName').html(html).show();
			set.fetchThemeDirs();
		});
	},
	fetchThemeDirs: function () {
	  $.getJSON(set.url,{'cat':'themes', 'mode':'getThemeDirs'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#theme_dir_url').html(html).show();
			set.fetchThemeList();
		});
	},
	fetchThemeList: function () {
	  $.getJSON(set.listSrvr,{'mode':'getThemeList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<option value="'+n+'">'+data[n]+'</option>';
			}
			$('#themeid').html(html).show();
			set.fetchFormData();
		});
	},

	//------------------------------
	fetchFormData: function () {
		$.getJSON(set.url, {'cat':'settings', 'mode':'getFormData'}, function (fields) {
			for (var n in fields) {
				/* this mapping needed due to same ids being used in <aside> */
				if (fields[n].name == 'library_name') fields[n].name = 'libraryName'
				if (fields[n].name == 'library_hours') fields[n].name = 'libraryHours'
				if (fields[n].name == 'library_phone') fields[n].name = 'libraryPhone'

				var $id = $('#'+fields[n].name);
				/* map out deprecated values */
				if (fields[n].type == 'int') fields[n].type = 'number';
				if (fields[n].type == 'bool') fields[n].type = 'checkbox';

        $id.attr('type',fields[n].type).prev().html(fields[n].title+':');
				switch (fields[n].type) {
					case 'select':
					case 'checkbox': $id.val([fields[n].value]); break;
					case 'number': $id.attr('pattern','/\d*/'); break;
					case 'date': $id.attr('pattern', flos.patterns.date); break;
					case 'tel': $id.attr('pattern', flos.patterns.tel); break;
					case 'url': $id.attr('pattern', flos.patterns.url); break;
					case 'email': $id.attr('pattern', flos.patterns.email); break;
				}
				if (fields[n].type == 'textarea') {
          $id.html(fields[n].value).attr('rows',fields[n].width)
				} else if ((fields[n].type != 'select') && (fields[n].type != 'checkbox')) {
					$id.val(fields[n].value).attr('size',fields[n].width);
				}
			}
		});
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
