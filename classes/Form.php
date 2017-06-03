<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class Form {
    ## -------------------------------------------------
	public function getCgi_el($fields) {
		$fields = Form::_cleanFields($fields);
		$errors = array();
		$values = array();
		if (isset($_REQUEST['_posted']))
			$values['_posted'] = $_REQUEST['_posted'];
		else
			$values['_posted'] = false;
		foreach ($fields as $f) {
			if ($f['type'] == 'fixed')
				continue;
			if ($f['type'] == 'bool' and (!isset($_REQUEST[$f['name']])
					or $_REQUEST[$f['name']] != 'Y'))
				$_REQUEST[$f['name']] = 'N';
			if (isset($_REQUEST[$f['name']]))
				$values[$f['name']] = $_REQUEST[$f['name']];
			else
				$values[$f['name']] = $f['default'];
			if ($f['required'] and $values[$f['name']] == '') {
				$errors[] = new FieldError($f['name'], T("This field must be filled in."));
				continue;
			}
			if ($f['type'] == 'select') {
				if (!isset($f['options'][$values[$f['name']]])) {
					$errors[] = new FieldError($f['name'], T("Choose a valid value from the list."));
				}
			}
		}
		return array($values, $errors);
	}
    ## -------------------------------------------------

	static function display($params) {
		$defaults = array(
			'title'=>'',
			'name'=>NULL,
			'method'=>'post',
			'action'=>NULL,
			'submit'=>T("Submit"),
			'cancel'=>NULL,
			'fields'=>array(),
			'values'=>array(),
			'errors'=>array(),
		);
		$params = array_merge($defaults, $params);
		if (!$params['action']) {
			Fatal::internalError(T("No form action"));
		}
		$fields = Form::_cleanFields($params['fields']);
		echo '<h3>'.H($params['title']).'</h3>';
		echo "<form role="form" method='".H($params['method'])."' action='".H($params['action'])."'";
		if ($params['name']) {
			echo ' name="'.H($params['name']).'" id="'.H($params['name']).'"';
		}
		echo ">\n";
		echo '<input type="hidden" name="_posted" value="1" />'."\n";
		list($msg, $errors) = FieldError::listExtract($params['errors']);
		$rows = array();
		foreach ($fields as $f) {
			## make up each user input line
			if (!isset($params['values'][$f['name']])) {
				$f['value'] = $f['default'];
			} else {
				$f['value'] = $params['values'][$f['name']];
			}
			$html = Form::_inputField($f);
			## cache input rows for displayinginto $rows[]
			if (isset($errors[$f['name']]))
				$error = $errors[$f['name']];
			else
				$error = NULL;
			if ($f['type'] == 'hidden') {
				if ($error) {
					Fatal::internalError(T("Unexpected hidden field error: %error%", array('error'=>$error)));
				}
				echo $html;
			} else {
				$rows[] = array('title'=>$f['title'], 'id'=>$f['name'], 'html'=>$html, 'error'=>$error);
			}
		}
		## construct the display exterior
		echo "<fieldset>\n";
		if ($msg) {
			echo '<p class="error">'.H($msg).'</p>';
		}
        ## fill using $rows[] cache
		foreach ($rows as $r) {
			if ($r['error']) {
				$err = '<span class="error">'.H($r['error']).'</span>';
			} else {
				$err = '';
			}
			//echo $r['html'].$err;
			//echo '<label for="'.$r["id"].'">'.H($r["title"]).':</label>';
			echo '<label for="'.$r["id"].'">'.$r["title"].':</label>';
			echo $r['html'].$err;
			echo '<br />';
		}
		echo '<input type="submit" value="'.H($params['submit']).'" />';
		if ($params['cancel']) {
			echo '<a href="'.H($params['cancel']).'">'.T("Cancel").'</a> ';
		}
		echo "</fieldset>\n";
		echo "</form>\n";
	}

    ## -------------------------------------------------
	static function _inputField($field) {
		$s = "";
		$attrs = "";
		foreach ($field['attrs'] as $k => $v) {
			$attrs .= H($k).'="'.H($v).'" ';
		}
		switch ($field['type']) {
		// FIXME radio
		case 'select':
			$s .= '<select id="'.H($field['name'])
				. '" name="'.H($field['name']).'" '.$attrs.">\n";
			foreach ($field['options'] as $val => $desc) {
				$s .= '<option value="'.H($val).'" ';
				if ($field['value'] == $val) {
					$s .= ' selected ';
				}
				$s .= ">".H($desc)."</option>\n";
			}
			$s .= "</select>\n";
			break;
		case 'textarea':
			$s .= '<textarea id="'.H($field['name']).'" '
				 . 'name="'.H($field['name']).'" '
				 . $attrs.">".H($field['value'])."</textarea>";
			break;
		case 'bool':
			$s .= '<input type="checkbox" ';
			$s .= 'id="'.H($field['name']).'" ';
			$s .= 'name="'.H($field['name']).'" ';
			$s .= 'value="Y" ';
			if ($field['value'] == 'Y') {
				$s .= ' checked ';
			}
			$s .= $attrs."/>";
			break;
		case 'fixed':
			$s .= H($field['value']);
			break;
		default:
			$s .= '<input type="'.H($field['type']).'" ';
			$s .= 'id="'.H($field['name']).'" ';
			$s .= 'name="'.H($field['name']).'" ';
			$s .= 'value="'.H($field['value']).'" ';
			$s .= 'size="'.(strlen($field['value'])).'" ';
			$s .= 'maxlength="'.(strlen($field['value'])+16).'" ';
			if ($field['required'] == true) {
				$s .= ' required aria-required="true" ';
			}
			$s .= $attrs." />";
			break;
		}
		if ($field['label']) {
			$s .= ' '.H($field['label']);
		}
		return $s;
	}

    ## -------------------------------------------------
	static function _cleanFields($fields) {
		$defaults = array(
			'name'=>NULL,
			'title'=>NULL,
			'type'=>'text',
			'default'=>'',
			'attrs'=>array(),
			'options'=>array(),
			'label'=>'',
			'required'=>false,
		);
		for ($i=0; $i<count($fields); $i++) {
			$fields[$i] = array_merge($defaults, $fields[$i]);
			if (!isset($fields[$i]['name'])) {
				Fatal::internalError(T("No name set for form field."));
			}
			if (!$fields[$i]['title']) {
				$fields[$i]['title'] = $fields[$i]['name'];
			}
		}
		return $fields;
	}
}
