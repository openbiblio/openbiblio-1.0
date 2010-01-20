<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
?>
<form name="phrasesearch" method="get" action="../shared/biblio_search.php">
<fieldset>
<legend><?php echo T("Search Catalog"); ?></legend>
<table class="primary" width="100%">
	<!--tr>
		<th colspan="2" valign="top" nowrap="yes" align="left">
			<?php //echo T("Search Catalog"); ?>
		</th>
	</tr-->
	<tr>
		<td nowrap="true" class="primary">
			<select id="searchType" name="searchType">
				<option value="keyword" selected="selected"><?php echo T("Keyword"); ?></option>
				<option value="title"><?php echo T("Title"); ?></option>
				<option value="subject"><?php echo T("Subject"); ?></option>
				<option value="series"><?php echo T("Series"); ?></option>
				<option value="publisher"><?php echo T("Publisher"); ?></option>
				<option value="callno"><?php echo T("Item Number"); ?></option>
			</select>
		</td>
		<td class="primary">
			<?php echo inputfield('text','searchText','',
														array('size'=>'30','maxlength'=>'256','style'=>'width: 100%')); ?>
			<?php //echo inputfield('hidden','sortBy','title'); ?>
			<?php echo inputfield('hidden','tab',$tab); ?>
			<?php echo inputfield('hidden','lookup',$lookup); ?>
		</td>
		<td id="searchsubmit">
			<input type="submit" value="<?php echo T("Search"); ?>" class="button" />
		</td>
	</tr>
	<tr>
	  <td colspan="3">
	    <label for="advanceQ"><?php echo T('Advanced Search?'); ?></label>
			<?php echo inputfield('checkbox','advanceQ','Y',null,null); ?>
		</td>
	</tr>
	<tr>
	  <td colspan="3">
	  <fieldset id="advancedSrch">
	  <legend><?php echo T("Limit Search Results"); ?></legend>
	  <table border="0">
		<tr>
			<td colspan="2" valign="top" nowrap="yes" align="left">
				<label for="mediaType">
				<?php
					echo T("Media Type:");
					$mattypes = new MaterialTypes;
					echo inputfield('select', 'mediaType', 'all', NULL, $mattypes->getSelect(true));
				?>
				</label>
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
<script>
	$(document).ready(function () {
		$('#advancedSrch').hide();
		$('#advanceQ').bind('click',null,function(){
			if ($('#advanceQ:checked').val() == 'Y')
				$('#advancedSrch').show();
			else
				$('#advancedSrch').hide();
		});
	});
</script>