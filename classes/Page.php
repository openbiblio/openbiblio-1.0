<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Page {
	static function header($params=array()) {
		global $_Page_params;
		global $nav, $tab, $focus_form_name, $focus_form_field;
		$params = Page::clean_params($params);
		$_Page_params = $params;
		if ($tab == 'opac') {
			require_once(REL(__FILE__, '../opac/menu.php'));
			opac_menu();
		} else {
			require_once(REL(__FILE__, '../shared/menu.php'));
			staff_menu();
		}
		require_once($params['theme_dir']."/header.php");
	}
	function header_help($params=array()) {
		global $_Page_params;
		$params = Page::clean_params($params);
		$_Page_params = $params;
		require_once($params['theme_dir']."/header_help.php");
	}
	function header_install($params=array()) {
		global $_Page_params;
		$params = Page::clean_params($params);
		$_Page_params = $params;
		require_once($params['theme_dir']."/header.php");
	}
	static function clean_params($params) {
		global $ThemeDirUrl, $ThemeId;
		$req = array('nav', 'title');
		foreach ($req as $r) {
			if (!isset($params[$r])) {
				Fatal::internalError(T("Missing required page parameter: %param%", array('param'=>$r)));
			}
		}
		$params['themeid'] = $ThemeId;
		$params['theme_dir'] = $ThemeDirUrl;
		$params['theme_dir_url'] = $ThemeDirUrl;
		if (!isset($params['html_head'])) {
			$params['html_head'] = '';
		}
		return $params;
	}
	function getPagination($count, $page, $pageLink) {
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
		$i = $page - floor(OBIB_SEARCH_MAXPAGES/2);
		if ($i <= 1) {
			$i = 1;
			$r['start_at_one'] = true;
		}
		$endpg = $i + OBIB_SEARCH_MAXPAGES-1;
		if ($endpg >= $r['total_pages']) {
			$endpg = $r['total_pages'];
			$r['near_last'] = true;
		}
		for(;$i<= $endpg; $i++) {
			$r['pages'][] = array(
				'number'=>$i,
				'url'=>$pageLink($i),
				'current'=>($i==$page),
			);
		}
		return $r;
	}
}
