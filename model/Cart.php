<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Queryi.php"));

class Cart {
	var $name;
	var $db;
	function Cart($name) {
		$this->name = $name;
		$this->db = new Queryi;
	}
	function viewURL() {
		$urls = array(
			'bibid' => '../shared/req_cart.php',
			'bookingid' => '../circ/booking_cart.php',
		);
		if (array_key_exists($this->name, $urls)) {
			return $urls[$this->name];
		} else {
			return '../circ/index.php';
		}
	}
	function count() {
		$sql = $this->db->mkSQL("select count(id) as count from cart "
			. "where sess_id=%Q and name=%Q ",
			session_id(), $this->name);
		$row = $this->db->select1($sql);
		return $row['count'];
	}
	function add($id) {
		$sql = $this->db->mkSQL("insert into cart values (%Q, %Q, %N) ",
			session_id(), $this->name, $id);
		$this->db->act($sql);
	}

	function remove($id) {
		$sql = $this->db->mkSQL("delete from cart where sess_id=%Q "
			. "and name=%Q and id=%N ",
			session_id(), $this->name, $id);
		$this->db->act($sql);
	}

	function contains($id) {
		$sql = $this->db->mkSQL("select * from cart where sess_id=%Q "
			. "and name=%Q and id=%N ",
			session_id(), $this->name, $id);
		return $this->db->select01($sql) !== NULL;
	}

	function clear() {
		$sql = $this->db->mkSQL("delete from cart where sess_id=%Q "
			. "and name=%Q ",
			session_id(), $this->name);
		$this->db->act($sql);
	}
}
function &getCart($type) {
	return new Cart($type);
}
