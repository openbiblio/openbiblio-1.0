<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../../classes/Queryi.php"));
require_once(REL(__FILE__, "../../classes/BiblioRows.php"));

class biblio_cart_rpt extends BiblioRows {
	var $params = NULL;
	var $q;

	function title() { return "Item Cart"; }
	function category() { return "Cataloging"; }
	function layouts() { return array(array('title'=>'MARC Export', 'name'=>'marc')); }
	function paramDefs() {
		return array(
			array('order_by', 'order_by', array('default'=>'title'), array(
				array('callno', array('title'=>'Call No.')),
				array('title', array('title'=>'Title')),
				array('date', array('title'=>'Date')),
				array('length', array('title'=>'Length')),
			)),
		);
	}
	function select($params) {
		$this->params = $params;
		$this->q = new Queryi;
		list( , , $raw) = $this->params->getFirst('order_by');
		$sortq = $this->getOrderSql('cart.id', $raw);
		$sql = "select cart.id as bibid "
			. "from cart ".$sortq['from']
			. $this->q->mkSQL("where sess_id=%Q and name='bibid' ",
				session_id())
			. $sortq['order by'];

		return new BiblioRowsIter($this->q->select($sql));
	}
}
