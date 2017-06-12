<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "settings";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"><?php echo T("Library Settings"); ?></h3>

<div id="editDiv">
	<div id="tabs">
		<fieldset>
			<ul class="controls inline">
				<!-- the displayed tab order will follow the order of the following links -->
				<li class="active"><a href="#libraryPage"><?php echo T("Library"); ?></a></li>
				<li><a href="#localePage"><?php echo T("Locale"); ?></a></li>
				<li><a href="#requestPage"><?php echo T("Requests"); ?></a></li>
				<li><a href="#photoPage"><?php echo T("CoverPhotos"); ?></a></li>
				<li><a href="#opacPage"><?php echo T("OPAC"); ?></a></li>
				<li><a href="#miscPage"><?php echo T("Miscellaneous"); ?></a></li>
			</ul>

			<!-- Note titles/Labels in this form are 'placeholders only', actual labels will be downloaded from the database -->
			<form role="form" name="editSettingsForm" id="editSettingsForm">
				<div id="libraryPage" class="block active">
					<label for="libraryName"><?php echo T("Library Title"); ?></label>
					<select id="libraryName" name="library_name" autofocus ></select>
					<br />
					<!--label for="libraryHours"><?php echo T("Library Hours"); ?></label>
					<textarea id="libraryHours" name="library_hours" rows="1" placeholder="M-F: 8am - 5pm<br />Sat:  9am - noon" /></textarea>
					<br /-->
					<label for="libraryPhone"><?php echo T("Library Phone"); ?></label>
					<input type="text" id="libraryPhone" name="library_phone" maxlength="32" />
					<br />
					<label for="library_home"><?php echo T("Library Address"); ?></label>
					<input type="text" id="library_home" name="library_home" maxlength="29" />
					<br />
					<label for="library_url"><?php echo T("Library URL"); ?></label>
					<input type="text" id="library_url" name="library_url" maxlength="36" />
					<br />
					<label for="library_image_url"><?php echo T("Library Image"); ?></label>
					<input type="text" id="library_image_url" name="library_image_url" maxlength="36" placeholder="photo of your choice" />
					<br />
					<label for="show_lib_info"><?php echo T("Show Lib Info on Staff pages"); ?></label>
					<input type="checkbox" id="show_lib_info" name="show_lib_info" value="Y" />
				</div>
				<div id="localePage" class="block">
					<label for="locale"><?php echo T("Available languages"); ?></label>
					<select id="locale" name="locale" ></select>
					<br />
					<label for="charset"><?php echo T("Character Set"); ?></label>
					<input type="text" id="charset" name="charset" >
					<br />
		            <label for="first_day_of_week"><?php echo T("First day of week"); ?></label>
		            <select id="first_day_of_week" name="first_day_of_week" ></select>
				</div>
				<div id="requestPage" class="block">
					<label for="request_from"><?php echo T("Request From"); ?></label>
					<input type="text" id="request_from" name="request_from" maxlength="18" />
					<br />
					<label for="request_to"><?php echo T("Request To"); ?></label>
					<input type="text" id="request_to" name="request_to" maxlength="19" />
					<br />
					<label for="request_subject"><?php echo T("Request Subject"); ?></label>
					<input type="text" id="request_subject" name="request_subject" maxlength="33" />
				</div>
				<div id="photoPage" class="block">
                    <label for="use_image_flg"><?php echo T("Use Image"); ?></label>
                    <input type="checkbox" id="use_image_flg" name="use_image_flg" value="Y" />
					<br />
					<label for="camera"><?php echo T("Select a camera"); ?></label>
					<select id="camera" name="camera" ><option >Not available</option></select>
					<br />
					<label for="items_per_page"><?php echo T("Photos per Page"); ?></label>
					<input type="number" id="items_per_page" name="items_per_page" maxlength="18" value="25" required aria-required />
					<br />
					<label for="item_columns"><?php echo T("Photo Columns"); ?></label>
					<input type="number" id="item_columns" name="item_columns" maxlength="17" value="5" required aria-required  />
					<br />
					<label for="thumbnail_width"><?php echo T("Photo Width"); ?></label>
					<input type="number" id="thumbnail_width" name="thumbnail_width" maxlength="19" value="100" required aria-required />(mm)
					<br />
					<label for="thumbnail_height"><?php echo T("Photo Height"); ?></label>
					<input type="number" id="thumbnail_height" name="thumbnail_height" maxlength="19" value="120" required aria-required />(mm)
					<br />
					<label for="thumbnail_rotation"><?php echo T("Photo Rotation"); ?></label>
					<input type="number" id="thumbnail_rotation" name="thumbnail_rotation" maxlength="19" value="0" required aria-required />(deg)
					<br />
					<input type="button" id="fotoTestBtn" value="Test" />
				</div>
				<div id="opacPage" class="block">
					<label for="opac_url"><?php echo T("OPAC URL"); ?></label>
					<input type="text" id="opac_url" name="opac_url" size="17" maxlength="33" />
                    <br />
                    <label for="opac_site_mode"><?php echo T("OpacUserSelectsSite"); ?></label>
                    <input type="checkbox" id="opac_site_mode" name="opac_site_mode" value="Y" />
				</div>
				<div id="miscPage" class="block">
					<label for="mbr_barcode_width"><?php echo T("Barcode width"); ?></label>
					<input type="number" id="mbr_barcode_width" name="mbr_barcode_width" maxlength="17" />
                    <br />
					<!--label for="themeid"><?php echo T("Theme id"); ?></label>
					<select id="themeid" name="themeid"></select>
					<br />
					<label for="theme_dir_url"><?php echo T("Theme Dir URL"); ?></label>
					<select id="theme_dir_url" name="theme_dir_url" ></select-->
				</div>
				<hr>
				<input type="hidden" id="cat" name="cat" value="settings" />
				<input type="hidden" id="mode" name="mode" />
				<input type="submit" id="updtBtn" value="Update" />
			</form>
		</fieldset>
	</div>
</div>

<div id="photoEditorDiv">
	<?php require_once(REL(__FILE__,"../catalog/photoEditorForm.php"));?>
		<input type="button" id="fotoDoneBtn" value="Done" />
</div>

<?php
  	require_once(REL(__FILE__,'../shared/footer.php'));

	require_once(REL(__FILE__, "../admin/settingsJs.php"));
?>
</body>
</html>
