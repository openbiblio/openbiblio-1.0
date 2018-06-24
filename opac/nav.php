<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 *
 * @author Fred LaPlante, June 2017
 */
?>
    <nav id="accordion" role="navigation" aria-label="site" tabindex="-1">
      	<section class="menuSect">
        	<h3 class="navHeading" id="defaultOpen">Library Holdings</h3>
				<div class="navContent">
				  <a href="../catalog/srchForms.php?tab=OPAC" title="search">Library Search</a><br />
				  <a href="../opac/doiSearchForms.php?tab=OPAC" title="doi">use DOI</a><br />
				  <a href="../opac/imageBrowseForm.php?tab=OPAC" title="Photos">CoverPhotos</a><br />
				  <a href="../shared/req_cart.php?tab=opac#main" title="cart">Cart</a><br />
				</div>
      	</section>
			  
		<section class="menuSect">
			<h3 class="navHeading">My Account</h3>
			<div class="navContent">
                <a href="../opac/my_account.php?tab=OPAC" title="Info">Account Information</a><br />
                <a href="../opac/edit_account.php?tab=OPAC" title="Edit">Edit Account</a><br />
                <a href="../opac/bookings.php?tab=OPAC" title="Bookings">Bookings</a><br />
	        </div>
	    </section>

		<section class="menuSect">
			<h3 class="navHeading">About Library</h3>
			<div class="navContent about">
                <a href="../opac/aboutForm.php?tab=OPAC" title="Info">About Library</a><br />
				<?php if (Settings::get('library_image_url') != "") {
					echo '<img id="logo"'.' src="'.Settings::get("library_image_url").'" />';
				} ?>

				<!-- Libname is defined in header_top.php -->
				<span id="library_name" ><?php echo $libName; ?></span>

				<hr class="hdrSpacer">
				<?php echo $open_hours->displayOpenHours(); ?>

				<hr class="hdrSpacer">
				<div id="library_phone"><?php echo Settings::get('library_phone'); ?></div>

				<hr class="hdrSpacer" />
				<footer>
				  <div id="obLogo">
						<a href="https://bitbucket.org/mstetson/obiblio-1.0-wip/">
							<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" alt="Powered by OpenBiblio" />
						</a>
						<br />
					</div>

					OpenBiblio Version: <?php echo H(OBIB_CODE_VERSION);?>
					<br />
					For <a href="../COPYRIGHT.html">Legal Info</a>.
				</footer>
	        </div>
	    </section>
	</nav>
