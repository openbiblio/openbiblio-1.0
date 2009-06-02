<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Members.php"));

function currentMbrBox() {
  if (isset($_SESSION['currentMbrid'])) {
    $members = new Members;
    $mbr = $members->maybeGetOne($_SESSION['currentMbrid']);
    if (!$mbr) {
      unset($_SESSION['currentMbrid']);
      return;
    }
    echo '<div class="current_mbr">'.T("Current Patron:");
    echo ' <a href="../circ/mbr_view.php?mbrid='.HURL($mbr['mbrid']).'">';
    echo H($mbr['first_name']).' ';
    echo H($mbr['last_name']).' ';
    echo '('.H($mbr['barcode_nmbr']).')';
    echo '</a></div>';
  }
}
