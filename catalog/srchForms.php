<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

// I beleive this is now taken care of for all $_session stuff in common.php
// In case of OPAC
//	if(empty($_SESSION['show_detail_opac']))
//		$_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');
	
	session_cache_limiter(null);

	if(empty($_REQUEST[tab]))
		$tab = "cataloging";
	else
	  $tab = $_REQUEST[tab];

	$nav = "localSearch";
	$focus_form_name = "barcodesearch";
	$focus_form_field = "searchBarcd";

	if (strtolower($tab) == 'opac') {
		Page::header_opac(array('nav'=>$nav, 'title'=>''));
	}
	else {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
		
		Nav::node('cataloging/search/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=cataloging');
		Nav::node('cataloging/search/catalog', T("MARC Output"), '../shared/layout.php?name=marc&rpt=Report&tab=cataloging');
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Existing Items'));
	}
	
?>

<!--h3><?php echo T("Existing Items"); ?></h3-->
<?php //print_r($_SESSION); // for debugging ?>

<div id="crntMbrDiv">to be filled by server</div>

<p id="errSpace" class="error">to be filled by server</p>

<!-- ------------------------------------------------------------------------ -->
<div id="searchDiv">
<form id="barcodeSearch" name="barcodeSearch" method="post">
<fieldset>
<legend><?php echo T("Find Item by Barcode Number"); ?></legend>
<table>
	<tr>
		<td nowrap="true">
			<label for="searchBarcd"><?php echo T("Barcode Number:");?></label>
			<input id="searchBarcd" name="searchBarcd" type="text" size="20" />
			<input id="srchByBarcd" name="srchByBarcd" type="submit" value="<?php echo T("Search"); ?>" class="srchByBarcdBtn" />
		</td>
	</tr>
	<tr>
		<td>
			<input id="searchType" name="searchType" type="hidden" value="barcodeNmbr" />
			<input id="sortBy" name="sortBy" type="hidden" value="default" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<form id="phraseSearch" name="phraseSearch" method="post" >
<fieldset>
<legend><?php echo T("Search Catalog"); ?></legend>
<table>
	<tbody id="mainTxtSrch">
	<tr>
		<td nowrap="true" colspan="3">
			<select id="searchType" name="searchType" >
				<option value="title"><?php echo T("Title"); ?></option>	
				<option value="author"><?php echo T("Author"); ?></option>
				<option value="subject"><?php echo T("Subject"); ?></option>	
				<option value="keyword" selected><?php echo T("Keyword"); ?></option>
				<option value="series"><?php echo T("Series"); ?></option>	
				<option value="publisher"><?php echo T("Publisher"); ?></option>
				<option value="callno"><?php echo T("Item Number"); ?></option>
			</select>
			<input id="searchText" name="searchText" type="text" size="20" maxlength="256" />
			<input id="srchByPhrase" name="srchByPhrase" type="submit" value="<?php echo T("Search"); ?>" class="srchByPhraseBtn" />
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<!--input id="sortBy" name="sortBy" type="hidden" value="title" /-->
			<input id="tab" name="tab" type="hidden" value="<?php echo $tab; ?>" />
			<input id="lookup" name="lookup" type="hidden" value="<?php echo $lookup; ?>" />
		</td>
	</tr>
	<tr>
	  <td colspan="3">
	    <label for="advanceQ"><?php echo T('Advanced Search?'); ?></label>
			<input id="advanceQ" name="advanceQ" type="checkbox" value="Y" />
		</td>
	</tr>
	</tbody>
	<!-- visiblity below here depends on above checkbox -->
	<tbody id="advTxtSrch">
	<tr>
	  <td colspan="3">
	  <fieldset id="advancedSrch">
	  <legend><?php echo T("Limit Search Results"); ?></legend>
	  <table border="0">
		<tr class="searchRow">
			<td><label for="mediaType"><?php echo T("Media Type:"); ?> </label></td>
			<td>
					<span id="srchMatTypes">to be filled by server</span>				
			</td>
			<td rowspan="3" valign="top" nowrap="yes" align="center">
			  <fieldset style="margin:0; margin-bottom:5px;">
				<legend><?php echo T("Sort by: "); ?></legend>
					<select id="sortBy" name="sortBy">
						<option value="author">Author</option>
						<option value="callno">Call Number</option>
						<option value="title" selected>Title</option>
					</select>
			  </fieldset>
			</td>
		</tr>
		<tr class="searchRow">
			<td><label for="audienceLevel"><?php echo T("Audience Level:"); ?></label></td>
			<td>
				<select id="audienceLevel" name="audienceLevel">
					<option value="K"><?php echo T("Kindergarten"); ?></option>
					<option value="P"><?php echo T("Primary"); ?></option>
					<option value="I"><?php echo T("Intermediate"); ?></option>
					<option value="J"><?php echo T("Junior High"); ?></option>
					<option value="S"><?php echo T("SeniorHigh"); ?></option>
					<option value="A"><?php echo T("Adult"); ?></option>
					<option value="all" selected><?php echo T("All"); ?></option>
				</select>
			</td>
		</tr>
		<tr class="searchRow">
			<td><label for="srchSites"><?php echo T("Search Site"); ?>:</label></td>
			<td>
				<select name="srchSites" id="srchSites">
				  <option id= value="all" selected="selected">All<option>
					<option>to be filled by server</option>
				</select>
			</td>
		</tr>
		<tr class="searchRow">
			<td><label><?php echo T("Production Date:"); ?></label></td>
			<td colspan="2" >
				<label for="from"><?php echo T("From Year:");?></label>
					<input id="from" name="from" type="number" size="4" min="1900" max="2099" />
				<label for="to"><?php echo T("To Year:"); ?></label>
					<input id="to" name="to" type="number" size="4" min="1900" max="2099" />
			</td>
		</tr>
		</tbody>
		</table>
		</fieldset>
		</td>
	</tr>
