<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "working";
	$nav = "webcamForm";
	$focus_form_name = "fotoForm";
	$focus_form_field = "capture";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Cover Photo Capture'));

	//print_r($_SESSION); // for debugging
?>

	<div id="crntMbrDiv">to be filled by server</div>
	<p id="errSpace" class="error">to be filled by server</p>

	<div id="fotoAreaDiv" role="main">
		<div id="fotoDiv">
		  <video id="camera" autoplay width="150" height="100" autoplay  style="display:none"></video>
		 	<canvas id="canvasIn" width="150" height="150" style="display:none"></canvas>
		</div>

		<div id="fotoCntlDiv">
			<form id="fotoForm">
				<fieldset class="inlineFldSet">
			 		<canvas id="canvasOut" width="100" height="150"></canvas>
				</fieldset>
				<fieldset class="inlineFldSet">
					<input type="button" id="capture" name="capture" value="Take Photograph" /><br ><br />
          <br />
					<label for="fotoFolder"><?php echo T("StoreAt"); ?>:</label>
					<p id="fotoFolder">../photos/<i>filename</i>.png</p>
					<br />
					<label for="fotoName"><?php echo T("Name"); ?>:</label>
					<input type="text" id="fotoName" name="url" size="32"
								pattern="(.*?)(\.)(jpg|png)$" required aria-required="true"
								title="Only png files are acceptable." />
								<span class="reqd">*</span>
				</fieldset>
			</form>
			<input type="submit" id="addFotoBtn" value="<?php echo T("Add New"); ?>" />
		</div>
	</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	include "./webcamJs.php";
?>

<script src="http://www.nihilogic.dk/labs/exif/exif.js" type="text/javascript"></script>
<script src="http://www.nihilogic.dk/labs/binaryajax/binaryajax.js" type="text/javascript"></script>

</body>
</html>
