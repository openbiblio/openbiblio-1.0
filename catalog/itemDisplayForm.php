<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
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