</table>
</fieldset>
</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="biblioListDiv">
	<h4><?php echo T("Search Results"); ?></h4>
	<div id="results_found">
		<?php //echo T('biblioSearchMsg', array('nrecs'=>$rpt->count(), 'start'=>1, 'end'=>25)); ?>
	</div>
	<table width="100%">
	<tr>
		<td>
			<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>" />
		</td>
		<td>
			<input type="button" id="addList2CartBtn" class="button" value="<?php echo T('Add List To Cart'); ?>" />
		</td>
		<td width="80%" align="right">
			<input type="button" class="goPrevBtn PgBtn" value="<?php echo T('Previous Page'); ?>">
			<span class="rsltQuan"></span>
			<input type="button" class="goNextBtn PgBtn" value="<?php echo T('Next Page'); ?>">
		</td>
	</tr>
	<tr>
	  <td colspan="3">
			<fieldset>
				<span id="resultsArea"></span>
				<fieldset>
					<table id="listTbl">
						<tbody id="srchRslts" class="striped">
						</tbody>
					</table>
				</fieldset>
				<?php
					if($_SESSION['show_detail_opac'] == "Y"){
				?>
				<ul id="flagInfo">
					<li><img src="../images/circle_green.png" class="flgDot"/> <?php echo T('Available'); ?></li>
					<li><img src="../images/circle_orange.png" class="flgDot"/> <?php echo T('Available elsewhere'); ?></li>
					<li><img src="../images/circle_blue.png" class="flgDot"/> <?php echo T('Not on loan/on hold'); ?></li>
					<li><img src="../images/circle_red.png" class="flgDot"/> <?php echo T('On loan/not available'); ?></li>
				</ul>
				<?php } ?>
			</fieldset>
		</td>
	<tr>
		<td>
			<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>" />
		</td>
		<td>&nbsp;</td>
		<td width="80%" align="right">
			<input type="button" class="goPrevBtn PgBtn" value="<?php echo T('Previous Page'); ?>">
			<span class="rsltQuan"></span>
			<input type="button" class="goNextBtn PgBtn" value="<?php echo T('Next Page'); ?>">
		</td>
	</tr>
	</table>
</div

