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
//class Settings extends Queryi
class Settings extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('settings');
		$this->setFields(array(
			'name'=>'string',
            'position'=>'number',
            'title'=>'string',
            'type'=>'string',
            'width'=>'number',
            'type_data'=>'string',
            'validator'=>'string',
            'value'=>'string',
            'menu'=>'string',
		));
        $this->setReq(array(
            'name', 'type', 'menu',
        ));
		$this->setKey('name');
        $this->setRows(
            '{"plugList":{"position":"", "title":"", "type":"text", "width":"", "type_data":"", "vallidator":"", "value"","menu":""}},
            '{"OBsize":  {"position":"NULL", "title":"Current Size", "type":"number", "width":"16", "type_data":"", "vallidator":"", "value":"","menu":""}},
            '{"allow_plugins_flg":  {"position":"0", "title":"Allow Plugins", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"library_name":  {"position":"1", "title":"Library Title", "type":"select", "width":"", "type_data":"sites", "vallidator":"", "value":"1","menu":"admin"}},
            '{"item_barcode_flg":  {"position":"1", "title":"Use Item Barcodes", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"library_hours":  {"position":"2", "title":"Library Hours", "type":"textarea", "width":"2", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"item_autoBarcode_flg":  {"position":"2", "title":"Item Auto Barcodes", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","tools":"admin"}},
            '{"library_phone":  {"position":"3", "title":"Library Phone No.", "type":"tel", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"library_home":  {"position":"4", "title":"Library Address", "type":"text", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"library_url":  {"position":"5", "title":"Library URL", "type":"url", "width":"32", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"library_image_url":  {"position":"6", "title":"Library Image", "type":"text", "width":"32", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"block_checkouts_when_fines_due":  {"position":"7", "title":"Block Checkouts When Fines Due", "type":"bool", "width":"1", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"locale":  {"position":"8", "title":"Locale", "type":"select", "width":"", "type_data":"locales", "vallidator":"", "value":"","menu":"admin"}},
            '{"charset":  {"position":"9", "title":"Character Set", "type":"text", "width":"", "type_data":"", "vallidator":"", "value":"UTF-8","menu":"admin"}},
            '{"request_from":  {"position":"10", "title":"Request From", "type":"text", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"mbr_barcode_flg":  {"position":"10", "title":"Use Member Barcodes", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"request_to":  {"position":"11", "title":"Request To", "type":"text", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"mamber_autoBarcode_flg":  {"position":"11", "title":"Member Auto Barcodes", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"request_subject":  {"position":"12", "title":"Request Subject", "type":"text", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"mamber_barcode_width":  {"position":"13", "title":"Member Card No Width", "type":"number", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"opac_url":  {"position":"16", "title":"OPAC URL", "type":"url", "width":"32", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"themeid":  {"position":"18", "title":"Theme", "type":"int", "width":"10", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"theme_dir_url":  {"position":"19", "title":"Theme Dir URL", "type":"select", "width":"", "type_data":"themes", "vallidator":"", "value":"","menu":"admin"}},
            '{"use_image_flg":  {"position":"20", "title":"Use Images", "type":"checkbox", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"show_checkout_mbr":  {"position":"20", "title":"Show member who has an item checked out", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"show_lib_info":  {"position":"21", "title":"Show Lib Info on Staff pages", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"show_item_photos":  {"position":"21", "title":"Show Item Photos", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"show_detail_opac":  {"position":"22", "title":"Show copy details in OPAC", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"multi_site_func":  {"position":"23", "title":"Default site for multiple site functionality (0 = ... 	", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"items_per_page":  {"position":"25", "title":"Photo per page", "type":"number", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"site_login":  {"position":"25", "title":"Select a site at login", "type":"bool", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"item_columns":  {"position":"26", "title":"Select a site at login", "type":"number", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"checkout_interval":  {"position":"26", "title":"Checkout Interval", "type":"select", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"item_barcode_width":  {"position":"27", "title":"Item Barcode Width", "type":"int", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"tools"}},
            '{"thumbnail_width":  {"position":"31", "title":"Photo Max Width", "type":"number", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"thumbnail_height":  {"position":"32", "title":"Photo Max height", "type":"number", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"thumbnail_rotation":  {"position":"33", "title":"Photo Rotation", "type":"number", "width":"", "type_data":"", "vallidator":"", "value":"","menu":"admin"}},
            '{"version":  {"position":"", "title":"OB Version", "type":"number", "width":"", "type_data":"text", "vallidator":"", "value":"1.0b","menu":"admin"}},
        ));
	}

	protected function validate_el($rec, $insert) {
        return array();
    }

	static public function load() {
		global $_settings_cache, $_settings_validators;
        //echo "in Settings::load() <br />\n";
		$db = new Queryi;
        $stmt = $db->act('SELECT * FROM settings');
        foreach ($stmt as $s) {
			$_settings_cache[$s['name']] = $s['value'];
			$_settings_validators[$s['name']] = explode(',', $s['validator']);
		}
        //echo "in Settings::load(), at end <br />\n";
	}
	static public function get($name) {
		global $_settings_cache;
		return $_settings_cache[$name];
	}
	static public function set($name, $value) {
		global $_settings_cache;
        $_settings_cache[$name] = $value;
        self::setOne_e($name, $value);
		return $_settings_cache[$name];
	}
	static public function getThemeDirs () {
		return Settings::_getSubdirs('themes');
	}
	static public function getFormFields($menu=NULL) {
		$r = Settings::_getData($menu);
		$fields = array();
		//while ($s = $r->fetch_assoc()) {
        foreach ($r as $s) {
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

    public static function getSettings() {
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
		//while ($s = $r->fetch_assoc()) {
        foreach ($r as $s) {
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
