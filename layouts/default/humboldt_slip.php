<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_humboldt_slip {
  function render($rpt) {
    $lay = new Lay(array('4in', '1.9375in'));
      while ($row = $rpt->each()) {
        $lay->container('Column', array('margin-left'=>'0.25in',
          'margin-right'=>'0.25in', 'margin-top'=>'0.25in',
          'margin-bottom'=>'0.25in', 'height'=>'1.9375in',
          'width'=>'4in',
        ));
          $lay->pushFont('Times-Bold', 10);
            $lay->container('TextLine', array('x-align'=>'center'));
              $lay->text('Humboldt County Office of Education');
            $lay->close();
            $lay->container('TextLine', array('x-align'=>'center'));
              $lay->text('HERC - Resource Center - 445-7072');
            $lay->close();
          $lay->popFont();
          $lay->element('Spacer', array('height'=>9));
          $lay->pushFont('Times-Roman', 12);
            $lay->container('TextLine');
              $lay->text(strtoupper($row['members'][0]['site_name']));
            $lay->close();
            $lay->container('TextLine');
              $lay->text('To:');
              $lay->text($row['members'][0]['barcode_nmbr']);
              $lay->pushFont('Times-Bold', 12);
                $lay->text($row['members'][0]['first_name'].' '.$row['members'][0]['last_name']);
              $lay->popFont();
            $lay->close();
            $lay->element('Spacer', array('height'=>9));
            $lay->container('TextLine');
              $lay->pushFont('Times-Bold', 12);
                $lay->text($row['item_num']);
              $lay->popFont();
              $lay->text($row['title']);
            $lay->close();
            $lay->container('Column', array('y-align'=>'bottom'));
              $lay->container('Line');
                $lay->container('TextLine');
                  $lay->text('Deliver: '.date('m/d/y', strtotime($row['book_dt'])));
                $lay->close();
                $lay->container('TextLine', array('x-align'=>'right'));
                  $lay->pushFont('Times-Bold', 12);
                    $lay->text('Due: '.date('m/d/y', strtotime($row['due_dt'])));
                  $lay->popFont();
                $lay->close();
              $lay->close();
            $lay->close();
          $lay->popFont();
        $lay->close();
      }
    $lay->close();
  }
}
