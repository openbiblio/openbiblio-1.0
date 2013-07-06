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
	<input type="hidden" id="tab" value="<?php echo $tab;?>" />

<!-- ------------------------------------------------------------------------ -->
	<div id="rptArea">
		<div class="cntlArea">
			<div class="btnBox">
				<ul class="btnRow">
					<li><button class="prevBtn"><?php echo T("Prev");?></button></li>
					<li><button class="nextBtn"><?php echo T("Next");?></button></li>
				</ul>
			</div>
			<div class="countBox"> <p> foto count goes here </p> </div>
			<div class="sortBox">
				<select id="orderBy">
					<option value="title">Title</option>
					<option value="author" SELECTED>Author</option>
					<option value="callno">Call No.</option>
				</select>
			</div>
		</div>

		<fieldset id="gallery">
			<table id="fotos"> </table>
		</fieldset>

		<div class="cntlArea">
			<div class="nmbrbox">
				<ul class="btnRow">
					<li><button class="prevBtn"><?php echo T("Prev");?></button></li>
					<li><button class="nextBtn"><?php echo T("Next");?></button></li>
				</ul>
			</div>
		</div>

	</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	include "./imageBrowseJs.php";
?>

</body>
</html>

?>
