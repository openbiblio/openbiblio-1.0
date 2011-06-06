<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	#****************************************************************************
	#*  Checking for get vars.  Go back to form if none found.
	#****************************************************************************
	if (count($_REQUEST) == 0) {
		header("Location: ../catalog/index.php");
		exit();
	}


	#****************************************************************************
	#*  Checking for tab name to show OPAC look and feel if searching from OPAC
	#****************************************************************************
	if (isset($_REQUEST["tab"])) {
		$tab = $_REQUEST["tab"];
	}
	if ($tab != "opac") {
		$tab = "cataloging";
	}

	$nav = "biblio";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/BiblioImages.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/Stock.php"));
	require_once(REL(__FILE__, "../model/Cart.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../functions/info_boxes.php"));

	#****************************************************************************
	#*  Retrieving get var
	#****************************************************************************
	$bibid = $_REQUEST["bibid"];
	if (isset($_REQUEST["msg"])) {
		$msg = '<p class="error">'.htmlspecialchars($_REQUEST["msg"]).'</p><br /><br />';
	} else {
		$msg = "";
	}

	#****************************************************************************
	#*  Search database
	#****************************************************************************
	$biblios = new Biblios();
	$biblio = $biblios->getOne($bibid);

	#**************************************************************************
	#*  Show bibliography info.
	#**************************************************************************
	if ($tab == "opac") {
		Page::header_opac(array('nav'=>$nav, 'title'=>''));
	} else {
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	}
?>

<script language="JavaScript" >
bvf = {
	init: function () {
	}
}
$(document).ready(bvf.init);
</script>

<?php

	echo $msg;

	currentMbrBox();

	if (isset($_REQUEST['rpt'])) {
		$rpt = Report::load($_REQUEST['rpt']);
	} else {
		$rpt = NULL;
	}
	if ($rpt and isset($_REQUEST['seqno'])) {
		$p = $rpt->row($_REQUEST['seqno']-1);
		$n = $rpt->row($_REQUEST['seqno']+1);
?>
		<fieldset>
		<table style="margin-bottom: 10px" width="60%" align="center">
		<tr>
			<td align="left">
<?php if ($p) { ?>
				<a href="../shared/biblio_view.php?bibid=<?php echo HURL($p['bibid']);?>
																					&amp;tab=<?php echo H($tab);?>
																					&amp;rpt=<?php echo H($rpt->name);?>
																					&amp;seqno=<?php echo H($p['.seqno']);?>"
					 accesskey="p">&laquo;<?php echo T('Prev');?>
				</a>
<?php } ?>
			</td>
		<td align="center">
			<?php echo T("Record %item% of %items%", array('item'=>H($_REQUEST['seqno']+1), 'items'=>H($rpt->count())));?>
		</td>
		<td align="right">
<?php if ($n) { ?>
			<a href="../shared/biblio_view.php?bibid=<?php echo HURL($n['bibid']);?>
																				&amp;tab=<?php echo H($tab);?>
																				&amp;rpt=<?php echo H($rpt->name);?>
																				&amp;seqno=<?php echo H($n['.seqno']);?>
				 accesskey="n"><?php echo T("Next");?>&raquo;
			</a>;
<?php	} ?>
		</td>
		</tr>
		</table>
		</fieldset>
<?php	} ?>

<table class="resultshead">
	<tr>
			<th><?php echo T("Item Information"); ?></th>
		<td class="resultshead">
<table class="buttons">
<tr>
<?php
	$cart = getCart('bibid');
	if ($cart->contains($bibid)) {
?>
		<td><a href="../shared/cart_del.php?name=bibid&amp;id[]=<?php echo H($bibid);?>&amp;tab=<?php echo H($tab);?>"><?php echo T("Remove from Cart"); ?></a></td>
<?php
	} else {
?>
		<td><a href="../shared/cart_add.php?name=bibid&amp;id[]=<?php echo H($bibid);?>&amp;tab=<?php echo H($tab);?>"><?php echo T("Add To Cart"); ?></a></td>
<?php
	}
	if ($tab != 'opac' and isset($_SESSION['currentMbrid'])) {
?>
		<td><a href="../circ/bookdate.php?bibid=<?php echo HURL($bibid)?>"><?php echo T("Book Item"); ?></a></td>
<?php
	}
	if ($tab == 'opac' and isset($_SESSION['authMbrid'])) {
?>
		<td><a href="../opac/book_item.php?bibid=<?php echo HURL($bibid)?>"><?php echo T("Book Item"); ?></a></td>
<?php
	}
?>
</tr>
</table>
</td>
	</tr>
</table>
<?php
		$bibimages = new BiblioImages;
		echo '<div class="biblio_images">';
		$imgs = $bibimages->getByBibid($biblio['bibid']);
		while ($img = $imgs->next()) {
			echo '<div class="biblio_image">';
			if ($img['url']) {
				echo '<a href="'.H($img['url']).'">';
			}
			echo '<img src="'.H($img['imgurl']).'" alt="'.H($img['caption']).'" /><br />';
			echo '<span class="img_caption">'.H($img['caption']).'</span><br />';
			if ($img['url']) {
				echo '</a>';
			}
			echo '</div>';
		}
		echo '</div>';
?>
<fieldset>
<table class="biblio_view">
<thead>
<tr>
	<td colspan=2 align=right>
		<a href="biblio_view.php?bibid=<?php echo $bibid;?>"><?php echo T("Simple View"); ?></a>&nbsp;|&nbsp;
		<a href="biblio_view_marc.php?bibid=<?php echo $bibid;?>"><?php echo T("MARC View"); ?></a>&nbsp;|&nbsp;
		<a href="biblio_cite.php?bibid=<?php echo $bibid;?>" target="_citation"><?php echo T("Citation"); ?></a>
	</td>
</tr>
<thead>
<tbody class="striped">
<?php
foreach ($biblio['marc']->getFields() as $f) {
	$value = "";
	foreach ($f->getSubfields() as $s) {
		$value .= " ".H($s->data);
	}
	$value = trim($value);
	if ($value == "") {
		continue;
	}
	# Honor newlines in MARC fields
	$value = str_replace("\n", "<br />", $value);
?>
	<tr>
		<td class="name"><?php echo T(str_pad($f->tag,3,"0",STR_PAD_LEFT)."$".$s->identifier);; ?>:</td>
		<td class="value"><?php echo $value; ?></td>
	</tr>
<?php
}
if ($tab == "cataloging") {
	echo "<tr>";
	echo '	<td class="name">'.T("Date Added:").'</td>';
	echo '	<td class="value">'.H(date('m/d/Y', strtotime($biblio['create_dt']))).'</td>';
	echo '</tr>';
}
?>
</tbody>
</table>
</fieldset>

<?php
	# Info below shouldn't be shown in the OPAC
	if ($tab != "cataloging") {
		 ;
		exit();
	}
	$collections = new Collections;
	$coll = $collections->getOne($biblio['collection_cd']);

	switch($coll['type']) {
	case 'Circulated':
		include_once(REL(__FILE__, "../catalog/biblio_copy_info.php"));
		showCopyInfo($bibid, $collections->getTypeData($coll));
		break;
	case 'Distributed':
		include_once(REL(__FILE__, "../catalog/biblio_stock_info.php"));
		showStockInfo($bibid, $collections->getTypeData($coll));
		break;
	}

	 ;
