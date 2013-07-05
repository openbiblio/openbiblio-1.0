<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	session_cache_limiter(null);

	$tab = "working";
	if (isset($_REQUEST["tab"])) {
		$tab = $_REQUEST["tab"];
	}
	$_REQUEST['tab'] = $tab;

	$nav = "browse_images";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}

	$nav = "imageBrowseForm";
	$focus_form_name = "";
	$focus_form_field = "";

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Cover Photos'));

	//print_r($_SESSION); // for debugging
?>

	<p id="errSpace" class="error"></p>
	<input type="hidden" id="tab" />

<!-- ------------------------------------------------------------------------ -->
	<div id="rptArea">
		<div class="cntlArea">
			<div class="nmbrbox"> <p class="pageList">xxxx</p> </div>
			<div class="countBox"> <p> foto count goes here </p> </div>
			<div class="sortBox">
				<select id="orderBy">
					<option value="title">Title</option>
					<option value="author" SELECTED>Author</option>
					<option value="callNo">Call No.</option>
				</select>
			</div>
		</div>

		<fieldset id="gallery">filled by server</fieldset>

		<div class="cntlArea">
			<div class="nmbrbox"> <p class="pageList">yyyy</p> </div>
		</div>

	</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	include "./imageBrowseJs.php";
?>

</body>
</html>

?>
