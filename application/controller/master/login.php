<?php
class Login_Controller extends MasterController {

        public function main() {
        	
        }
		
		public function login(){
			if($this -> module_io -> account == $this -> master_config["account"] && $this -> module_io -> password == $this -> master_config["password"]){
				@session_start();
				$_SESSION["developer-login-session"] = uniqid();
				@session_write_close();
				$this -> module_go -> page("fix.php?mod=login");
			}
			else{
				$this -> module_showbox -> set_message("帳號密碼錯誤");
				//$this -> module_go -> back();
			}
		}
}
?>