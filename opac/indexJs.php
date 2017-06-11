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
}

var opac = new Opac();
</script>
