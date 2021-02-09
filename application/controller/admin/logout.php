<?php
class Logout_Controller extends AdminController {
	public function main() {
		@session_start();
		unset($_SESSION[ $_SERVER["HTTP_HOST"] . "-login-session"]);
		unset($_SESSION[ $_SERVER["HTTP_HOST"] . "-login-module-code"]);
		@session_write_close();
		
		$this -> module_go -> page($this -> config_machine_relative_admin_path);
	}
}
?>