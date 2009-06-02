<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Page {
	function header($params=array()) {
		global $_Page_params;
		$params = Page::clean_params($params);
		$_Page_params = $params;
		require_once(REL(__FILE__, '../shared/menu.php'));
		require_once($params['theme_dir']."/header.php");
	}
	function footer() {
		global $_Page_params;
		$params = $_Page_params;
		require_once($params['theme_dir']."/footer.php");
	}
	function header_opac($params=array()) {
		global $_Page_params;
		$params = Page::clean_params($params);
		$_Page_params = $params;
		require_once(REL(__FILE__, '../opac/menu.php'));
		require_once($params['theme_dir']."/header_opac.php");
	}
	function footer_opac() {
		global $_Page_params;
		$params = $_Page_params;
		require_once($params['theme_dir']."/footer_opac.php");
	}
	function header_help($params=array()) {
		global $_Page_params;
		$params = Page::clean_params($params);
		$_Page_params = $params;
		require_once($params['theme_dir']."/header_help.php");
	}
	function footer_help() {
		global $_Page_params;
		$params = $_Page_params;
		require_once($params['theme_dir']."/footer_help.php");
	}
	function clean_params($params) {
		$req = array('nav', 'title');
		foreach ($req as $r) {
			if (!isset($params[$r])) {
				Fatal::internalError(T("Missing required page parameter: %param%", array('param'=>$r)));
			}
		}
		$theme = Settings::get('theme_name');
		$params['theme_dir'] = REL(__FILE__, "../themes/".$theme);
		$params['theme_dir_url'] = "../themes/".$theme;
		if (!isset($params['html_head'])) {
			$params['html_head'] = '';
		}
		return $params;
	}
}
