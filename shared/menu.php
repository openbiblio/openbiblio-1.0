<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

function staff_menu() {
	if($_SESSION["hasCircAuth"]){
		Nav::node('circulation', T("Circulation"), '../circ/memberForms.php');
		Nav::node('circulation/searchform', T("Members"), '../circ/memberForms.php');
		Nav::node('circulation/search', T("SearchResults"));

		Nav::node('circulation/bookings', T("Bookings"), '../circ/bookings.php');
		Nav::node('circulation/bookings/cart', T("Booking Cart"), '../circ/booking_cart.php');
		Nav::node('circulation/bookings/pending', T("Pending Bookings"));

		if (isset($_REQUEST[bookingid])) {
			$params = 'bookingid='.U($_REQUEST[bookingid]);
			if (isset($_REQUEST['rpt']) and isset($_REQUEST['seqno'])) {
				$params .= '&rpt='.U($_REQUEST['rpt']);
				$params .= '&seqno='.U($_REQUEST['seqno']);
			}
			Nav::node('circulation/bookings/view', T("Booking Info"), '../circ/booking_view.php?'.$params);
			Nav::node('circulation/bookings/deleted', T("Deleted"));
		}

		Nav::node('circulation/bookings/book', T("Create Booking"));
		Nav::node('circulation/checkin', T("Check In"), '../circ/checkinForms.php');
	}

	##-------------------------------------------------------------------------------------##
	if($_SESSION["hasCatalogAuth"]){
		Nav::node('cataloging', T("Cataloging"), '../catalog/srchForms.php');
		Nav::node('cataloging/localSearch', T("Existing Items"), "../catalog/srchForms.php");
		Nav::node('cataloging/newItem', T("New Item"), "../catalog/newItemForms.php");

		if (isset($_SESSION['rpt_BiblioSearch'])) {
			Nav::node('cataloging/search', T("old search results"), '../shared/biblio_search.php?searchType=previous&tab='.U($tab));
		}

		Nav::node('cataloging/cart', T("Request Cart"), '../shared/req_cart.php?tab='.U($tab));

			$params = 'bibid='.U($_REQUEST['bibid']);
			if (isset($_REQUEST['rpt']) and isset($_REQUEST['seqno'])) {
				$params .= '&rpt='.U($_REQUEST['rpt']);
				$params .= '&seqno='.U($_REQUEST['seqno']);
			}

			
			Nav::node('cataloging/biblio/editmarc', T("Edit MARC"), "../catalog/biblio_marc_edit_form.php?".$params);
			Nav::node('cataloging/biblio/editstock', T("Edit Stock Info"));
			Nav::node('cataloging/biblio/newlike', T("New Like"), "../catalog/biblio_new_like.php?".$menu_params);
			
			Nav::node('cataloging/biblio/bookings', T("Item Bookings"), "../reports/run_report.php?type=bookings"
				. "&rpt_order_by=outd!r"
				. "&tab=cataloging&nav=biblio/bookings"
				. "&rpt_bibid=".U($_REQUEST['bibid'])
				. "&".$params);
			Nav::node('cataloging/biblio/holds', T("Hold Requests"), "../catalog/biblio_hold_list.php?".$params);
		//Nav::node('cataloging/upload_usmarc', T("MARC Import"), "../catalog/upload_usmarc_form.php");
		//Nav::node('cataloging/upload_csv', T("CSVImport"), "../catalog/importCsvForms.php");
		Nav::node('cataloging/upload_usmarc', T("MARC Import"), "../catalog/importMarcForms.php");
		Nav::node('cataloging/upload_csv', T("CSVImport"), "../catalog/importCsvForms.php");
		Nav::node('cataloging/bulk_delete', T("Bulk Delete"), "../catalog/bulkDelForm.php");
	}
	
	##-------------------------------------------------------------------------------------##
	Nav::node('user', T("Research"), '../catalog/srchForms.php?tab=user');
	Nav::node('user/localSearch', T("Local Search"), '../catalog/srchForms.php?tab=user');
	Nav::node('user/doiSearch', T("doiSearch"), '../opac/doiSearchForms.php');
	Nav::node('user/images', T("CoverPhotos"), '../opac/imageBrowseForm.php?tab=user');
	Nav::node('user/biblio', T("Record Info"));
	Nav::node('user/cart', T("Cart"), '../shared/req_cart.php?tab=user');
	Nav::node('user/request', T("Booking"));
	if (isset($_SESSION['authMbrid'])) {
		Nav::node('user/account', T("My Account"), '../opac/my_account.php');
		Nav::node('user/account/edit', T("Edit Info"), '../opac/edit_account.php');
		Nav::node('user/account/bookings', T("Bookings"), '../opac/bookings.php');
		Nav::node('user/account/bookings/view', T("View"));
	}

	##-------------------------------------------------------------------------------------##
	if($_SESSION["hasAdminAuth"]){
		Nav::node('admin', T("Admin"), '../admin/index.php');
		Nav::node('admin/staff', T("Staff Admin"), '../admin/staffForm.php');
		Nav::node('admin/settings', T("Library Settings"), '../admin/settingsForm.php');
		Nav::node('admin/biblioFields', T("Biblio Fields"),'../admin/biblioFldsForm.php');
		Nav::node('admin/biblioCopyFields', T("Biblio Copy Fields"),'../admin/biblioCopyFldsForm.php');
		Nav::node('admin/calendar', T("Calendar Manager"), '../admin/calendarForm.php');
		Nav::node('admin/collections', T("Collections"), '../admin/collectionsForm.php');
		Nav::node('admin/media', T("Media Types"), '../admin/mediaForm.php');
		Nav::node('admin/memberTypes', T("Member Types"), '../admin/memberTypeForm.php');
		Nav::node('admin/memberFields', T("Member Fields"), '../admin/memberFldsForm.php');
		Nav::node('admin/onlineOpts', T("Online Options"), '../admin/onlineOptsForm.php');
		Nav::node('admin/onlineHosts', T("Online Hosts"), '../admin/onlineHostsForm.php');
		Nav::node('admin/openHours', T("Open hours"), '../admin/hoursForm.php');
		Nav::node('admin/sites', T("Sites"), '../admin/sitesForm.php');
		Nav::node('admin/states', T("States"), '../admin/statesForm.php');
		Nav::node('admin/themes', T("Themes"), '../admin/themeForm.php');
		Nav::node('admin/opac', T("View Opac"), '../catalog/srchForms.php?tab=OPAC');
		Nav::node('admin/integrity', T("Check Database"), '../admin/integrity.php');
	}	
	
	##-------------------------------------------------------------------------------------##
	if($_SESSION["hasReportsAuth"]){
		Nav::node('reports', T("Reports"), '../reports/index.php');
		Nav::node('reports/reportlist', T("Report List"), '../reports/index.php');
		if (isset($_SESSION['rpt_Report'])) {
			Nav::node('reports/results', T("Report Results"), '../reports/run_report.php?type=previous');
		}
	}

	##-------------------------------------------------------------------------------------##
	if($_SESSION["hasToolsAuth"]){
		Nav::node('tools', T("Tools"), '../tools/index.php');
		Nav::node('tools/settings', T("System Settings"), '../tools/settings_edit_form.php?reset=Y');
		Nav::node('tools/plugins', T("Plugin Manager"), '../tools/plugMgr_form.php');
		Nav::node('tools/valid', T("Input Validations"), '../tools/validForm.php');
		Nav::node('tools/system', T("WebServerInformation"), '../install/phpinfo.php');
		Nav::node('tools/system', T("DbServerInformation"), '../tools/DBConfigForms.php');
		Nav::node('tools/system', T("SystemDocumentation"), '../docs/index.php');
		//Nav::node('tools/system', T("Crude YAZ Test"), '../tools/yazTest.php');
		Nav::node('install/system', T("Install"), '../install/index.php');
	}
	
	##-------------------------------------------------------------------------------------##
		//Nav::node('working', T("Under Construction"), '../working/index.php');
		//Nav::node('working/testApp', T("MARCImport"), "../catalog/importMarcForms.php");
		//Nav::node('working/testApp', T("CSVImport"), "../catalog/importCsvForms.php");

	##-------------------------------------------------------------------------------------##
	$text = Settings::get('help_link');
	$helpurl = "javascript:popSecondary('" . $text . "');";

	Nav::node('help', T("Help"), $helpurl);
	
	##-------------------------------------------------------------------------------------##
	## #######################################
	## For plug-in support
	## #######################################
	$list = getPlugIns('nav.nav');
	for ($x=0; $x<count($list); $x++) {
		include_once ($list[$x]);
	}
	## #######################################
}
