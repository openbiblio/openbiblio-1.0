<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

function opac_menu() {
	Nav::node('home', T("New Search"), '../opac/index.php');
	if (isset($_SESSION['rpt_BiblioSearch'])) {
		Nav::node('search', T("Search Results"),
			'../shared/biblio_search.php?searchType=previous&tab='.U($tab));
	}
	Nav::node('images', T("Browse Images"), '../shared/image_browse.php?tab='.U($tab));
	Nav::node('biblio', T("Record Info"));
	Nav::node('cart', T("Request Cart"), '../shared/req_cart.php');
	Nav::node('request', T("Booking Request"));
	if (isset($_SESSION['authMbrid'])) {
		Nav::node('account', T("My Account"), '../opac/my_account.php');
		Nav::node('account/edit', T("Edit Info"), '../opac/edit_account.php');
		Nav::node('account/bookings', T("Bookings"), '../opac/bookings.php');
		Nav::node('account/bookings/view', T("View"));
	}
}
