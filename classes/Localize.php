<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
// Most error messages below cannot be translated,
// as it could result in infinite recursion.

class Localize {
	var $localePath = '';
	var $meta;
	var $trans = array();
	var $marc = array();

	function init($locale) {
		if (!ereg("^[A-Za-z0-9][A-Za-z0-9_]*$", $locale)) {
			Fatal::internalError('Invalid Locale');
		}
		
		$this->localePath = LOCALE_ROOT."/".$locale."/";
		if (!is_readable($this->localePath.'metadata.php')) {
			Fatal::internalError('Locale has no metadata');
		}
		include($this->localePath.'metadata.php');
		
		$classname = ucfirst($locale).'MetaData';
		if (!class_exists($classname)) {
			Fatal::internalError('Locale has no metadata class');
		}
		$this->meta = new $classname;
		$this->trans = array();
		$this->marc = NULL;
		$files = array('trans.php', 'custom_trans.php');
		foreach ($files as $f) {
			if (is_readable($this->localePath.$f)) {
				$trans = $this->_loadFile($this->localePath.$f);
				$this->trans = array_merge($this->trans, $trans);
			}
		}
	}
	function getMarc($key) {
		if ($this->marc == NULL) {
			$this->loadMarc();
		}
		if (isset($this->marc[$key])) {
			return $this->marc[$key];
		} else {
			return $this->getText('Undefined');
		}
	}
	function loadMarc() {
		$this->marc = array();
		$files = array('marc.php', 'custom_marc.php');
		foreach ($files as $f) {
			if (is_readable($this->localePath.$f)) {
				$trans = $this->_loadFile($this->localePath.$f);
				$this->marc = array_merge($this->marc, $trans);
			}
		}
	}
	/* This is a separate function to avoid scope contamination. */
	function _loadFile($file) {
		include($file);
		return $trans;
	}
	function _substituteVars($text, $vars=NULL) {
		if ($vars == NULL) {
			$vars = array();
		}
		$trans = "";
		while(!empty($text)) {
			$p = strpos($text, "%");
			if ($p === false) {
				$trans .= $text;
				break;
			}
			$trans .= substr($text, 0, $p);
			$text = substr($text, $p+1);    // Skip '%'
			$p = strpos($text, "%");
			if ($p === false) {
				Fatal::internalError("Unmatched % in translation key.");
			}
			$varkey = substr($text, 0, $p);
			$text = substr($text, $p+1);    // Skip '%'
			if ($varkey == '') {    // %%
				$trans .= '%';
			} else if (isset($vars[$varkey])) {
				$trans .= $vars[$varkey];
			}
		}
		return $trans;
	}
	function getText($key, $vars=NULL, $suffix='') {
		$k = explode('|', $key);
		$key = $key.$suffix;
		$text = $k[count($k)-1];
		if (isset($this->trans[$key])) {
			$text = $this->trans[$key];
		}
		$text = $this->_substituteVars($text, $vars);

		if (OBIB_HIGHLIGHT_I18N_FLG) {
			$text = "<span color='#FF8A00'>".$text."</span>";
		}
		return $text;
	}
	function nGetText($n, $key, $vars=NULL) {
		$suffix = '|'.$this->meta->pluralForm($n);
		return $this->getText($key, $vars, $suffix);
	}
	function getLocales () {
		$dir = opendir(LOCALE_ROOT);
		$locales = array();
		while (($file=readdir($dir)) !== false) {
			if ($file == '.' or $file == '..') {
				continue;
			}
			if (!is_dir(LOCALE_ROOT."/".$file)) {
				continue;
			}
			if (!file_exists(LOCALE_ROOT.'/'.$file.'/metadata.php')) {
				continue;
			}
			include_once(LOCALE_ROOT.'/'.$file.'/metadata.php');
			$classname = ucfirst($file).'MetaData';
			if (!class_exists($classname)) {
				Fatal::internalError(T("Bad locale metadata: %file%: No class", array('file'=>$file)));
			}
			$meta = new $classname;
			$locales[$file] = $meta->locale_description;
		}
		closedir($dir);
		return $locales;
	}
}

