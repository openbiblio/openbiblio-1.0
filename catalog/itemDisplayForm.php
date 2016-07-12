<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
	<fieldset>
		<legend><?php echo T("Biblio Information"); ?> ( #<span id="theBibId"></span> )</legend>
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
				<?php if (!(strtolower($tab) == 'opac' || strtolower($tab) == 'working' || strtolower($tab) == 'user' || strtolower($tab) == 'circulation' )){ ?>
					<th><?php echo T("Function"); ?></th>
				<?php } ?>
				<?php
					if($_SESSION['multi_site_func'] > 0){
						echo "<th id=\"siteFld\" align=\"center\" nowrap=\"yes\">" . T("Site") . "</th>";
					}
				?>
				<th><?php echo T("Barcode"); ?></th>
				<th><?php echo T("Status"); ?></th>
				<th><?php echo T("Status Dt"); ?></th>
				<th><?php echo T("Due Back"); ?></th>
				<th><?php echo T("Description"); ?></th>
		</tr>
		</thead>
		<tbody id="copies" class="striped"></tbody>
		</table>
	</fieldset>
<?php
	include_once ("../shared/jsLibJs.php");
	include_once(REL(__FILE__,'../catalog/itemDisplayJs.php'));
?>
