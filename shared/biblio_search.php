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
			"author" 			=> "author",
			"subject" 		=> "subject",
			"title" 			=> "title",
			"publisher" 	=> "publisher",
			"series" 			=> "series",
			"callno" 			=> "callno",
			"keyword" 		=> "keyword",
		);
		if (in_array($_REQUEST["searchType"], array_keys($searches))) {
			$sType = $searches[$_REQUEST["searchType"]];
		} else {
			$sType = "keyword";
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

		if (isset($_REQUEST['sortBy'])) {
			$sortBy = $_REQUEST["sortBy"];
		} else {
			if ($sType == "author") {
				$sortBy = $_REQUEST["sortBy"] = "author";
			} else {
				$sortBy = $_REQUEST["sortBy"] = "title";
			}
		}
		$rpt->init(array('terms'=>$terms, 'order_by'=>$sortBy));
		return $rpt;
	}
	function pageLink($page) {
		global $tab;
		return URL('../shared/biblio_search.php?type=previous'
			. '&tab={tab}&page={page}',
			array('tab'=>$tab, 'page'=>$page));
	}
	function getPagination($count, $page) {
		$perpage = Settings::get('items_per_page');
		$r = array(
			'num_results'=>$count,
			'total_pages'=>ceil($count/$perpage),
			'multiple_pages'=>ceil($count/$perpage)>1,
			'starting_item'=>($page-1)*$perpage + 1,
			'ending_item'=>min($count, $page*$perpage),
			'start_at_one'=>false,
			'near_last'=>false,
			'pages'=>array(),
		);
		if ($page <= OBIB_SEARCH_MAXPAGES/2) {
			$i = 1;
			$r['stat_at_one'] = true;
		} else {
			$i = $page - OBIB_SEARCH_MAXPAGES/2;
		}
		$endpg = $i + OBIB_SEARCH_MAXPAGES-1;
		if ($endpg > $r['total_pages']) {
			$endpg = $r['total_pages'];
			$r['near_last'] = true;
		}
		for(;$i<= $endpg; $i++) {
			$r['pages'][] = array(
				'number'=>$i,
				'url'=>pageLink($i),
				'current'=>($i==$page),
			);
		}
		return $r;
	}
	function processResults($rpt) {
		$biblios = new Biblios;
		$bibimages = new BiblioImages;
		$mats = new MaterialTypes;
		$mf = new MaterialFields;
		$results = array();
		while($row = $rpt->next()) {
			$bib = $biblios->getOne($row['bibid']);
			$row['title'] = $row['title_a'].' '.$row['title_b'];
			$row['biblio_url'] = URL('{bibid|biblio-link-url}', $row);
			if (time() - strtotime($bib['create_dt']) < 365*86400) {
				/* Item was added in the last year. */
				$row['new'] = true;
			} else {
				$row['new'] = false;
			}
			$mat = $mats->getOne($row['material_cd']);
			$imgs = $bibimages->getByBibid($row['bibid']);
			if ($imgs->count() != 0) {
				$img = $imgs->next();
				$row['img_url'] = $img['imgurl'];
			} else {
				$row['img_url'] = false;
			}
			if ($mat['image_file']) {
				$row['material_img'] = '../images/'.$mat['image_file'];
			} else {
				$row['material_img'] = false;
			}
			$row['fields'] = array();
			$fields = $mf->getMatches(array('material_cd'=>$row['material_cd']), 'position');
			while ($f = $fields->next()) {
				if ($f['search_results'] != 'Y') {
					continue;
				}
				$m = new MarcDisplay($f, $bib);
				$v = $m->htmlValues();
				if (strlen($v)) {
					$row['fields'][] = array('heading'=>$m->title(),'value'=>$v);
				}
			}
			$results[] = $row;
		}
		return $results;
	}

	$rpt = getRpt();

	if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
		$currentPageNmbr = $_REQUEST["page"];
	} else {
		$currentPageNmbr = $rpt->curPage();
	}

	$page_data = array(
		'pagination'=>getPagination($rpt->count(), $currentPageNmbr),
		'results'=>processResults($rpt->pageIter($currentPageNmbr)),
	);

	// Show biblio view screen if only one result from query
	if (count($page_date['results']) == 1) {
		header('Location: '.$page_data['results'][0]['biblio_url']);
		exit();
	}

	if ($tab == "opac") {
		Nav::node('search/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=opac');
		Page::header_opac(array('nav'=>$nav, 'title'=>''));
	} else {
		Nav::node('cataloging/search/catalog', T("Print Catalog"), '../shared/layout.php?name=catalog&rpt=BiblioSearch&tab=cataloging');
		Nav::node('cataloging/search/catalog', T("MARC Output"), '../shared/layout.php?name=marc&rpt=Report&tab=cataloging');
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	}

	currentMbrBox();

	echo HTML(file_get_contents('biblio_search.jsont'), $page_data);

	Page::footer();