<!-- ------------------------------------------------------------------------ -->
<div id="biblioDiv">
	<p id="rsltMsg" class="error"></p>

	<ul class="btnRow">
		<li><input type="button" class="gobkBtn" value="<?php echo T('Go Back'); ?>" /></li>
		<li><input type="button" id="marcBtn" value=""></li>
		<li><input type="button" id="addItem2CartBtn" value="<?php echo T('Add To Cart'); ?>" /></li>
		<?php if (!(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))) {?>
		<li><input type="button" id="biblioEditBtn" value="<?php echo T('Edit This Item'); ?>"></li>
		<li><input type="button" id="photoEditBtn" value="<?php echo T("Edit This Photo"); ?>"></li>
		<li><input type="button" id="photoAddBtn" value="<?php echo T("Add New Photo"); ?>"></li>
		<li><input type="button" id="biblioDeleteBtn" value="<?php echo T('Delete This Item'); ?>"></li>
		<?php }?>
	</ul>
		
	<fieldset>
		<legend><?php echo T("Biblio Information"); ?></legend>
		<div id="bibBlks">
			<div id="bibBlkA">
				<table id="biblioTbl" border="1">
					<tbody id="biblio" class="striped"></tbody>
				</table>
			</div>
			<div id="bibBlkB"></div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo T("Copy Information"); ?></legend>
		<table id="copyList">
		<thead>
		<tr>
				<?php if (!(strtolower($tab) == "opac" || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))){ ?>
					<th nowrap="yes" align="center"><?php echo T("Function"); ?></th>
				<?php } ?>
				<th align="center" nowrap="yes"><?php echo T("Barcode"); ?></th>
				<th align="center" nowrap="yes"><?php echo T("Description"); ?></th>
				<?php
					if($_SESSION['multi_site_func'] > 0){
						echo "<th id=\"siteFld\" align=\"center\" nowrap=\"yes\">" . T("Site") . "</th>";
					}
				?>
				<th align="center" nowrap="yes"><?php echo T("Status"); ?></th>
				<th align="center" nowrap="yes"><?php echo T("Status Dt"); ?></th>
				<th align="center" nowrap="yes"><?php echo T("Due Back"); ?></th>
		</tr>
		</thead>
		<tbody id="copies" class="striped"></tbody>
		</table>
	</fieldset>
	
	<ul class="btnRow">
		<li><input type="button" class="gobkBtn" value="<?php echo T('Go Back'); ?>"></li>
		<?php if (!(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))) { ?>
			<li><input type="button" id="addNewBtn" class="button" value="<?php echo T('Add New Copy'); ?>"></li>
		<?php } ?>
	</ul>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="itemEditorDiv">
  <form id="biblioEditForm" name="biblioEditForm" >
		<p class="note"><?php echo T("Fields marked are required"); ?></p>
		<input type="button" class="itemGobkBtn" value="<?php echo T('Go Back'); ?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="onlnUpdtBtn" class="button" value="<?php echo T('Fetch On-line Data'); ?>" />
		<input type="button" id="onlnDoneBtn" class="button" value="<?php echo T('Search Complete'); ?>" />

		<?php include(REL(__FILE__,"../catalog/item_editor.php")); ?>
	
		<input type="submit" id="itemSubmitBtn" value="<?php echo T("Submit"); ?>" />
		<!--input type="button" id="itemSubmitBtn" value="<?php echo T("Submit"); ?>" /-->
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" value="<?php echo T("Go Back"); ?>" class="itemGobkBtn" />
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="copyEditorDiv">
	<?php include_once(REL(__FILE__,"copyEditorForm.php"));?>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="photoEditorDiv">
	<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
	<p id="fotoMsg" class="error"></p>

	<form id="fotoForm" name="fotoForm" enctype="multipart/form-data" method="POST" >
	<ul class="btnRow">
		<li><input type="button" class="gobkFotoBtn" value="<?php echo T('Go Back'); ?>" /></li>
	</ul>
	
	<fieldset>
		<legend id="fotoEdLegend"></legend>
		<table id="editTbl" role="presentation">
				<tr id="imgSrce">
					<td>
						<label for="fotoSrce"><?php echo T("Source"); ?>:</label>
						<input type="file" id="fotoSrce" value="" name="image" size="32" required aria-required="true" />
						<span class="reqd">*</span>
					</td>
					<td id="fotoBlkB" rowspan="3"></td>
				</tr>
				<tr>
					<td>
						<label for="fotoFile"><?php echo T("Name"); ?>:</label>
						<input type="text" id="fotoFile" name="url" size="32" 
								pattern="(.*?)\.(jpg|jpg|png)$" required aria-required="true" 
								title="Only jpg and png files are acceptable." />
						<span class="reqd">*</span>
					</td>
				</tr>
				<tr>
					<td>
						<label for="fotoCapt"><?php echo T("Caption"); ?>:</label>
						<input type="text" id="fotoCapt" name="caption" size="32" />
					</td>
				</tr>
		</table>
	</fieldset>
	
	<input type="hidden" id="fotoBibid" name="bibid" value="" />
	<input type="hidden" id="fotoMode" name="mode" value="" />
	<input type="hidden" id="fotoType" name="type" value="Link" />
	<input type="hidden" id="fotoImgUrl" name="imgurl" value="Link" />
	<input type="hidden" id="fotoPos" name="position" value="0" />
	
	<ul class="btnRow">
		<li><input type="button" class="gobkFotoBtn" value="<?php echo T('Go Back'); ?>" /></li>
		<li><input type="button" id="addFotoBtn" value="<?php echo T('Add New'); ?>" /></li>
		<li><input type="button" id="updtFotoBtn" value="<?php echo T('Update'); ?>" /></li>
		<li><input type="button" id="deltFotoBtn" value="<?php echo T('Delete'); ?>" /></li>
	</ul>
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
	
	include_once('../shared/ajaxFileUpload/ajaxfileupload.js');
	include_once(REL(__FILE__,'./srchJs.php'));
?>	

