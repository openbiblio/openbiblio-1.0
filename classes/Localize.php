<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
// Most error messages below cannot be translated,
// as it could result in infinite recursion.

class Localize {
	var $localePath = '';
	var $meta;
	var $trans = array(); // memory image of translation table
	var $marc = array();

	function init($locale) {
		if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_]*$/', $locale)) {
			Fatal::internalError('Invalid Locale: >'.$locale.'<');
		}
		
		$this->localePath = LOCALE_ROOT."/".$locale."/";
		if (!is_readable($this->localePath.'metadata.php')) {
			Fatal::internalError('Locale >'.$locale.'< has no metadata');
		}
		include($this->localePath.'metadata.php');
		
		$classname = $locale.'MetaData';
		if (!class_exists($classname)) {
			Fatal::internalError('Locale >'.$locale.'< has no metadata class');
		}
		$this->meta = new $classname;
		$this->trans = array();
		$this->marc = NULL;
		//$files = array('trans.php', 'custom_trans.php');
		$files = array('trans.php', 'customTrans.php');
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
	function translateOne($str) {
		if (isset($this->trans[$str])) {
			return $this->trans[$str];
		}
		return $str;
	}
	function translateCallback($matches) {
		return $this->translateOne($matches[1]);
	}
	// Find, translate, and replace occurrences of {#trans}...{#end}
	// in $str. The text to be translated is between the delimiters
	// and optionally surrounded by white space.  Alternative
	// metacharacters may be specified if, say, [#trans] is more
	// convenient than {#trans}.
	function translate($str, $metachars='{}') {
		$meta = JsonTemplateModule::SplitMeta($metachars);
		$start = preg_quote($meta[0].'#trans'.$meta[1]);
		$end = preg_quote($meta[0].'#end'.$meta[1]);
		// Non-greedy matching is necessary so that
		// {#trans}one{#end}{#trans}two{#end} is seen
		// as two separate translations.
		$trans_re = '/'.$start.'\s*(.*?)\s*'.$end.'/';
		return preg_replace_callback($trans_re, array($this, 'translateCallback'), $str);
	}
	function moneyFormat($amount) {
		return $this->meta->moneyFormat($amount);
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
			$classname = $file.'MetaData';
			if (!class_exists($classname)) {
				Fatal::internalError(T("Bad locale metadata: %file%: No class", array('file'=>$file)));
			}
			$meta = new $classname;
			$locales[$file] = $meta->locale_description;
		}
		closedir($dir);
		return $locales;
	}
	/* This is a separate function to avoid scope contamination. */
	function _loadFile($file) {
		include($file);
		return $trans;
	}
	
	// Everything below is deprecated
	
	function _substituteVars($text, $vars=NULL) {
		if ($vars == NULL) {
			$vars = array();
		}
		$trans = '';
		while(!empty($text)) {
			$p1 = strpos($text, '%');
			if ($p1 == true) {
//echo "have a variable substitution ==> $text <br />";		
				$trans .= substr($text, 0, $p1);	// save all up to first '%'
//echo "part 1 => $trans<br />";				
				$text = substr($text, $p1+1);			// get all after first %'
//echo "part 2 => $text<br />";
				$p2 = strpos($text, '%');					// find end of substutution 
//echo "ending '%' found at char $p2<br />";				
				if ($p2 == false) {
					Fatal::internalError("Unmatched '%' in translation key.");
					return "";
				}
	
				$varkey = substr($text, 0, $p2);
//echo "varKey => $varkey<br />";				
				if ($varkey == '') {    // %%
					$trans .= '%';
				} else if (isset($vars[$varkey])) {
				//} else if (isset($vars)) {
					$trans .= $vars[$varkey];
					//$trans .= $vars;
//echo "new value => $vars[$varkey]<br />";					
				}
				$text = substr($text, $p2+1);    // collect all after ending '%'
			} else {
				$trans .= $text;	// no substitution needed
				$text = '';
			}
//echo "final trans => $trans<br />";			
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
		else{
			// flag text without an entry in trans.php file
			$text = 'T!'.$text."T!";
		}
		$text = $this->_substituteVars($text, $vars);

//		if (OBIB_HIGHLIGHT_I18N_FLG) {
//			$text = "<span color='#FF8A00'>".$text."</span>";
//		}
		return $text;
	}
	function nGetText($n, $key, $vars=NULL) {
		$suffix = '|'.$this->meta->pluralForm($n);
		return $this->getText($key, $vars, $suffix);
	}

}

