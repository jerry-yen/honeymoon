<?php
/**
 * 訊息 模組
 */
class Alert extends Base_Module {
	public function set_message($message) {
		@session_start();
		$_SESSION["alert_message"] = $message;
		@session_write_close();
	}

	public function after_load_view($modules) {
		@session_start();
		if (isset($_SESSION["alert_message"]) && trim($_SESSION["alert_message"]) != "") {
			$message = $_SESSION["alert_message"];
			$this -> show_message($message);
			unset($_SESSION["alert_message"]);
		}
		@session_write_close();
	}

	private function show_message($message) {
		echo "<link rel='stylesheet' href='" . $this -> system_configs["machine_relative_jquery_lib_path"] . "/sweetalert/sweetalert.css' type='text/css' />";
		echo "<script src='" . $this -> system_configs["machine_relative_jquery_lib_path"] . "/sweetalert/sweetalert-dev.js'></script>";
		echo "<script>";
		echo "$(document).ready(function(){
				swal('{$message}');
			});";
		echo "</script>";
	}

}
?>