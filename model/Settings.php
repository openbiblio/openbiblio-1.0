<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, '../classes/Query.php'));

global $_settings_cache, $_settings_validators;
$_settings_cache = array();
$_settings_validators = array();

/* To be used statically. */
class Settings {
	function load() {
		global $_settings_cache, $_settings_validators;
		$db = new Query;
		$r = $db->select('SELECT * FROM settings');
		while ($s = $r->next()) {
			$_settings_cache[$s['name']] = $s['value'];
			$_settings_validators[$s['name']] = explode(',', $s['validators']);
		}
	}
	function get($name) {
		global $_settings_cache;
		return $_settings_cache[$name];
	}
	function getAll() {
		global $_settings_cache;
		return $_settings_cache;
	}
	function getFormFields($menu=NULL) {
		$db = new Query;
		$sql = "SELECT * FROM settings WHERE (title <> '') ";
		if (!empty($menu)) {
			$sql .= " AND (menu = '$menu') ";
		}
		$r = $db->select($sql);
		$fields = array();
		while ($s = $r->next()) {
			$fields[] = Settings::_mkField($s);
		}
		return $fields;
	}
	function setOne_e($name, $value) {
		# FIXME - VALIDATE
		$db = new Query;
		$db->lock();
		$sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $value, $name);
		$db->act($sql);
		$db->unlock();
		return NULL;
	}
	function setOne_el($name, $value) {
		# FIXME - VALIDATE
		$db = new Query;
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
		$db = new Query;
		$db->lock();
		foreach ($settings as $n=>$v) {
			$sql = $db->mkSQL('UPDATE settings SET value=%Q WHERE name=%Q', $v, $n);
			$db->act($sql);
		}
		$db->unlock();
		return $errors;
	}
	function _mkField($s) {
		global $_settings_validators;
		$attrs = array();

		if ($s['width']) {
			$attrs['size'] = $s['width'];
		}
		
		if ($s['type'] == 'int') {
			$s['type'] = 'text';
		}
		
		$options = array();
		if ($s['type'] == 'select') {
			# FIXME - handle other selects
			if ($s['name'] == 'locale') {
				$options = Localize::getLocales();
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

