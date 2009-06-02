<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Buttons.php"));

class CompactInfoDisplay {
  var $title;
  function CompactInfoDisplay() {
    $this->title = NULL;
    $this->buttons = array();
  }
  function begin() {
    $s = "<div class='compact_info_display'>\n";
    if ($this->title) {
      $s .= '<table class="header"><th class="title">'.$this->title.'</tg>';
      if ($this->buttons) {
        $s .= '<td class="buttons">'.Buttons::display($this->buttons).'</td>';
      }
      $s .= "</table>\n";
    }
    $s .= "<ul>";
    return $s;
  }
  function row($heading, $value) {
    return '<li><span class="heading">'.$heading.'</span> '.$value.'</li>'."\n";
  }
  function end() {
    return "</ul></div>\n";
  }
}

