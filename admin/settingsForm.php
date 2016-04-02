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
		<p id="updateMsg" class="warning"></p>

		<fieldset>
			<ul class="controls inline">
				<li class="active"><a href="#localePage"><?php echo T("Locale"); ?></a></li>
				<li><a href="#libraryPage"><?php echo T("Library"); ?></a></li>
				<li><a href="#miscPage"><?php echo T("Miscellaneous"); ?></a></li>
				<li><a href="#requestPage"><?php echo T("Requests"); ?></a></li>
				<li><a href="#photoPage"><?php echo T("CoverPhotos"); ?></a></li>
			</ul>

			<form name="editSettingsForm" id="editSettingsForm">
				<div id="localePage" class="block active">
					<label for="locale"></label>
					<select id="locale" name="locale" autofocus ></select>
					<br />
					<label for="charset"></label>
					<input type="text" id="charset" name="charset" >
					<br />
					<label for="themeid"></label>
					<select id="themeid" name="themeid"></select>
					<br />
					<label for="theme_dir_url"></label>
					<select id="theme_dir_url" name="theme_dir_url" ></select>
				</div>
				<div id="libraryPage" class="block">
					<label for="libraryName"></label>
					<select id="libraryName" name="library_name" ></select>
					<br />
					<label for="libraryHours"></label>
					<textarea id="libraryHours" name="library_hours" rows="1" /></textarea>
					<br />
					<label for="libraryPhone"></label>
					<input type="text" id="libraryPhone" name="library_phone" maxlength="32" />
					<br />
					<label for="library_home"></label>
					<input type="text" id="library_home" name="library_home" maxlength="29" />
					<br />
					<label for="library_url"></label>
					<input type="text" id="library_url" name="library_url" maxlength="36" />
					<br />
					<label for="library_image_url"></label>
					<input type="text" id="library_image_url" name="library_image_url" maxlength="36" />
					<br />
					<label for="show_lib_info"></label>
					<input type="checkbox" id="show_lib_info" name="show_lib_info" value="Y" />
				</div>
				<div id="miscPage" class="block">
					<label for="mbr_barcode_width"></label>
					<input type="number" id="mbr_barcode_width" name="mbr_barcode_width" maxlength="17" />
          <br />
					<label for="block_checkouts_when_fines_due"></label>
					<input type="checkbox" id="block_checkouts_when_fines_due" name="block_checkouts_when_fines_due" value="Y" />
          <br />
					<label for="opac_url"></label>
					<input type="text" id="opac_url" name="opac_url" size="17" maxlength="33" />
				</div>
				<div id="requestPage" class="block">
					<label for="request_from"></label>
					<input type="text" id="request_from" name="request_from" maxlength="18" />
					<br />
					<label for="request_to"></label>
					<input type="text" id="request_to" name="request_to" maxlength="19" />
					<br />
					<label for="request_subject"></label>
					<input type="text" id="request_subject" name="request_subject" maxlength="33" />
				</div>
				<div id="photoPage" class="block">
          <br />
					<label for="use_image_flg"></label>
					<input type="checkbox" id="use_image_flg" name="use_image_flg" value="Y" />
					<br />
					<label for="items_per_page"></label>
					<input type="number" id="items_per_page" name="items_per_page" maxlength="18"  />
					<br />
					<label for="item_columns"></label>
					<input type="number" id="item_columns" name="item_columns" maxlength="17"  />
					<br />
					<label for="thumbnail_width"></label>
					<input type="number" id="thumbnail_width" name="thumbnail_width" maxlength="19"  />(mm)
					<br />
					<label for="thumbnail_height"></label>
					<input type="number" id="thumbnail_height" name="thumbnail_height" maxlength="19"  />(mm)
					<br />
					<label for="thumbnail_rotation"></label>
					<input type="number" id="thumbnail_rotation" name="thumbnail_rotation" maxlength="19"  />(deg)
				</div>
				<hr>
				<input type="hidden" id="cat" name="cat" value="settings" />
				<input type="hidden" id="mode" name="mode" />
				<input type="submit" value="Update" />
			</form>
		</fieldset>
	</div>
</div>

<div id="msgDiv" style="display: none;"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));

//	require_once(REL(__FILE__, "../classes/ListJs.php"));
	require_once(REL(__FILE__, "settingsJs.php"));
?>
</body>
</html>
