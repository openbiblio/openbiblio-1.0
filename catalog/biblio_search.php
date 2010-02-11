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
	
	include_once(REL(__FILE__,'/biblio_searchJs.php'));
	
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
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<label for="searchText"><?php echo T("Barcode Number:");?></label>
			<?php echo inputfield('text','searchBarcd','',array('size'=>'20','maxlength'=>'20')); ?>
			<?php echo inputfield('hidden','searchType','barcodeNmbr'); ?>
			<?php echo inputfield('hidden','sortBy','default'); ?>
			<input id="srchByBarcd" type="submit" value="<?php echo T("Search"); ?>" class="button srchBtn" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<form id="phraseSearch" name="phraseSearch" method="post" >
<fieldset>
<legend><?php echo T("Search Catalog"); ?></legend>
<table class="primary" width="100%">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo inputfield('select','searchType','title',null,array(
														 'title'=>T("Title")
														,'author'=>T('Author')
														,'subject'=>T("Subject")
														,'keyword'=>T("Keyword")
														,'series'=>T("Series")
														,'publisher'=>T("Publisher")
														,'callno'=>T("Item Number")
														));
			?>
		</td>
		<td class="primary">
			<?php echo inputfield('text','searchText','',
														array('size'=>'30','maxlength'=>'256','style'=>'width: 100%')); ?>
			<?php //echo inputfield('hidden','sortBy','title'); ?>
			<?php echo inputfield('hidden','tab',$tab); ?>
			<?php echo inputfield('hidden','lookup',$lookup); ?>
		</td>
		<td>
			<input id="srchByPhrase" type="submit" value="<?php echo T("Search"); ?>" class="button srchBtn" />
		</td>
	</tr>
	<tr>
	  <td colspan="3">
	    <label for="advanceQ"><?php echo T('Advanced Search?'); ?></label>
			<?php echo inputfield('checkbox','advanceQ','Y',null,null); ?>
		</td>
	</tr>
	<!-- visiblity below here depends on above checkbox -->
	<tr>
	  <td colspan="3">
	  <fieldset id="advancedSrch">
	  <legend><?php echo T("Limit Search Results"); ?></legend>
	  <table border="0">
		<tr class="searchRow">
			<td class="label">
				<label for="mediaType"><?php echo T("Media Type:"); ?> </label>
			</td>
			<td>
					<span id="srchMatTypes">to be filled by server</span>				
			</td>
			<td rowspan="3" valign="top" nowrap="yes" align="center">
			  <fieldset style="margin:0; margin-bottom:5px;">
				<legend><?php echo T("Sort by: "); ?></legend>
					<?php echo inputfield('select','sortBy','title',null,array(
											'author'=>'Author',
											'callno'=>'Call Number',
											'title' =>'Title'
										));
					?>
			  </fieldset>
			</td>
		</tr>
		<tr class="searchRow">
			<td class="label">
				<label for="audienceLevel"><?php echo T("Audience Level:"); ?></label>
			</td>
			<td>
					<?php echo inputfield('select','audienceLevel','all',null,array(
											'K' 	=>T("Kindergarten"),
											'P' 	=>T("Primary"),
											'I' 	=>T("Intermediate"),
											'J' 	=>T("Junior High"),
											'S' 	=>T("Senior High"),
											'A' 	=>T("Adult"),
											'all'	=>T("All"),
										));
				?>
			</td>
		</tr>
		<tr class="searchRow">
			<td class="label">
				<label for="srchSites"><?php echo T("Search Site"); ?>:</label>
			</td>
			<td>
				<select name="srchSites" id="srchSites">
				  <option id= value="all" selected="selected">All<option>
					<option>to be filled by server</option>
				</select>
			</td>
		</tr>
		<tr class="searchRow">
			<td class="label primary">
				<label><?php echo T("Production Date:"); ?></label>
			</td>
			<td colspan="2" >
				<label for="from"><?php echo T("From Year:");?></label>
					<?php echo inputfield('text','from',null,array('size'=>'4'))?>
				<label for="to"><?php echo T("To Year:"); ?></label>
					<?php echo inputfield('text','to',null,array('size'=>'4'))?>

			</td>
		</tr>
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
				<table>
				<tr>
					<td width="125"><img src="../images/circle_green.png"/> <?php echo T('Available'); ?></td>
					<td width="150"><img src="../images/circle_orange.png"/> <?php echo T('Available elsewhere'); ?></td>
					<td width="105"><img src="../images/circle_blue.png"/> <?php echo T('On hold'); ?></td>
					<td width="175"><img src="../images/circle_red.png"/> <?php echo T('On loan/not available'); ?></td>
				</tr>
				</table>
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
	<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" id="marcBtn" class="button" value="<?php echo T('View Marc Tags'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" id="addItem2CartBtn" class="button" value="<?php echo T('Add To Cart'); ?>" />
	<?php if (!(strtolower($tab) == 'opac' || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))) {?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="biblioEditBtn" class="button" value="<?php echo T('Edit This Item'); ?>">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="biblioDeleteBtn" class="button" value="<?php echo T('Delete This Item'); ?>">
	<?php }?>
	
	<fieldset>
		<legend><?php echo T("Biblio Information"); ?></legend>
		<table id="biblioTbl">
		<tbody id="biblio" class="striped"></tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend><?php echo T("Copy Information"); ?></legend>
		<table id="copyList">
		<thead>
		<tr>
				<?php if (!(strtolower($tab) == "opac" || ($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))){ ?>
					<th nowrap="yes"><?php echo T("Function"); ?></th>
				<?php } ?>
				<th align="left" nowrap="yes"><?php echo T("Barcode"); ?></th>
				<th align="left" nowrap="yes"><?php echo T("Description"); ?></th>
				<?php
					if($_SESSION['show_copy_site'] == "Y"){
						echo "<th id=\"siteFld\" align=\"left\" nowrap=\"yes\">" . T("Site") . "</th>";
					}
				?>
				<th align="left" nowrap="yes"><?php echo T("Status"); ?></th>
				<th align="left" nowrap="yes"><?php echo T("Status Dt"); ?></th>
				<th align="left" nowrap="yes"><?php echo T("Due Back"); ?></th>
		</tr>
		</thead>
		<tbody id="copies" class="striped"></tbody>
		</table>
	</fieldset>
	
	<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if (!(strtolower($tab) == 'opac' || !($_SESSION["hasCircAuth"] && !$_SESSION["hasCatalogAuth"]))) { ?>
	<input type="button" id="addNewBtn" class="button" value="<?php echo T('Add New Copy'); ?>">
<?php } ?>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="itemEditorDiv">
  <form id="biblioEditForm" name="biblioEditForm" method="POST" >
		<p class="note"><?php echo T("Fields marked are required"); ?></p>
		<input type="button" class="button itemGobkBtn" value="<?php echo T('Go Back'); ?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="onlnUpdtBtn" class="button" value="<?php echo T('Fetch On-line Data'); ?>" />
		<input type="button" id="onlnDoneBtn" class="button" value="<?php echo T('Search Complete'); ?>" />

	<?php include(REL(__FILE__,"../catalog/item_editor.php")); ?>
	
		<input type="submit" id="itemSubmitBtn" value="<?php echo T("Submit"); ?>" class="button" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" value="<?php echo T("Go Back"); ?>" class="button itemGobkBtn" />
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="copyEditorDiv">
	<?php include_once(REL(__FILE__,"biblio_copy_editor.php"));?>
</div>

</body>
</html>
