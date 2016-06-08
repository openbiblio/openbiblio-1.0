<?php
require_once("../shared/common.php");


class ObServer {

	function __construct() {
		$this->tasks = Array();
	}

	public function addTask($request_mode, $task_type) {
		$tmp['request_mode'] = $request_mode;
		$tmp['task_type'] = $task_type;
		switch($task_type) {
			case 'selectAll':
				$tmp['model'] = func_get_arg(2);
				if (func_get_arg(3)) {
					$tmp['order_by'] = func_get_arg(3);
				}
				break;
		}
		$this->tasks[] = $tmp;
	}

	public function respond() {
		$this->mode_requested_by_user = $_POST['mode'];
		if ($this->task_exists()) {
			require_once(REL(__FILE__, '../model/' . $this->current_task['model'] . '.php'));
			$ptr = new $this->current_task['model'];

			switch($this->current_task['task_type']) {
				case 'selectAll':
					if (isset($this->current_task['order_by'])) {
						$set = $ptr->getAll();
					} else {
						$set = $ptr->getAll();
					}
					try {
						$this->response_object = Array();
						foreach ($set as $row) {
							$this->response_object[] = $row;
						}
					} catch (Exception $e) {
						$this->response_object = new ObErr($e);
					}
					break;
			}	
		}
		$this->echo_json_response();
	}

	private function task_exists() {
		if (NULL == $this->mode_requested_by_user) {
			$this->response_object = new ObErr('Please include a mode');
			return false;
		} else  {
			foreach ($this->tasks as $task) {

				if ($this->mode_requested_by_user == $task['request_mode']) {
					$this->current_task = $task;
					return true;
				}
			}
		}
		$this->response_object = new ObErr('invalid mode');
		return false;
	}

	private function echo_json_response() {
		if(!isset($this->response_object)) {
			$this->response_object = new ObErr('The data server is misconfigured');
		}
		echo json_encode($this->response_object);
	}
}
