<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once('../classes/Lay.php');

class Layout_labels {
  var $p;
  function paramDefs() {
    return array(
      array('string', 'skip', array('title'=>'Skip Labels', 'default'=>'0')),
    );
  }
  function init($params) {
    $this->p = $params;
  }
  function render($rpt) {
    $lay = new Lay('A4');
      $lay->container('Columns', array(
        'margin-top'=>'12mm', 'margin-bottom'=>'12mm',
        'margin-left'=>'32.5mm', 'margin-right'=>'32.5mm',
      ));
        list(, $skip) = $this->p->getFirst('skip');
        for ($i = 0; $i < $skip; $i++) {
          $lay->container('Line', array(
            'height'=>'16.9mm', 'width'=>'145mm',
          ));
          $lay->close();
        }
        while ($row = $rpt->each()) {
          $lay->container('Line', array(
            'height'=>'16.9mm', 'width'=>'145mm',
          ));
            $lay->container('Column', array('width'=>'29mm', 'y-align'=>'center'));
              $lay->pushFont('Helvetica-Bold', 11);
                $lay->text($row['callno']);
              $lay->popFont();
            $lay->close();
            $lay->container('Column', array('width'=>'58mm', 'y-spacing'=>'1mm', 'y-align'=>'center'));
              $lay->container('TextLine', array('x-align'=>'center'));
                $lay->pushFont('Code39JK', 20);
                  $lay->text('*'.strtoupper($row['barcode_nmbr']).'*');
                $lay->popFont();
              $lay->close();
              $lay->container('TextLine', array('x-align'=>'center'));
                $lay->pushFont('Courier', 10);
                  $lay->text(strtoupper($row['barcode_nmbr']));
                $lay->popFont();
              $lay->close();
            $lay->close();
            $lay->container('Column', array('width'=>'58mm', 'y-align'=>'center'));
              $lay->pushFont('Helvetica', 9);
              $lay->container('TextLine');
                $lay->text($row['author']);
              $lay->close();
              $lay->container('TextLine');
                $lay->text($row['title']);
              $lay->close();
              $lay->container('TextLine');
                $lay->text($row['collection']);
              $lay->close();
              $lay->popFont();
            $lay->close();
          $lay->close();
        }
      $lay->close();
    $lay->close();
  }
}

?>
