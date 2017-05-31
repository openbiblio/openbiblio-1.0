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
	<?php echo "calMASTER:".OBIB_MASTER_CALENDAR.","; ?>

	init: function () {
		var d = new Date();
		cal.mon = d.getMonth();
		cal.year = d.getFullYear();
		cal.url = '../admin/calendarSrvr.php';
        cal.listSrvr = '../shared/listSrvr.php';
		cal.initWidgets();
		$('.help').hide();

		$('#calAddNewBtn').on('click',null,cal.newCalendar);
		$('#editForm').on('submit',null,cal.saveCalendar);
		$('.calGoBkBtn').on('click',null,cal.rtnToList);
		$('.calDeltBtn').on('click',null,cal.deleteCalendar);

		cal.resetForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		obib.hideMsg('now');
		$('.help').hide();
		$('#errSpace').hide();
		$('#listDiv').show();
		$('#editDiv').hide();
		$('#calDeltBtn').disable();
    	cal.fetchCalendarList();
	},
	rtnToList: function () {
		cal.resetForm()
	},

	//------------------------------
	newCalendar: function (e) {
		e.stopPropagation();
		e.preventDefault();
		cal.calCd = cal.calMASTER;
		$('#calCd').val(cal.calCd);
		$('#name').val('');
		$('#calName').val('');
		$('#calMode').val('getCalendar');
		cal.doGetCalendar(e);
		$('#calMode').val('makeNewCalendar');
		$('#calDeltBtn').hide();
		$('#listDiv').hide();
		$('#editDiv').show();
	},

	saveCalendar: function (e) {
		e.stopPropagation();
        e.preventDefault();
		$('#calName').val(cal.calName);
		$('#calCd').val(cal.calCd);
	    var params = $('#editForm').serialize();
	    $.post(cal.url, params, function(response){
//			$('#errSpace').html(response).show();
			obib.showMsg(response);
		});
	},

	fetchCalendarList: function (e) {
	  $.post(cal.listSrvr, {mode:'getCalendarList'}, function(data){
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
		}, 'json');
	},

	doGetCalendar: function () {
		$.post(cal.url, {'mode':$('#calMode').val(), 'calendar':cal.calCd, 'mon':cal.mon, 'year':cal.year}, function (response) {
			$('#calArea').html(response);
			cal.createYearNavLinks();
		});
	},

	showCalendar: function (e) {
		e.stopPropagation();
		e.preventDefault();
		cal.calCd = $(this).next().val();
		cal.calName = $(this).next().next().val();
		if (cal.calCd == 1)
			$('#masterOnly').show();
		else
			$('#masterOnly').hide();
		$('#name').val(cal.calName);
		$('#calCd').val(cal.calCd);
		$('#calName').val(cal.calName);
		$('#calMode').val('getCalendar');
		cal.doGetCalendar(e);
		$('#calMode').val('saveCalendar');
		$('#calDeltBtn').show().enable();
		$('#listDiv').hide();
		$('#editDiv').show();

	},

	createYearNavLinks: function () {
		$(".year-next").click(function(e){
			e.preventDefault();
			cal.saveCalendar(e);
			cal.year = cal.year + 1;
			cal.showCalendar(e);
		});
		$(".year-prev").click(function(e){
			e.preventDefault();
			cal.saveCalendar(e);
			cal.year = cal.year - 1;
			cal.showCalendar(e);
		});
	},


	deleteCalendar: function (e) {
		if (confirm("<?php echo T("DeleteThisCalendar"); ?>?")) {
			$.post(cal.url, {'mode':'deleteCalendar', 'calendar':cal.calCd}, function (response) {
//				$('#errSpace').html(response).show();
				obib.showMsg(response);
				$('#calDeltBtn').disable();
			});
		}
	},
}
$(document).ready(cal.init);
</script>
