<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));

class Layout_authority_list {
    var $p;
    function paramDefs() {
      return array(
        array('string', 'title', array('title'=>'Title', 'default'=>'')),
      );
    }
    function init($params) {
      $this->p = $params;
    }
   function render($rpt) {
    $lay = new Lay;
      $lay->container('Columns', array(
        'margin-left'=>'0.5in', 'margin-right'=>'0.5in',
        'margin-top'=>'0.5in', 'margin-bottom'=>'0.5in',
      ));
        list( , $title) = $this->p->getFirst('title');
        if ($title) {
          $lay->pushFont('Times-Bold', 16);
            $lay->container('TextLine', array(
              'margin-bottom'=>'0.125in',
              'underline'=>true,
              'x-align'=>'center',
            ));
              $lay->text($title);
            $lay->close();
          $lay->popFont();
        }
        $lay->pushFont('Times-Italic', 12);
          $lay->container('Line');
            $lay->container('TextLine', array('width'=>'1in', 'underline'=>1));
              $lay->text('Items');
            $lay->close();
            $lay->container('TextLine', array('underline'=>1));
              $lay->text('Value');
            $lay->close();
          $lay->close();
        $lay->popFont();
        while ($row = $rpt->each()) {
          $lay->container('Line');
            $lay->container('TextLine', array('width'=>'1in'));
              $lay->text($row['items']);
            $lay->close();
            $lay->container('TextLine');
              $lay->text($row['subfield_data']);
            $lay->close();
          $lay->close();
        }
      $lay->close();
    $lay->close();
  }
}
