<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	#****************************************************************************
	#*  Checking for post vars.  Go back to form if none found.
	#****************************************************************************
	if (count($_POST) == 0 and count($_GET) == 0) {
		header("Location: ../catalog/index.php");
		exit();
	}

	#****************************************************************************
	#*  Checking for tab name to show OPAC look and feel if searching from OPAC
	#****************************************************************************
	$tab = "cataloging";
	if (isset($_REQUEST["tab"])) {
		$tab = $_REQUEST["tab"];
	}
	$_REQUEST['tab'] = $tab;

	$nav = "search";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/BiblioImages.php"));
	require_once(REL(__FILE__, "../model/MaterialTypes.php"));
	require_once(REL(__FILE__, "../model/MaterialFields.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/Links.php"));
	require_once(REL(__FILE__, "../classes/CompactInfoDisplay.php"));
	require_once(REL(__FILE__, "../classes/MarcDisplay.php"));
	require_once(REL(__FILE__, "../functions/info_boxes.php"));


	function mkTerm($type, $text, $exact='0') {
		return array('type'=>$type, 'text'=>$text, 'exact'=>$exact);
	}
	function getRpt() {
		global $tab;

		if ($_REQUEST['searchType'] == 'previous') {
			$rpt = Report::load('BiblioSearch');

			if ($rpt && $_REQUEST['rpt_order_by']) {
				$rpt = $rpt->variant(array('order_by'=>$_REQUEST['rpt_order_by']));
			}
			return $rpt;
		}

		$searches = array(
			"barcodeNmbr" => "barcode",
			 "author" => "author",
			"subject" => "subject",
			"title" => "title",
			"publisher" => "publisher",
			"series" => "series",
			"callno" => "callno",
			"keyword" => "keyword",
		);
		if (in_array($_REQUEST["searchType"], array_keys($searches))) {
			$sType = $searches[$_REQUEST["searchType"]];
		} else {
			$sType = "keyword";
		}
		if (isset($_REQUEST['sortBy'])) {
			$sortBy = $_REQUEST["sortBy"];
		} else {
			if ($sType == "author") {
				$sortBy = $_REQUEST["sortBy"] = "author";
			} else {
				$sortBy = $_REQUEST["sortBy"] = "title";
			}
		}

		$terms = array();
		array_push($terms, mkTerm($sType, $_REQUEST['searchText'], $_REQUEST['exact']));
		if ($_REQUEST['from']) {
			array_push($terms, mkTerm('pub_date_from', trim($_REQUEST['from'])));
		}
		if ($_REQUEST['to']) {
			array_push($terms, mkTerm('pub_date_to', trim($_REQUEST['to'])));
		}
		if ($_REQUEST['audienceLevel'] && $_REQUEST['audienceLevel'] != 'all') {
			array_push($terms, mkTerm('audience_level', $_REQUEST['audienceLevel']));
		}
		if ($_REQUEST['mediaType'] && $_REQUEST['mediaType'] != 'all') {
			array_push($terms, mkTerm('media_type', $_REQUEST['mediaType']));
		}

		$rpt = Report::create('biblio_search', 'BiblioSearch');
		if (!$rpt) {
			return false;
		}
		$rpt->init(array('terms'=>$terms,
										 'order_by'=>$sortBy));
		return $rpt;
	}

	$rpt = getRpt();

	if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
		$currentPageNmbr = $_REQUEST["page"];
	} else {
		$currentPageNmbr = $rpt->curPage();
	}

	if (isset($_REQUEST["msg"])) {
		$msg = $_REQUEST["msg"];
	} else {
		$msg = '';
	}

	#**************************************************************************
	#*  Show biblio view screen if only one result from query
	#**************************************************************************
	if ($rpt->count() == 1) {
		$row = $rpt->row(0);
		$url = '../shared/biblio_view.php?bibid='.U($row['bibid']).'&tab='.U($tab);
		if ($msg) {
			$url .= '&msg='.U($msg);
		}
		header('Location: '.$url);
		exit();
	}

	#**************************************************************************
	#*  Show search results
	#**************************************************************************
	if ($tab == "opac") {
		Nav::node('search/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=opac');
		Page::header_opac(array('nav'=>$nav, 'title'=>''));
	} else {
		Nav::node('cataloging/search/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=cataloging');
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	}

	currentMbrBox();
?>
<div class="search_results">
<div class="title"><?php echo T("Search Results"); ?></div>
<?php
	# Display no results message if no results returned from search.
	if ($rpt->count() == 0) {
		echo T("No results found.");
		Page::footer();
		exit();
	}
?>
<div class="results_found"><?php echo T('biblioSearchMsg', array('nrecs'=>$rpt->count(), 'start'=>1, 'end'=>25)); ?></div>
<?php
	$page_url = new LinkUrl("../shared/biblio_search.php", 'page',
		array('type'=>'previous', 'tab'=>$tab));
	$disp = new ReportDisplay($rpt);
	echo $disp->pages($page_url, $currentPageNmbr);
?>
<div class="search_terms"><?php echo 'search terms: FIXME'; ?></div>
<?php
	$biblios = new Biblios;
	$bibimages = new BiblioImages;
	$mats = new MaterialTypes;
	$mf = new MaterialFields;
	
	echo '<div class="results_list">';
	$page = $rpt->pageIter($currentPageNmbr);
	while($row = $page->next()) {
print_r($row);echo"<br />";
		$bib = $biblios->getOne($row['bibid']);
		$title = Links::mkLink('biblio', $row['bibid'], $row['title_a'].' '.$row['title_b']);
		if (time() - strtotime($bib['create_dt']) < 365*86400) {
			/* Item was added in the last year. */
			# FIXME
		}
		$mat = $mats->getOne($row['material_cd']);
		echo "<table class=\"search_result\"><tr>\n";
		$imgs = $bibimages->getByBibid($row['bibid']);
		echo "<td class=\"cover_image\">\n";
		if ($imgs->count() != 0) {
			$img = $imgs->next();
			$html = '	<img src="'.H($img['imgurl']).'" alt="'.T("Item Image")."\" />\n";
			echo Links::mkLink('biblio', $row['bibid'], $img);
		}
		echo "</td>\n";
		echo "<td class=\"call_media\">\n";
		echo "<div class=\"call_number\">".H($row['callno'])."</div>\n";
		if ($mat['image_file']) {
			echo "<img class=\"material\" src=\"../images/".H($mat['image_file'])."\" />\n";
		}
		echo "</td>\n";
		$fields = $mf->getMatches(array('material_cd'=>$row['material_cd']), 'position');
		echo "<td class=\"material_fields\">\n";
		$d = new CompactInfoDisplay;
		$d->title = $title;
		echo $d->begin();
		while ($f = $fields->next()) {
			if ($f['search_results'] != 'Y') {
				continue;
			}
			$m = new MarcDisplay($f, $bib);
			$v = $m->htmlValues();
			if (strlen($v)) {
				echo $d->row($m->title().':', $v);
			}
		}
		echo $d->end();
		echo "</td>\n";
		echo "<td class=\"right_info\">\n";
		echo "<a class=\"button\" href=\"#\">".T("Add To Cart")."</a>\n";
		echo "<div class=\"available\">1 of 1 Available</div>\n";
		echo "</td>\n";
		echo "</tr>\n</table>\n";
	}
	echo "</div>\n";

	Page::footer();
