<?php
class List_Controller extends MasterController {
		if ($this -> module_metadata["type"] == "Config") {
		$this -> module_go -> page("add.php?mod=" . $this -> module_io -> mod);
	}
		if (!is_array($this -> module_io -> ids)) {
			return;
		}
		foreach ($this -> module_io -> ids as $id) {
			$module = $this -> module_dao -> get_object("module", $id);
		$this -> module_go -> back();
	}