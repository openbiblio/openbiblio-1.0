<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once('../shared/common.php');

if (!isset($_GET['bibid'])) {
	header('Location: ../catalog/index.php');
	exit();
}

$tab = 'cataloging';
if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'opac') {
	$tab = 'opac';
}

$nav = 'biblio';
if ($tab != 'opac') {
	require_once(REL(__FILE__, '../shared/logincheck.php'));
}

require_once(REL(__FILE__, '../model/Biblios.php'));
require_once(REL(__FILE__, '../model/BiblioImages.php'));
require_once(REL(__FILE__, '../model/Collections.php'));
require_once(REL(__FILE__, '../model/Cart.php'));
require_once(REL(__FILE__, '../classes/Report.php'));
require_once(REL(__FILE__, '../functions/info_boxes.php'));

$bibid = $_REQUEST['bibid'];
$biblios = new Biblios();
$biblio = $biblios->getOne($bibid);

$page = array('bibid'=>$bibid, 'tab'=>$tab);

if (isset($_REQUEST['rpt'])) {
	$rpt = Report::load($_REQUEST['rpt']);
} else {
	$rpt = NULL;
}

if ($rpt and isset($_REQUEST['seqno'])) {
	$view_url = '../shared/biblio_view.php?bibid={bibid}'
		. '&tab={tab}&rpt={rpt}&seqno={seqno}';
	$p = $rpt->row($_REQUEST['seqno']-1);
	$n = $rpt->row($_REQUEST['seqno']+1);
	$page['sequence'] = array(
		'number'=>$_REQUEST['seqno'],
		'count'=>$rpt->count(),
		'prev_url'=>NULL,
		'next_url'=>NULL,
	);
	if ($p) {
		$page['sequence']['prev_url'] = URL($view_url, array(
			'tab'=>$tab,
			'rpt'=>$rpt->name,
			'bibid'=>$p['bibid'],
			'seqno'=>$p['.seqno'],
		));
	}
	if ($n) {
		$page['sequence']['next_url'] = URL($view_url, array(
			'tab'=>$tab,
			'rpt'=>$rpt->name,
			'bibid'=>$n['bibid'],
			'seqno'=>$n['.seqno'],
		));
	}
} else {
	$page['sequence'] = NULL;
}

$cart = getCart('bibid');
if ($cart->contains($bibid)) {
	$page['in_cart'] = true;
} else {
	$page['in_cart'] = false;
}

$bibimages = new BiblioImages;
$images = $bibimages->getByBibid($biblio['bibid']);
$page['images'] = array();
while ($img = $images->next()) {
	$page['images'][] = $img;
}

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
	mkfield(T("Item Number"), '099$a'),
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
	$url = URL('../shared/biblio_search.php?'
		.'searchType={type}&searchText={value}'
		.'&tab={tab}&exact=1', array(
			'type'=>$type,
			'value'=>$value,
			'tab'=>$tab,
		));
	return HTML('<a href="{url}">{value}</a>',
		array('url'=>$url, 'value'=>$value));
}
function link856($value) {
	return HTML('<a href="{@}">{@}</a>', $value);
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
		$prefix = "<br />";
	}
	$value = trim($value);
	if ($value == "") {
		continue;
	}
	# Honor newlines in MARC fields
	$value = str_replace("\n", "<br />", $value);
	$page['fields'][] = array('name'=>$f['name'], 'value'=>$value);
}
if ($tab == "cataloging") {
	$page['fields'][] = array(
		'name'=>T('Date Added'),
		'value'=>date('m/d/Y', strtotime($biblio['create_dt']))
	);
}

if ($tab == "opac") {
	Page::header_opac(array('nav'=>$nav, 'title'=>''));
} else {
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
}
currentMbrBox();

echo HTML(file_get_contents('biblio_view.jsont'), $page);

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
	# Added $tab for the option to show details in OPAC - LJ
	showCopyInfo($bibid, $collections->getTypeData($coll), $tab);
	break;
case 'Distributed':
	include_once(REL(__FILE__, "../catalog/biblio_stock_info.php"));
	showStockInfo($bibid, $collections->getTypeData($coll));
	break;
}

Page::footer();
