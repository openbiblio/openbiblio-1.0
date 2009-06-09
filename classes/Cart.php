<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Query.php"));

class Cart extends Query {
	var $_name;
	function Cart($name) {
		$this->_name = $name;
		$this->Query();
	}
	function viewURL() {
		$urls = array(
			'bibid' => '../shared/req_cart.php',
			'bookingid' => '../circ/booking_cart.php',
		);
		if (array_key_exists($this->_name, $urls)) {
			return $urls[$this->_name];
		} else {
			return '../circ/index.php';
		}
	}
	function count() {
		$sql = $this->mkSQL("select count(id) as count from cart "
			. "where sess_id=%Q and name=%Q ",
			session_id(), $this->_name);
		$rows = $this->eexec($sql);
		if (count($rows)) {
			return $rows[0]['count'];
		}
		return 0;
	}
	function add($id) {
		$sql = $this->mkSQL("insert into cart values (%Q, %Q, %N) ",
			session_id(), $this->_name, $id);
		return $this->eexec($sql);
	}

	function remove($id) {
		$sql = $this->mkSQL("delete from cart where sess_id=%Q "
			. "and name=%Q and id=%N ",
			session_id(), $this->_name, $id);
		return $this->eexec($sql);
	}

	function contains($id) {
		$sql = $this->mkSQL("select * from cart where sess_id=%Q "
			. "and name=%Q and id=%N ",
			session_id(), $this->_name, $id);
		return count($this->eexec($sql))>0;
	}

	function clear() {
		$sql = $this->mkSQL("delete from cart where sess_id=%Q "
			. "and name=%Q ",
			session_id(), $this->_name);
		return $this->eexec($sql);
	}
}
function &getCart($type) {
	return new Cart($type);
}
