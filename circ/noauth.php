<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "";

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo '<p class="error">'.T('circNoauth').'</p>';

Page::footer();
