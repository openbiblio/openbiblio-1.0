<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, '../classes/Queryi.php'));
require_once(REL(__FILE__, '../model/Sites.php'));

global $_settings_cache, $_settings_validators;
$_settings_cache = array();
$_settings_validators = array();

/* To be used statically. */
class Settings extends Queryi
{
	public function __construct() {
		parent::__construct();
	}

	static public function load() {
		global $_settings_cache, $_settings_validators;
		$db = new Queryi;
		$r = $db->select('SELECT * FROM settings');
        //echo "in Settings::load()";print_r($r);echo "<br /> \n";
		while ($s = $r->fetch_assoc()) {
			$_settings_cache[$s['name']] = $s['value'];
			$_settings_validators[$s['name']] = explode(',', $s['validator']);
		}
	}
	static public function get($name) {
		global $_settings_cache;
		return $_settings_cache[$name];
	}
	static public function getThemeDirs () {
		return Settings::_getSubdirs('themes');
	}
	static public function getFormFields($menu=NULL) {
		$r = Settings::_getData($menu);
		$fields = array();
		while ($s = $r->fetch_assoc()) {
				$fields[] = Settings::_mkField($s);
		}
		return $fields;
	}
	static private function _getSubdirs($root) {
		$aray = array();
	  if (is_dir('../'.$root)) {
			//echo $root." Dir found: <br />";
  	  ## find all sub-directories
			if ($dirHndl = opendir('../'.$root)) {
		    # look at all sub-dirs
		    while (false !== ($subdir = readdir($dirHndl))) {
		      if (($subdir == '.') || ($subdir == '..')) continue;
					//echo "subdir => $subdir<br />";
  	      $path = "../".$root."/".$subdir;
  	      if (is_dir($path)) {
  	        if (!in_array($path, $aray)) {
  	        	$aray[$path] = $path;
						}
					}
  		  }
  		  closedir($dirHndl);
			}
		}
		return $aray;
	}

    public function getAll() {
		global $_settings_cache;
		return $_settings_cache;
	}
	private function _getData ($menu=NULL, $cols='*'){
		$db = new Queryi;
		$sql = "SELECT ".$cols." FROM settings WHERE (title <> '') ";
		if (!empty($menu)) {
			$sql .= " AND (menu = '$menu') ";
		}
		$sql .= " ORDER BY position ";
		//echo "sql={$sql}<br />\n";
		return $db->select($sql);
	}
	function getFormData ($menu=NULL, $cols) {
		$r = $this->_getData($menu, $cols);
		$fields = array();
		while ($s = $r->fetch_assoc()) {
				$fields[] = $s;
		}
		return $fields;
	}
	function setOne_e($name, $value) {
		# FIXME - VALIDATE
		$db = new Queryi;
		$db->lock();
		$sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $value, $name);
		$db->act($sql);
		$db->unlock();
		return NULL;
	}
	function setOne_el($name, $value) {
		# FIXME - VALIDATE
		$db = new Queryi;
		$db->lock();
		$sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $value, $name);
		$db->act($sql);
		$db->unlock();
		return $errors;
	}
	function setAll_el($settings) {
		$errors = array();
		# FIXME - VALIDATE
		if (!empty($errors)) {
			return $errors;
		}
		$db = new Queryi;
		$db->lock();
		foreach ($settings as $n=>$v) {
			$sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $v, $n);
			//echo "sql={$sql}<br />\n";
			$db->act($sql);
		}
		$db->unlock();
		return $errors;
	}
	private function _mkField($s) {
		global $_settings_validators;
		$attrs = array();

		if ($s['width']) {
			$attrs['size'] = $s['width'];
		}
		
		if ($s['type'] == 'int') {
			$s['type'] = 'number';
		}
		
		$options = array();
		if ($s['type'] == 'select') {
			switch ($s['type_data']) {
			case 'locales':
				$options = Localize::getLocales();
				break;
			case 'sites':
				$sites = new Sites;
				$options = $sites->getSelect();
				break;
			case 'themes':
				$crntTheme = Settings::get('theme_dir_url');
				//echo "crnt theme= ".$crntTheme;			
				$options = Settings::_getSubdirs('themes');
				$s['value'] = $crntTheme;
				break;
			case 'default':
				Fatal::internalError("Unknown select type in settings");
			}
			//if ($s['name'] == 'library_name') {
			//  $sites = new Sites;
			//	$options = $sites->getSelect();
			//}
			if ($s['name'] == 'checkout_interval') {
				$options = array('Hours','Days');
			}
		}
		
		$label = '';
		if ($s['type'] != 'select' and $s['type_data'] !== NULL) {
			$label = $s['type_data'];
		}
		
		$required=false;
		if (in_array('required', $_settings_validators[$s['name']])) {
			$required=true;
		}
		return array(
			'name'=>$s['name'],
			'title'=>$s['title'],
			'type'=>$s['type'],
			'default'=>$s['value'],
			'attrs'=>$attrs,
			'options'=>$options,
			'required'=>$required,
			'label'=>$label,
		);
	}
}
