<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

echo "in srchForms.php";
	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	session_cache_limiter(null);

	$tab = strToLower($_REQUEST[tab]);
	if(empty($tab)) {
		$tab = "cataloging";
		$title = T("Existing Items");
	} else if ($tab == 'user'){
		$title = T("Library Catalog");
	} else if ($tab == 'opac'){
		$title = T("Library Catalog");
	} else if ($tab == 'rpt'){
		$title = T("ReportSelection");
	}

	$nav = "localSearch";
	$menu = $tab . '/search/catalog';
	$focus_form_name = "barcodesearch";
	$focus_form_field = "ph_searchText";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}

	Nav::node($menu, T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=cataloging');
	Nav::node($menu, T("MARC Output"), '../shared/layout.php?name=marc&rpt=Report&tab=cataloging');
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>$title));

	//print_r($_SESSION); // for debugging
?>

<div id="crntMbrDiv">to be filled by server</div>

<p id="errSpace" class="error">to be filled by server</p>

<!-- ------------------------------------------------------------------------ -->
<div id="searchDiv">
<form id="barcodeSearch" name="barcodeSearch" method="post">
<fieldset>
	<legend><?php echo T("Find Item by Barcode"); ?></legend>
	<label for="bc_searchBarcd"><?php echo T("Barcode");?>:</label>
	<input type="text" id="bc_searchBarcd" name="searchBarcd" size="20" />
	<input type="submit" id="barcdSrchBtn" name="barcdSrchBtn" value="<?php echo T("Search"); ?>" class="srchByBarcdBtn" />
	<input type="hidden" id="bc_searchType" name="searchType" value="barcodeNmbr" />
	<input type="hidden" id="bc_sortBy" name="sortBy" value="default" />
</fieldset>
</form>

<form id="phraseSearch" name="phraseSearch" method="post" >
<fieldset>
<legend><?php echo T("Search Catalog"); ?></legend>
<table>
	<tbody id="mainTxtSrch">
	<tr>
		<td colspan="3">
			<select id="ph_searchType" name="searchType" >
				<option value="title"><?php echo T("Title"); ?></option>	
				<option value="author"><?php echo T("Author"); ?></option>
				<option value="subject"><?php echo T("Subject"); ?></option>	
				<option value="keyword" selected><?php echo T("Keyword"); ?></option>
				<option value="series"><?php echo T("Series"); ?></option>	
				<option value="publisher"><?php echo T("Publisher"); ?></option>
				<option value="callno"><?php echo T("Call Number"); ?></option>
				<option value="id"><?php echo T("Id"); ?></option>
			</select>
			<input type="text" id="ph_searchText" name="searchText" size="20" maxlength="256" />
			<input type="submit" id="phraseSrchBtn" name="phraseSrchBtn" value="<?php echo T("Search"); ?>" class="phraseSrchBtnBtn" />
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
	    <label for="advanceQ"><?php echo T("Advanced Search?"); ?></label>
			<input id="advanceQ" name="advanceQ" type="checkbox" value="Y" />
		</td>
	</tr>
	</tbody>
	<!-- visiblity below here depends on above checkbox -->
	<tbody id="advancedSrch">
	<tr>
		<td nowrap="true" colspan="3">
			<label for="sortBy"><?php echo T("Sort by"); ?>: </label>
			<select id="sortBy" name="sortBy">
				<option value="author"><?php echo T("Author"); ?></option>
				<option value="callno"><?php echo T("Call Number"); ?></option>
				<option value="title" selected><?php echo T("Title"); ?></option>
			</select>
		</td>
	</tr>
	<tr>
	  <td colspan="3">
	  <fieldset>
	  <legend><?php echo T("Limit Search Results"); ?></legend>
	  <table border="0">
		<tr class="searchRow">
			<td><label for="srchMediaTypes"><?php echo T("Media Type"); ?>: </label></td>
			<td><select id="srchMediaTypes" name="materialCd"></select></td>
		</tr>
		<!--tr id="marcTagsRow" class="searchRow">
			<td><label for="srchMarcTags"><?php echo T("MARCTags"); ?>: </label></td>
			<td><select id="srchMarcTags" name="marcTag"></select></td>
		</tr-->
		<tr class="searchRow">
			<td><label for="srchCollections"><?php echo T("Collection"); ?>: </label></td>
			<td><select id="srchCollections" name="collectionCd"></select></td>
		</tr>
		<tr class="searchRow">
			<td><label for="audienceLevel"><?php echo T("Audience Level"); ?>: </label></td>
			<td>
				<select id="audienceLevel" name="audienceLevel">
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
			<td><label><?php echo T("Production Date"); ?>:</label><br /></td>
			<td><label for="from"><?php echo T("From Year");?>:</label>
						<input id="from" name="from" type="number" size="4" min="1850" max="2099" />
					<br />
					<label for="to"><?php echo T("To Year"); ?>:</label>
						<input id="to" name="to" type="number" size="4" min="1850" max="2099" />
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
	<h5><?php echo T("SearchResults"); ?> &quot;<span id="srchRsltTitl"></span>&quot;</h5>
	<!--div id="results_found">
		<?php //echo T("biblioSearchMsg", array('nrecs'=>$rpt->count(), 'start'=>1, 'end'=>25)); ?>
	</div-->
	<table>
	<tr>
		<td colspan="3">
			<ul class="pagBtns">
				<li>
					<input type="button" class="listGobkBtn" value="<?php echo T("Go Back"); ?>" />
					<input type="button" id="addList2CartBtn" value="<?php echo T("Add List To Cart"); ?>" />
				</li>
				<li>
					<input type="button" class="goPrevBtn PgBtn" value="<?php echo T("Previous Page"); ?>">
					<span class="rsltQuan"></span>
					<input type="button" class="goNextBtn PgBtn" value="<?php echo T("Next Page"); ?>">
				</li>
			</ul>
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
					<li><img src="../images/circle_green.png" class="flgDot"/> <?php echo T("Available"); ?></li>
					<li><img src="../images/circle_orange.png" class="flgDot"/> <?php echo T("Available elsewhere"); ?></li>
					<li><img src="../images/circle_blue.png" class="flgDot"/> <?php echo T("Not on loan/on hold"); ?></li>
					<li><img src="../images/circle_red.png" class="flgDot"/> <?php echo T("On loan/not available"); ?></li>
					<li><img src="../images/circle_purple.png" class="flgDot"/> <?php echo T("Available online"); ?></li>
				</ul>
				<?php } ?>
			</fieldset>
		</td>
	<tr>
		<td colspan="3">
			<ul class="pagBtns">
				<li><input type="button" class="listGobkBtn" value="<?php echo T("Go Back"); ?>" /></li>
				<li>
					<input type="button" class="goPrevBtn PgBtn" value="<?php echo T("Previous Page"); ?>">
					<span class="rsltQuan"></span>
					<input type="button" class="goNextBtn PgBtn" value="<?php echo T("Next Page"); ?>">
				</li>
			</ul>
		</td>
	</tr>
	</table>
