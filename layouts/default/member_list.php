<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_member_list {
  function render($rpt) {
    $lay = new Lay;
      $lay->container('Columns', array(
        'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
        'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
      ));
        $lay->pushFont('Times-Italic', 12);
          $lay->container('Line');
            $lay->container('TextLine', array('width'=>'1in'));
              $lay->text('Number');
            $lay->close();
            $lay->container('TextLine', array('width'=>'3in'));
              $lay->text('Name');
            $lay->close();
            $lay->container('TextLine', array('width'=>'3in'));
              $lay->text('Site');
            $lay->close();
          $lay->close();
        $lay->popFont();
        while ($row = $rpt->each()) {
          $lay->container('Line');
            $lay->container('TextLine', array('width'=>'1in'));
              $lay->text($row['barcode_nmbr']);
            $lay->close();
            $lay->container('TextLine', array('width'=>'3in'));
              $lay->text($row['last_name'].', '.$row['first_name']);
            $lay->close();
            $lay->container('TextLine', array('width'=>'3in'));
              $lay->text($row['site_name']);
            $lay->close();
          $lay->close();
        }
      $lay->close();
    $lay->close();
  }
}
