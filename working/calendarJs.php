<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";
	//------------------------------------------------------------------------//
	//These two JS functions are specific to the PHP Calendar class being used//
	function toggleDay (id) {
		var input$ = $('#IN-'+id);
		var cell$ = $('#date-'+id);
		var className = "";
		if (cell$.hasClass('calendarToday')) {
			className = "calendarToday ";
		}
		if(input$.val() == 'Yes') {
      input$.val('No');
			cell$.removeClass('calendarOpen');
			cell$.addClass('calendarClosed');
		} else {
      input$.val('Yes');
			cell$.removeClass('calendarClosed');
			cell$.addClass('calendarOpen');
		}
		modified=true; // for link confirmation
	};
	function toggleDays (wday, year, month) {
		var pattern = '^IN-(';
		if (wday == '*') {
			pattern += '[0-9]';
		} else {
			pattern += wday;
		}
		pattern += '-'+year+'-'+month+'-[0-9][0-9])$';
		var re = new RegExp(pattern);
		$('input').each(function(n){
			var m = re.exec(this.id);
			if (m) toggleDay(m[1]);
		});
	};
	//------------------------------------------------------------------------//

var cal = {
	init: function () {
		cal.url = '../working/calendarSrvr.php';
    cal.listSrvr = '../shared/listSrvr.php';
		cal.initWidgets();
		$('.help').hide();

		$('.calSaveBtn').on('click',null,cal.saveCalendar);

		cal.resetForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		$('.help').hide();
		$('#errSpace').hide();
		$('#listDiv').show();
		$('#editDiv').hide();
    cal.fetchCalendarList();
	},

	//------------------------------
	saveCalendar: function (e) {
		e.stopPropagation();
		$.post(cal.url, { 'mode':'saveCalendar',
											'oldName':cal.calName,
										},
										function (response) {

		});
    e.preventDefault();
	},

	fetchCalendarList: function () {
	  $.getJSON(cal.listSrvr,{mode:'getCalendarList'}, function(data){
			var html = '';
      for (var n in data) {
				html+= '<li>';
				html+= '<input type="button" class="calEntryBtn" value="edit" />';
				html+= '<input type="hidden" value="'+n+'" />';
				html+= '<input type="text" readonly value="'+data[n]+'" />';
				html+= '</li>';
			}
			$('#calList').html(html);
			$('.calEntryBtn').on('click',null,cal.showCalendar);
		});
	},

	showCalendar: function (e) {
		e.stopPropagation();
		cal.calCd = $(this).next().val();
		cal.calName = $(this).next().next().val();
		$('#name').val(cal.calName);

		console.log('show calendar for '+cal.calName);
		$.get(cal.url, {'mode':'getCalendar', 'calendar':cal.calCd}, function (response) {
			$('#calArea').html(response);
		});
		e.preventDefault();

		$('#listDiv').hide();
		$('#editDiv').show();
	},

}
$(document).ready(cal.init);
</script>
