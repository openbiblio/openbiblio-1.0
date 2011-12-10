<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "themes";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3><?php echo T("Themes"); ?></h3>

<div id="listDiv">
<h5 id="updateMsg"></h5>
<br />
<form id="selectForm" name="selectForm">
<fieldset>
<legend><?php echo T("Change Theme In Use:"); ?></legend>
	<div>
		<label for"themid"><?php echo T("Choose a New Theme:"); ?>
		<select id="themeList"></select>
		<input id="chngBtn" type="submit" value="<?php echo T("Update"); ?>" />
	</div>
</fieldset>
</form>

<p class="error">Sorry, Not Working, Under Construction<p>

<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<legend><?php echo T("Available Themes"); ?></legend>
<table id="showList" name="showList">
	<thead>
	<tr>
		<th valign="top"><?php echo T("Function"); ?></th>
		<th valign="top"><?php echo T("Theme Name"); ?></th>
		<th valign="top"><?php echo T("Usage"); ?></th>
	</tr>
	</thead>
	<tbody class="striped">
	  <tr><td colspan="4"><?php echo T("No sites have been defined."); ?> </td></tr>
	</tbody>
</table>
</fieldset>
<input type="submit" class="newBtn" value="<?php echo T("Add New"); ?>" />
</form>
</div>

<div id="editDiv">
<form id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
<table>
	<legend><?php echo T('Edit Media Properties'); ?></legend>
	<tbody id="part1" class="unstriped">
	<tr>
		<td><label for="theme_name"><?php echo T("Theme Name"); ?>:</label></td>
		<td colspan="4" valign="top">
			<input id="theme_name" name="theme_name" type="text" size="40" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td><label for="border_color"><?php echo T("Table Border Color:"); ?></label></td>
		<td colspan="4" valign="top">
			<input id="border_color" name="border_color" type="color" size="10" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td><label for="primary_error_color"><?php echo T("Error Color:"); ?></label></td>
		<td colspan="4" valign="top">
			<input id="primary_error_color" name="primary_error_color" type="color" size="10" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td><label for="border_width"><?php echo T("Table Border Width:"); ?></label></td>
		<td colspan="4" valign="top">
			<input id="border_width" name="border_width" type="number" size="2" required aria-required="true">px
		</td>
	</tr>
	<tr>
		<td><label for="table_padding"><?php echo T("Table Cell Padding:"); ?></label></td>
		<td colspan="4" valign="top">
			<input id="table_padding" name="table_padding" type="number" size="2" required aria-required="true">px
		</td>
	</tr>
	</tbody>
	
	<tbody class="unstriped">
	<tr>
		<th valign="top">&nbsp;</td>
		<th valign="top"><?php echo T("Title"); ?></td>
		<th valign="top"><?php echo T("Main Body"); ?></td>
		<th valign="top"><?php echo T("Navigation"); ?></td>
		<th valign="top"><?php echo T("Tabs"); ?></td>
	</tr>
	</tbody class="unstriped">
	
	<tbody id="part2" class="striped">
	<tr>
		<td nowrap="true"><label><?php echo T("Background Color:"); ?></label</td>
		<td valign="top">
			<input id="title_bg" name="title_bg" type="color" size="20" required aria-required="true">
		</td>
		<td valign="top">
			<input id="primary_bg" name="primary_bg" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt1_bg" name="alt1_bg" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt2_bg" name="alt2_bg" type="color" size="10" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td nowrap="true"><label><?php echo T("Font Face:"); ?></label></td>
		<td valign="top">
			<input id="title_font_face" name="title_font_face" type="text" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="primary_font_face" name="primary_font_face" type="text" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt1_font_face" name="alt1_font_face" type="text" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt2_font_face" name="alt2_font_face" type="text" size="10" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td nowrap="true"><label><?php echo T("Font Size:"); ?></label></td>
		<td valign="top">
			<input id="title_font_size" name="title_font_size" type="number" size="2" required aria-required="true">px
			<input id="title_font_bold" name="title_font_bold" checked type="checkbox"><lable for="titleFontBold"><?php echo T("bold");?></label>
		</td>
		<td valign="top">
			<input id="primary_font_size" name="primary_font_size" type="number" size="2" required aria-required="true">px
		</td>
		<td valign="top">
			<input id="alt1_font_size" name="alt1_font_size" type="number" size="2" required aria-required="true">px
		</td>
		<td valign="top">
			<input id="alt2_font_size" name="alt2_font_size" type="number" size="2" required aria-required="true">px
			<input id="alt2_font_bold" name="alt2_font_bold" checked type="checkbox"><lable for="alt2FontBold"><?php echo T("bold");?></label>
		</td>
	</tr>
	<tr>
		<td nowrap="true"><label><?php echo T("Font Color:"); ?></label></td>
		<td valign="top">
			<input id="title_font_color" name="title_font_color" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="primary_font_color" name="primary_font_color" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt1_font_color" name="alt1_font_color" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt2_font_color" name="alt2_font_color" type="color" size="10" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td nowrap="true"><label><?php echo T("Link Color:"); ?></label></td>
		<td valign="top">&nbsp;</td>
		<td valign="top">
			<input id="primary_link_color" name="primary_link_color" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt1_link_color" name="alt1_link_color" type="color" size="10" required aria-required="true">
		</td>
		<td valign="top">
			<input id="alt2_link_color" name="alt2_link_color" type="color" size="10" required aria-required="true">
		</td>
	</tr>
	<tr>
		<td nowrap="true"><label><?php echo T("Align:"); ?></label></td>
		<td valign="top">
			<select id="title_align" name="title_align">
				<option value="left">Left</option>
				<option value="center">Center</option>
				<option value="right">Right</option>
			</select>
		</td>
		<td colspan="3" valign="top">&nbsp;</td>
	</tr>
	</tbody>
	
	<tfoot>
	<tr>
		<td colspan="5">
			<input type="hidden" id="cat" name="cat" value="themes">
			<input type="hidden" id="mode" name="mode" value="">
			<input type="hidden" id="themeid" name="themeid" value="">
			<ul id="btnRow">
		    <li><input type="submit" id="addBtn" value="Add" /></li>
		    <li><input type="submit" id="updtBtn" value="Update" /></li>
		    <li><input type="button" id="cnclBtn" value="Cancel" /></li>
		    <li><input type="submit" id="deltBtn" value="Delete" /></li>
			</ul>
		</td>
	</tr>
	</tfoot>
</table>
</fieldset>
</form>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<p class="note">
	<?php echo T("Note:"); ?><br /><?php echo T('No delete on active theme'); ?>
</p>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "themeJS.php"));
?>	
