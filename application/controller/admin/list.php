<?php
class List_Controller extends AdminController {
		if ($this -> module -> single == "Y") {
	}
		if (!is_array($this -> module_io -> ids)) {
			return;
		}
		foreach ($this -> module_io -> ids as $id) {
			$item_class = $this -> module -> get_single_class(array("id=?"),array($id));
		$this -> module_go -> back(0);
	}