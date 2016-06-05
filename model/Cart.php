<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Queryi.php"));

class Cart extends Queryi {
	private $name;
	private $db;
	public function __construct($name) {
		parent::__construct();
		$this->name = $name;
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
		$sql = $this->mkSQL("select count(id) as count from cart "
			. "where sess_id=%Q and name=%Q ",
			session_id(), $this->name);
		$row = $this->select1($sql);
		return $row['count'];
	}
	function add($id) {
		$sql = $this->mkSQL("insert into cart values (%Q, %Q, %N) ",
			session_id(), $this->name, $id);
		$this->act($sql);
	}

	function remove($id) {
		$sql = $this->mkSQL("delete from cart where sess_id=%Q "
			. "and name=%Q and id=%N ",
			session_id(), $this->name, $id);
		$this->act($sql);
	}

	function contains($id) {
		$sql = $this->mkSQL("select * from cart where sess_id=%Q "
			. "and name=%Q and id=%N ",
			session_id(), $this->name, $id);
		return $this->select01($sql) !== NULL;
	}

	function clear() {
		$sql = $this->mkSQL("delete from cart where sess_id=%Q "
			. "and name=%Q ",
			session_id(), $this->name);
		$this->act($sql);
	}
}
function &getCart($type) {
	return new Cart($type);
}