</div

<!-- ------------------------------------------------------------------------ -->
<div id="biblioDiv">
	<p id="rsltMsg" class="error"></p>
	<ul class="btnRow">
		<?php if (!(($tab == 'opac') || ($tab == 'rpt'))) { ?>
			<li><input type="button" class="bibGobkBtn" value="<?php echo T("Go Back"); ?>" /></li>
		<?php } ?>
		<li><input type="button" id="marcBtn" value=""></li>
		<li><input type="button" id="addItem2CartBtn" value="<?php echo T("Add To Cart"); ?>" /></li>
			<?php if (($_SESSION["hasCatalogAuth"]) && ($tab == 'cataloging')) {?>
			<li><input type="button" id="biblioEditBtn" value="<?php echo T("Edit This Item"); ?>"></li>
			<?php if ($_SESSION['show_item_photos'] == 'Y') { ?>
				<li><input type="button" id="photoEditBtn" value="<?php echo T("Edit This Photo"); ?>"></li>
				<li><input type="button" id="photoAddBtn" value="<?php echo T("Add New Photo"); ?>"></li>
			<?php } ?>
			<li><input type="button" id="biblioDeleteBtn" value="<?php echo T("Delete This Item"); ?>"></li>
		<?php }?>
	</ul>
		
	<?php include(REL(__FILE__,"../catalog/itemDisplayForm.php")); ?>

	<ul class="btnRow">
		<?php if (!(($tab == 'opac') || ($tab == 'rpt'))) { ?>
			<li><input type="button" class="bibGobkBtn" value="<?php echo T("Go Back"); ?>"></li>
		<?php } ?>
		<?php if (!($tab != 'cataloging' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))) { ?>
			<li><input type="button" id="addNewBtn" class="button" value="<?php echo T("Add New Copy"); ?>"></li>
		<?php } ?>
	</ul>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="itemEditorDiv">
  <form id="biblioEditForm" name="biblioEditForm" >
		<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
		<input type="button" class="itemGobkBtn" value="<?php echo T("Go Back"); ?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="onlnUpdtBtn" class="button" value="<?php echo T("Fetch On-line Data"); ?>" />
		<input type="button" id="onlnDoneBtn" class="button" value="<?php echo T("Search Complete"); ?>" />


		<?php require(REL(__FILE__,"../catalog/itemEditorForm.php")); ?>
	

		<!--input type="submit" id="itemSubmitBtn" value="<?php echo T("Submit"); ?>" /-->
		<input type="button" id="itemSubmitBtn" value="<?php echo T("Submit"); ?>" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" value="<?php echo T("Go Back"); ?>" class="itemGobkBtn" />
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="copyEditorDiv">
	<?php require_once(REL(__FILE__,"../catalog/copyEditorForm.php"));?>
</div>

<!-- ------------------------------------------------------------------------ -->
<?php if ($tab == 'cataloging') { ?>
<div id="photoEditorDiv">
	<?php require_once(REL(__FILE__,"../catalog/photoEditorForm.php"));?>

	<ul class="btnRow">
		<li><input type="button" class="gobkFotoBtn" value="<?php echo T("Go Back"); ?>" /></li>
		<li><input type="submit" id="addFotoBtn" value="<?php echo T("Add New"); ?>" /></li>
		<li><input type="button" id="updtFotoBtn" value="<?php echo T("Update"); ?>" /></li>
		<li><input type="button" id="deltFotoBtn" value="<?php echo T("Delete"); ?>" /></li>
	</ul>
</div>
<?php } ?>

<!-- ------------------------------------------------------------------------ -->
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	include_once(REL(__FILE__,'srchJs.php'));
?>

</body>
</html>
