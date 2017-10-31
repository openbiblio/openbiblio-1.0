<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

function opac_menu() {
	Nav::node('home', T("Search"), '../catalog/srchForms.php?tab=OPAC');
	Nav::node('images', T("CoverPhotos"), '../opac/imageBrowseForm.php?tab=opac');
	Nav::node('biblio', T("Record Info"));
	Nav::node('cart', T("Cart"), '../shared/req_cart.php?tab=opac');
	Nav::node('request', T("Booking"));
	if (isset($_SESSION['authMbrid'])) {
		Nav::node('account', T("My Account"), '../opac/my_account.php');
		Nav::node('account/edit', T("Edit Info"), '../opac/edit_account.php');
		Nav::node('account/bookings', T("Bookings"), '../opac/bookings.php');
		Nav::node('account/bookings/view', T("View"));
	}
}
