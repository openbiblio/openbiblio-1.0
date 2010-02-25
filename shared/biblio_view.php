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
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));
	require_once(REL(__FILE__, "../classes/InfoDisplay.php"));
	require_once(REL(__FILE__, "../classes/MarcDisplay.php"));
	require_once(REL(__FILE__, "../model/Stock.php"));
	require_once(REL(__FILE__, "../model/Cart.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../functions/info_boxes.php"));

	#****************************************************************************
	#*  Retrieving get var
	#****************************************************************************
	$bibid = $_REQUEST["bibid"];
	if (isset($_REQUEST["msg"])) {
//		$msg = '<p class="error">'.htmlspecialchars($_REQUEST["msg"]).'</p><br /><br />';
		$msg = '<p class="error">'.htmlspecialchars($_REQUEST["msg"]).'</p>';
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

<script language="JavaScript1.4" >
bv = {
	init: function () {
	}
}
$(document).ready(bv.init);
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
		echo "<fieldset>\n";
		echo "<table style=\"margin-bottom: 10px\" width=\"60%\" align=\"center\">\n";
		echo "<tr>\n";
		echo "<td align=\"left\">\n";
		if ($p) {
			echo "<a href=\"../shared/biblio_view.php?bibid="
			.HURL($p['bibid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($p['.seqno'])
			."\" accesskey=\"p\">&laquo;".T('Prev')
			."</a>\n";
		}
		echo "</td>\n";
		echo "<td align=\"center\">\n";
		echo T("Record %item% of %items%", array('item'=>H($_REQUEST['seqno']+1), 'items'=>H($rpt->count())));
		echo "</td>\n";
		echo "<td align=\"right\">\n";
		if ($n) {
			echo "<a href=\"../shared/biblio_view.php?bibid=".HURL($n['bibid']).'&amp;tab='.H($tab).'&amp;rpt='.H($rpt->name).'&amp;seqno='.H($n['.seqno'])."\" accesskey=\"n\">".T("Next")."&raquo;</a>\n";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</fieldset>\n";
	}

	$bibimages = new BiblioImages;
	echo "<div class=\"biblio_images\">\n";
	$images = $bibimages->getByBibid($biblio['bibid']);
	while ($img = $images->next()) {
		echo "<div class=\"biblio_image\">\n";
		if ($img['url']) {
			echo "<a href=\"".H($img['url'])."\">\n";
		}
		echo "<img src=\"".H($img['imgurl'])."\" alt=\"".H($img['caption'])."\" />\n";
		echo "<span class=\"img_caption\">".H($img['caption'])."</span>\n";
		if ($img['url']) {
			echo "</a>\n";
		}
		echo "</div>\n";
	}
	echo "</div>\n";

	$d = new InfoDisplay;
	$d->title = T("Item Info");
	$d->buttons = array();
	$cart = getCart('bibid');
	if ($cart->contains($bibid)) {
		$d->buttons[] = array(
			T("Remove from Cart"),
			'../shared/cart_del.php?name=bibid&id[]='.U($bibid).'&tab='.U($tab),
		);
	} else {
		$d->buttons[] = array(
			T("Add To Cart"),
			'../shared/cart_add.php?name=bibid&id[]='.U($bibid).'&tab='.U($tab),
		);
	}
	if ($tab != 'opac' and isset($_SESSION['currentMbrid'])) {
		$d->buttons[] = array(
			T("Book Item"),
			'../circ/bookdate.php?bibid='.U($bibid),
		);
	}
	if ($tab == 'opac' and isset($_SESSION['authMbrid'])) {
		$d->buttons[] = array(
			T("Book Item"),
			'./opac/book_item.php?bibid='.U($bibid),
		);
	}
	echo $d->begin();
//	echo $d->row(T("Title:"), 'Foo'); // ??? - Fred
	echo $d->end();
?>

<fieldset>
<legend></legend>
<table class="biblio_view">
<thead>
<tr>
	<td colspan=2 align=right>
		<a href="biblio_view_full.php?bibid=<?php echo $bibid;?>"><?php echo T("Detailed View"); ?></a>
		&nbsp;|&nbsp;
		<a href="biblio_view_marc.php?bibid=<?php echo $bibid;?>"><?php echo T("MARC View"); ?></a>
		&nbsp;|&nbsp;
		<a href="biblio_cite.php?bibid=<?php echo $bibid;?>" target="_citation"><?php echo T("Citation"); ?></a>
	</td>
</tr>
</thead>
<tbody class="striped">
<?php
function mkfield() {
	$args = func_get_args();
	$name = array_shift($args);
	$field = array_shift($args);
	if (count($args)) {
		$func = array_shift($args);
	} else {
		$func = NULL;
	}
	$a = explode('$', $field);
	$b = array();
	if (isset($a[1])) {
		$b = explode(',', $a[1]);
	}
	if ($args == NULL)
		$args = array();
	return array(
		'name' => $name,
		'tag' => $a[0],
		'subfields' => $b,
		'func' => $func,
		'args' => $args,
	);
}
$fields = array(
	mkfield(T("Title"), '245$a,b'),
	mkfield(T("Author"), '100$a', 'catalog_search', 'author'),
	mkfield(T("Item Number"), '99$a'),
	mkfield(T("Grade Level"), '521$a'),
	mkfield(T("Publication Date"), '260$c'),
	mkfield(T("Publisher"), '260$b', 'catalog_search', 'publisher'),
	mkfield(T("Edition"), '250$a'),
	mkfield(T("Length"), '300$a'),
	mkfield(T("Series"), '440$a', 'catalog_search', 'series'),
	mkfield(T("Summary"), '520$a'),
	mkfield(T("Contents"), '505$a'),
	mkfield(T("Other Physical Details"), '300$b'),
	mkfield(T("Dimensions"), '300$c'),
	mkfield(T("Accompanying Material"), '300$e'),
	mkfield(T("Subjects"), '650$a', 'catalog_search', 'subject'),
	mkfield(T("Links"), '856$u', 'link856'),
);

function catalog_search($type, $value) {
	global $tab;
	return '<a href="../shared/biblio_search.php'
		. '?searchType=' . HURL($type)
		. '&amp;searchText=' . HURL($value)
		. '&amp;tab=' . HURL($tab)
		. '&amp;exact=1">'
		. H($value)
		. "</a>\n";
}
function link856($value) {
	return "<a href=\"".H($value)."\">".H($value)."</a>\n";
}

foreach ($fields as $f) {
	$bibfl = $biblio['marc']->getFields($f['tag']);
	$value = "";
	$prefix = "";
	foreach ($bibfl as $bf) {
		$value .= $prefix;
		foreach ($f['subfields'] as $subf) {
			$subfl = $bf->getSubfields($subf);
			foreach ($subfl as $s) {
				if ($f['func']) {
					$args = $f['args'];
					$args[] = $s->data;
					$value .= call_user_func_array($f['func'], $args);
				} else {
					$value .= H($s->data);
				}
			}
		}
//		$prefix = "<br />";
	}
	$value = trim($value);
	if ($value == "") {
		continue;
	}
	# Honor newlines in MARC fields
	$value = str_replace("\n", "<br />", $value);
?>
	<tr>
		<td class="name"><?php echo H($f['name']); ?>:</td>
		<td class="value"><?php echo $value; ?></td>
	</tr>
<?php
}
if ($tab == "cataloging") {
	echo "<tr><td class=\"name\">".T("Date Added:")."</td>\n";
	echo "<td class=\"value\">".H(date('m/d/Y', strtotime($biblio['create_dt'])))."</td></tr>\n";
}
?>
	</tbody>
</table>
</fieldset>

<?php
	# Info below shouldn't be shown in the OPAC unless show_detail_opac setting is set to Y
	# Have to lookup the value as if not set as normally for OPAC this info is not loaded
	#		(maybe not the most nice solution) - LJ
	if(empty($_SESSION['show_detail_opac']))
		$_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');
	if(empty($_SESSION['multi_site_func']))
		$_SESSION['multi_site_func'] = Settings::get('multi_site_func');
	
	if (($tab != "cataloging") && ($_SESSION['show_detail_opac'] != 'Y')) {
		Page::footer();
		exit();
	}
	$collections = new Collections;
	$coll = $collections->getOne($biblio['collection_cd']);

	switch($coll['type']) {
	case 'Circulated':
		include_once(REL(__FILE__, "../catalog/biblio_copy_info.php"));
		# Added $tab for the opotion to show details in OPAC - LJ
		showCopyInfo($bibid, $collections->getTypeData($coll), $tab);
		break;
	case 'Distributed':
		include_once(REL(__FILE__, "../catalog/biblio_stock_info.php"));
		showStockInfo($bibid, $collections->getTypeData($coll));
		break;
	}

	Page::footer();
