<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/*
 * @author Micah Stetson
 */

$cache = NULL;
require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/Form.php"));

$tab = "tools";
$nav = "settings";
$focus_form_name = "editsettingsform";
$focus_form_field = "";

require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

#****************************************************************************
#*  Display update message if coming from settings_edit with a successful update.
#****************************************************************************
if (isset($_REQUEST["updated"])){
	echo '<p class="error">'.T("Update successful").'</p>';
}
Form::display(array(
	'title'=>T("Edit System Settings"),
	'name'=>'editsettingsform',
	'action'=>'../tools/settings_edit.php',
	'submit'=>T("Update"),
	'fields'=>Settings::getFormFields('tools'),
));

/*
INSERT INTO `settings` (`name`, `position`, `title`, `type`, `width`, `type_data`, `validator`, `value`, `menu`)
VALUES ('allow_auto_db_check', '28', 'Allow Database Auto-Integrity Check', 'bool', NULL, NULL, NULL, 'Y', 'tools');
*/
