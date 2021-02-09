<?php
class Login_Controller extends AdminController {

	public function main() {

	}

	public function login() {

		$login_success = false;
		
		foreach($this -> languages as $language){
			if($language -> id == $this -> module_io -> language){
				@session_start();
				$_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"] = $language -> id;
				@session_write_close();
				break;
			}
		}

		$this -> module_io -> mod = 'manager';

		// 總管理者登入
		if ($this -> module_io -> account == $this -> mod_login -> account && $this -> module_io -> password == $this -> mod_login -> password) {

			$login_success = true;
			@session_start();
			$_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"] = "super";
			$_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"] = "super-admin";
			@session_write_close();
		}
		// 多網域
		else if ($this -> is_domain) {

			// 網域管理者
			if ($this -> usr_domain -> account -> get_value() == $this -> module_io -> account && $this -> usr_domain -> password -> get_value() == $this -> module_io -> password) {

				$login_success = true;
				@session_start();
				$_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"] = $this -> usr_domain -> id;
				$_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"] = "domain";
				@session_write_close();

			}
			// 指定用戶模組登入
			else if (isset($this -> module_io -> mod)) {
				$login_success = $this -> login_in_module();
			}

		}
		// 指定用戶模組登入
		else if (isset($this -> module_io -> mod)) {
			$login_success = $this -> login_in_module();
		}

		// 登入成功
		if ($login_success) {
			$module = $this -> module_dao -> get_object("module");
			$modules = $module -> get_modules();

			foreach ($modules as $module) {
				if ($module -> landing_page == "Y") {
					$this -> module_go -> page("list.php?mod=" . $module -> code);
				}
			}

			$this -> module_showbox -> set_message("尚未設定登入頁");
		} else {
			$this -> module_alert -> set_message("帳號密碼錯誤");
		}
	}

	private function login_in_module() {
		
		$login_success = false;
		
		$module = $this -> module_dao -> get_object("module");
		$module -> get_module($this -> module_io -> mod);

		if ($module -> moduleType != "User") {
			$this -> module_showbox -> set_message("模組載入錯誤");
			$this -> module_go -> back();
		}

		$item = $module -> get_single_item(array("account=?", "password=?"), array($this -> module_io -> account, $this -> module_io -> password));

		if ($item -> is_exists()) {
			$login_success = true;
			@session_start();
			$_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"] = $item -> id;
			$_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"] = $this -> module_io -> mod;
			@session_write_close();
		}
		
		return $login_success;
	}

}
?>