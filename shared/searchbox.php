<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
?>
<form id="catalog_search" name="catalog_search" method="get" action="../shared/biblio_search.php">
	<input type="hidden" name="sortBy" value="title" />
	<input type="hidden" name="tab" value="<?php echo $tab; ?>" />
	<div id="search_main">
		<h3><label><?php echo T("Search Catalog"); ?></label></h3>
		<select id="searchType" name="searchType">
			<option value="keyword" selected="selected"><?php echo T("Keyword"); ?></option>
			<option value="title"><?php echo T("Title"); ?></option>
			<option value="subject"><?php echo T("Subject"); ?></option>
			<option value="series"><?php echo T("Series"); ?></option>
			<option value="publisher"><?php echo T("Publisher"); ?></option>
			<option value="callno"><?php echo T("Item Number"); ?></option>
		</select>
		<input id="searchText" type="text" name="searchText" size="30" maxlength="256" />
		<input type="submit" value="Search" class="button" />
	</div>
	<div id="search_limiters">
		<h3><?php echo T("Limit Search Results") ?></h3>
		<label for="mediaType"><?php echo T("Media Type:") ?></label>
				<?php
					$mattypes = new MaterialTypes;
					echo inputfield('select', 'mediaType', 'all', NULL, $mattypes->getSelect(true));
				?>
		<br /><br />
		<label><?php echo T("Audience Level:"); ?></label>
				<select name="audienceLevel">
					<option value="all" selected="selected"><?php echo T("All"); ?></option>
					<option value="K"><?php echo T("Kindergarten"); ?></option>
					<option value="P"><?php echo T("Primary"); ?></option>
					<option value="I"><?php echo T("Intermediate"); ?></option>
					<option value="J"><?php echo T("Junior High"); ?></option>
					<option value="S"><?php echo T("Senior High"); ?></option>
					<option value="A"><?php echo T("Adult"); ?></option>
				</select>
		<br /><br />
		<label><?php echo T("Production Date:"); ?></label>
		<label for="from"><?php echo T("From Year:");?></label>
			<?php echo inputfield('text','from','',array('size'=>'4')); ?>
		<label for="to"><?php echo T("To Year:"); ?></label>
			<?php echo inputfield('text','to','',array('size'=>'4')); ?>
	</div>
</form>
