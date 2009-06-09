<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/Form.php"));

session_cache_limiter(null);

$tab = "admin";
$nav = "settings";
$focus_form_name = "editsettingsform";
$focus_form_field = "library_name";

require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

#****************************************************************************
#*  Display update message if coming from settings_edit with a successful update.
#****************************************************************************
if (isset($_REQUEST["updated"])){
	echo '<p class="error">'.T("Data has been updated.").'</p>';
}
Form::display(array(
	'title'=>T("Edit Library Settings"),
	'name'=>'editsettingsform',
	'action'=>'../admin/settings_edit.php',
	'submit'=>T("Update"),
	'fields'=>Settings::getFormFields(),
));

Page::footer();
