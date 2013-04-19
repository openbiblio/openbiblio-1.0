<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Params.php"));

/* A report should always be created with Report::load(), Report::create(),
 * or Report::create_e(), never with new Report().  
 * Create_e() makes a new report of the given type.  If the report is given a 
 *   name, it will be saved in the session in a way that does not depend on 
 *   storing objects in the session.  
 * Load() loads a named report from data stored in the session.  
 * Create() calls create_e(), but treats any error as fatal.
 * Link() returns an URL for linking to the results of a named report.  An
 *   optional message may be supplied for display on the results page.
 *
 * Public instance methods:
 *   title(), layouts(), paramDefs(), init(), init_el(), initCgi(),
 *   initCgi_el(), variant() ,variant_el(), columns(), columnNames(),
 *   count(), curPage(), row(), each(), table(), and pageTable()
 */
class Report {
	var $name;
	var $rpt;
	var $params;
	var $iter;
	var $cache;
	var $pointer = 0;
	
	function link($name, $msg='', $tab='') {
		$urls = array(
			'Report'=>'../reports/run_report.php?type=previous&msg=',
			'BiblioSearch'=>'../shared/biblio_search.php?searchType=previous&msg=',
			'BiblioCart'=>'../shared/req_cart.php?msg=',
		);
		if (isset($urls[$name])) {
			$url = $urls[$name];
		} else {
			$url = '../reports/index.html?msg=';
		}
		$url .= U($msg);
		if ($tab) {
			$url .= '&tab='.U($tab);
		}
		return $url;
	}
	function create($type, $name=NULL) {
		list($rpt, $err) = Report::create_e($type, $name);
		if($err) {
			Fatal::internalError(T('ReportCreatingReport', array('error'=>$err->toStr())));
		}
		return $rpt;
	}
	function create_e($type, $name=NULL) {
		$cache = array('type'=>$type);
		$rpt = new Report;
		$err = $rpt->_load_e($name, $cache);
		return array($rpt, $err);
	}
	function load($name) {
		if (!isset($_SESSION['rpt_'.$name])) {
			return NULL;
		}
		$rpt = new Report;
		$err = $rpt->_load_e($name, $_SESSION['rpt_'.$name]);
		if ($err) {
			unset($_SESSION['rpt_'.$name]);
			Fatal::internalError(T('ReportNoLoadReport', array('name'=>$name)));
		}
		return $rpt;
	}
	function _load_e($name, $cache) {
		$this->name = $name;
		assert('preg_match("{^[-_/A-Za-z0-9]+\$}", $cache["type"])');
		$fname = '../reports/defs/'.$cache['type'];
		if (is_readable($fname.'.php')) {
			## for hard-coded reports
			$err = $this->_load_php_e($cache['type'], $fname.'.php');
			
		} elseif (is_readable($fname.'.rpt')) {
		  ## for scripted reports
			$err = $this->_load_rpt_e($cache['type'], $fname.'.rpt');
		}
		if ($err) {
			return $err;
		}
		$this->cache = $cache;
		if (array_key_exists('params', $cache) and is_array($cache['params'])) {
			$this->params = new Params;
			$this->params->loadDict($cache['params']);
		}
		return NULL;
	}
	function _load_php_e($type, $fname) {
		$classname = $type.'_rpt';
		include_once($fname);
		$this->rpt = new $classname;
		return NULL;		# Can't error non-fatally
	}
	function _load_rpt_e($type, $fname) {
		require_once(REL(__FILE__, '../classes/Rpt.php'));
		$rpt = new Rpt;
		$err = $rpt->load_e($fname);
		if ($err) {
			return $err;
		} else {
			$this->rpt = $rpt;
		}
	}
	function type() {
		return $this->cache['type'];
	}
	function title() {
		return $this->rpt->title();
	}
	function category() {
		return $this->rpt->category();
	}
	function layouts() {
		return $this->rpt->layouts();
	}
	function paramDefs() {
		return $this->rpt->paramDefs();
	}
	function columns() {
		return $this->rpt->columns();
	}
	function columnNames() {
		return array_map(create_function('$x', 'return $x["name"];'), $this->columns());
	}
	function init($params) {
		$errs = $this->init_el($params);
		if(!empty($errs)) {
			Fatal::internalError(T('ReportInitReport', array('error'=>Error::listToStr($errs))));
		}
	}
	function init_el($params) {
		assert('is_array($params)');
		$p = new Params;
		$errs = $p->load_el($this->rpt->paramDefs(), $params);
		if (!empty($errs)) {
			return $errs;
		}
		return $this->_init_el($p);
	}
	function initCgi($prefix='rpt_') {
		$errs = $this->initCgi_el($prefix);
		if(!empty($errs)) {
			Fatal::internalError(T('ReportInitReport', array('error'=>Error::listToStr($errs))));
		}
	}
	function initCgi_el($prefix='rpt_') {
		$p = new Params;
		$errs = $p->loadCgi_el($this->rpt->paramDefs(), $prefix);
		if (!empty($errs)) {
			return $errs;
		}
		return $this->_init_el($p);
	}
	function _init_el($params) {
		unset($this->cache['params']);
		$this->params = $params;
		$this->cache['params'] = $params->dict;
		$this->_save();
		return array();
	}
	function variant($newParams, $newName=NULL) {
		list($rpt, $errs) = $this->variant_el($newParams, $newName);
		if(!empty($errs)) {
			Fatal::internalError(T('ReportMakingVariant', array('error'=>Error::listToStr($errs))));
		}
		return $rpt;
	}
	function variant_el($newParams, $newName=NULL) {
		if(!is_array($this->cache["params"])) {
			Fatal::internalError(T('ReportNoParams'));
		}
		if ($newName === NULL) {
			$newName = $this->name;
		}
		$rpt = Report::create($this->cache['type'], $newName);
		if(!$rpt) {
			Fatal::internalError(T('ReportCreationFailed'));
		}
		$params = new Params;
		$params->loadDict($this->cache['params']);
		$errs = $params->load_el($rpt->rpt->paramDefs(), $newParams);
		if (!empty($errs)) {
			return array(NULL, $errs);
		}
		$errs = $rpt->_init_el($params);
		if (!empty($errs)) {
			return array(NULL, $errs);
		}
		return array($rpt, array());
	}
	function curPage() {
		if ($this->cache['page']) {
			return $this->cache['page'];
		} else {
			return 1;
		}
	}
	function _getIter() {
		if (isset($this->iter) && $this->iter) {
			return;
		} else {
			$this->iter = new NumberedIter($this->rpt->select($this->params));
		}
	}
	function count() {
		if ($this->cache['count'] === NULL) {
			$this->_getIter();
			$this->cache['count'] = $this->iter->count();
			$this->_save();
		}
		return $this->cache['count'];
	}
	function next() {
		$this->_getIter();
		return $this->iter->next();
	}
	function each() { # FIXME - get rid of this
		return $this->next();
	}
	function row($num) {
		if (isset($this->cache['rows'][$num])) {
			return $this->cache['rows'][$num];
		}
		$first = max(0, $num - floor(Settings::get('items_per_page')/2));
		$this->_cacheSlice($first);
		if (isset($this->cache['rows'][$num])) {
			return $this->cache['rows'][$num];
		} else {
			return NULL;
		}
	}
	function _cacheSlice($skip, $len=NULL) {
		if ($len === NULL) {
			$len = Settings::get('items_per_page');
		}
		$first = min($skip, $this->count()-1);
		$last = min($skip+$len-1, $this->count()-1);
		if (isset($this->cache['rows'])
				and isset($this->cache['rows'][$first])
				and isset($this->cache['rows'][$last])) {
			return;
		}
		$this->iter = NULL;
		$this->_getIter();
		$this->iter = new SliceIter($skip, $len, $this->iter);
		$this->cache['rows'] = array();
		while (($row = $this->iter->next()) !== NULL) {
			$this->cache['rows'][$row['.seqno']] = $row;
		}
		$this->_save();
	}
	function _cachePage($page) {
		$this->_cacheSlice(($page-1)*Settings::get('items_per_page'));
	}
	function pageIter($page) {
		$this->_cachePage($page);
		return new ArrayIter(array_values($this->cache['rows']));
	}
	function _save() {
		if ($this->name) {
			$_SESSION['rpt_'.$this->name] = $this->cache;
		}
	}
}

