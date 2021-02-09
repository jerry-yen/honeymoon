<?php
/**
 * 訊息 模組
 */
class Ga_Event extends Base_Module {
	public function set_event($category, $action, $label, $value="") {
		@session_start();
		$_SESSION["event_category"] = $category;
		$_SESSION["event_action"] = $action;
		$_SESSION["event_label"] = $label;
		$_SESSION["event_value"] = $value;
		@session_write_close();
	}

	public function after_load_view() {
		@session_start();
		if (isset($_SESSION["event_category"]) && trim($_SESSION["event_category"]) != "") {
			echo "<script>";
			echo "$(document).ready(function(){
					ga('send','event','" . $_SESSION["event_category"] . "','" . $_SESSION["event_action"] . "','" . $_SESSION["event_label"] . "');
				});";
			echo "</script>";
			
			unset($_SESSION["event_category"]);
			unset($_SESSION["event_action"]);
			unset($_SESSION["event_label"]);
			unset($_SESSION["event_value"]);
		}
		@session_write_close();
	}

}
?>