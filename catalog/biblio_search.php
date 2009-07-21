<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "searchform";
	$focus_form_name = "barcodesearch";
	$focus_form_field = "searchText";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Local Search'));

	include_once(REL(__FILE__,'biblio_searchJs.php'));
	
?>
	<h3><?php echo T("Local Search"); ?></h3>

<div id="crntMbrDiv">to be filled by server</div>

<p class="error">to be filled by server</p>

<div id="searchDiv">
<form id="barcodeSearch" name="barcodeSearch" method="post">
<fieldset>
<legend><?php echo T("Find Item by Barcode Number"); ?></legend>
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<label for="searchText"><?php echo T("Barcode Number:");?></label>
			<?php echo inputfield('text','searchText','',array('size'=>'20','maxlength'=>'20')); ?>
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
		<tr>
			<td colspan="2" valign="top" nowrap="yes" align="left">
				<label for="mediaType"><?php echo T("Media Type:"); ?> </label>
					<span id="srchMatTypes">to be filled by server</span>
						<br /><br />
				<label for="audienceLevel"><?php echo T("Audience Level:"); ?></label>
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
			<td colspan="1" valign="top" nowrap="yes" align="center">
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
		<tr>
		<td colspan="3" align="right" class="primary">
			<label><?php echo T("Production Date:"); ?></label>
			<label for="from"><?php echo T("From Year:");?></label>
				<?php echo inputfield('text','from',null,array('size'=>'4'))?>
			<label for="to"><?php echo T("To Year:"); ?></label>
				<?php echo inputfield('text','to',null,array('size'=>'4'))?>

		</td>
		</tr>
		</table>
		</fieldset>
		</td>
	<tr>
</table>
</fieldset>
</form>
</div>

<div id="biblioListDiv">
	<h4><?php echo T("Search Results"); ?></h4>
	<div class="results_found">
		<?php //echo T('biblioSearchMsg', array('nrecs'=>$rpt->count(), 'start'=>1, 'end'=>25)); ?>
	</div>
	<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>" />
	<span id="rsltQuan"></span>
	<div id="srchRsltsDiv"></div>
	<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>">
</div

<div id="biblioDiv">
	<p id="rsltMsg" class="error"></p>
	<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" id="marcBtn" class="button" value="<?php echo T('View Marc Tags'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" id="biblioEditBtn" class="button" value="<?php echo T('Edit This Item'); ?>">
	<fieldset>
		<legend><?php echo T("Biblio Information"); ?></legend>
		<table id="biblioTbl">
		<tbody id="biblio" class="striped"></tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend><?php echo T("Copy Information"); ?></legend>
		<table id="copyList">
		<tr>
			<th>Function</th><th>Barcode</th><th>Descrition</th>
									<th>Status</th><th>Status Date</th><th>Due Back</th>
		</tr>
		<tbody id="copies" class="striped"></tbody>
		</table>
	</fieldset>
	<input type="button" class="gobkBtn button" value="<?php echo T('Go Back'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" id="addNewBtn" class="button" value="<?php echo T('Add New Copy'); ?>">
</div>

<div id="itemEditorDiv">
  <form id="biblioEditForm" name="biblioEditForm" method="POST" >
	<?php include(REL(__FILE__,"../catalog/item_editor.php")); ?>
	</form>
</div>

<div id="copyEditorDiv">
	<?php include_once(REL(__FILE__,"biblio_copy_editor.php"));?>
</div>

</body>
</html>
