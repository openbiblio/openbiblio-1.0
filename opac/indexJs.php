<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * Re-write of original PHP based version.
 * @author Fred LaPlante, June 2017
 */

// JavaScript Document - ..../opac/indexJs.php
"use strict";

class Opac {
    constructor () {
		//console.log ("in OPAC::init()");
		this.url = "../catalog/catalogServer.php";

		this.crntSite = "<?php echo $_SESSION['current_site']; ?>";
		this.siteMode = "<?php echo Settings::get('opec_site_mode'); ?>";

		$('#theBtn').on('click', null, this.setSite);

		list.getSiteList($('#libraryName'));

		this.makeNav();

		// set initial (closed menu) states
		//$('#menuBtn').attr('aria-expanded', 'false')
		//			 .attr('hidden', 'false');
		//$('.menuSect').attr('hidden', 'true');

		$('#menuBtn').on('click', function() {
			// toggle menu visibility
console.log('menu btn clicked');
			$('.menuSect').toggle(); // show/hide
			if ($(this).attr('aria-expanded') === 'true') {
				$(this).attr('aria-expanded', 'false');
			} else {
                $(this).attr('aria-expanded', 'true');
			}
		});
		this.initWidgets();
		this.resetForms();
	};

	//------------------------------
	initWidgets () {
	};
	resetForms () {
	};

	//------------------------------
	setSite (e) {
		e.stopPropagation();
		e.preventDefault();

		$('#mode').val('setCurrentSite');
		let params = $('#chooserForm').serialize();
		this.url ="../catalog/catalogServer.php"; // can not seem to get to 'this.url'
		$.post(this.url, params, function(response){
			window.location = "../catalog/srchForms.php?tab=OPAC";
		}, 'json');
	};

	//------------------------------
    makeNav () {
		// based on "Stupid Simple jQuery Accordian" by Ryan Stemkoski, 2009
		$(".navHeading").bind('click',null,function() {
			$(".navSelected").removeClass("navSelected").addClass("navUnselected")
			$(this).removeClass("navUnselected").addClass("navSelected");
			$(".navContent").hide();//slideUp();
			$(this).next(".navContent").show();//slideDown();
		});

		//$(".navContent").hide();
		$(".navHeading").addClass("navUnselected");
		$("nav #defaultOpen").trigger("click");
	};

}

var opac = new Opac();
</script>
