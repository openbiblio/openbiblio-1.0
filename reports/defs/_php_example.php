<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**************
 * This example is out of date -- FIXME
 **************/
/* Example PHP report.  Takes parameters of all three types.
 * Results are the parameter values.
 */
class _php_example_rpt {
  var $params = NULL;
  function title() {
    return "Example PHP Report";
  }
  function layouts() {
    return array(
      array('name'=>'layout1'),
      array('name'=>'layout2'),
    );
  }
  function paramDefs() {
    return array(
      array('group', 'rows', array('repeatable'=>true), array(
        array('select', 'type', array('title'=>'Type'), array(
          array('foo', array('title'=>'Foo')),
          array('bar', array('title'=>'Bar')),
        )),
        array('string', 'value', array('title'=>'Value')),
      )),
    );
  }
  function init($params) {
    $this->params = $params;
    return true;
  }
  function columns() {
    assert('$this->params !== NULL');
    return array(
      array('name'=>'type', 'title'=>'Type'),
      array('name'=>'value', 'title'=>'Value'),
    );
  }
  function count() {
    assert('$this->params !== NULL');
    return count($this->params->getList('rows'));
  }
  function slice($offset, $length) {
    assert('$this->params !== NULL');
    $l = array();
    foreach ($this->params->getList('rows') as $r) {
      assert('$r[0] == "group"');
      $row = array();
      foreach ($r[1] as $n => $v) {
        assert('$v[0] == "string"');
        $row[$n] = $v[1];
      }
      $l[] = $row;
    }
    return array_slice($l, $offset, $length);
  }
}
