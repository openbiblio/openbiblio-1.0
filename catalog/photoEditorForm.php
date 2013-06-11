<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
	<h3 id="fotoHdr"><?php echo T("PhotoEditor"); ?></h3>
	<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
	<p id="fotoMsg" class="error"></p>
	<fieldset>
		<legend id="fotoEdLegend"></legend>

		<!-- to reduce annoyance, only load video components if wanted-->
		<?php if ($_SESSION['show_item_photos'] == 'Y') { ?>
		<div id="fotoDiv" style="display:none" >
			<!-- The following have their dimensions interchanged for books that have spine facing user -->
		  <video id="camera" width="<?php echo Settings::get('thumbnail_height');?>"
												 height="<?php echo Settings::get('thumbnail_width');?>"
												 preload="none" ></video>
			<!-- folowing dimensions are not an error, the box MUST be square for later image rotation -->
		 	<canvas id="canvasIn" width="<?php echo Settings::get('thumbnail_height');?>"
			 											height="<?php echo Settings::get('thumbnail_height');?>" >
			</canvas>
		</div>
		<?php } ?>

		<div id="fotoCntlDiv">
			<form id="fotoForm">
				<fieldset class="inline">
			 		<canvas id="canvasOut" width="<?php echo Settings::get('thumbnail_width');?>"
					 											 height="<?php echo Settings::get('thumbnail_height');?>">
					</canvas>
				</fieldset>
				<fieldset class="inline">
					<fieldset>
						<legend>Select an image Source</legend>
						<label for="useCapture"><?php echo T("Camera"); ?></label>
						<input type="radio" id="useCapture" name="imgSrce" value="cam" checked class="fotoSrceBtns" \>
						<label for="useBrowse"><?php echo T("Browse"); ?></label>
						<input type="radio" id="useBrowse" name="imgSrce" value="brw" class="fotoSrceBtns" \>
					</fieldset>
					<input type="button" id="capture" name="capture" value="<?php echo T("Take Photograph"); ?>" />
					<input type="file" id="browse" name="browse" accept="image/png image/jpg" />
          <br />
					<label for="fotoFolder"><?php echo T("StoreAt"); ?>:</label>
					<p id="fotoFolder">../photos/<i><?php echo T("filename"); ?></i>.jpg</p>
					<br />
					<label for="fotoName"><?php echo T("FileName"); ?>:</label>
					<input type="text" id="fotoName" name="url" size="32"
								pattern="(.*?)(\.)(jpg|jpeg|png)$" required aria-required="true"
								title="<?php echo T("Only jpg or png files are acceptable."); ?>" />
								<span class="reqd">*</span>
				</fieldset>

				<input type="hidden" id="fotoBibid" name="bibid" value="" />
			</form>
		</div>
	</fieldset>
<?php
	include_once(REL(__FILE__,'photoEditorJs.php'));
?>
