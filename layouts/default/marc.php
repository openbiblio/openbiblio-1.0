<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once(REL(__FILE__, '../../classes/MarcQuery.php'));

  class Layout_marc {
    function render($rpt) {
      header('Content-Type: application/marc');
      header('Content-disposition: inline; filename="export.mrc"');
      $marcQ = new MarcQuery();
      while ($row = $rpt->each()) {
        $marc = $marcQ->get($row['bibid']);
        if (!$marc) {
          continue;
        }
        list($rec, $err) = $marc->get();
        assert('!$err');
        echo $rec;
      }
    }
  }
