<?php
/**
 * 網域 Controller
 * 自動驗證是否登入
 */
class DomainController extends Base_Controller {

	/**
	 * @var 是否為多網域設定
	 */
	protected $is_domain = false;

	/**
	 * @var 網域模組
	 */
	protected $mod_domain = array();

	/**
	 * @var 網域資訊
	 */
	protected $usr_domain = array();
	public function global_code() {
		
		@session_start();
		$_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"] = null;
		@session_write_close();
			

		$this -> mod_domain = $this -> module_dao -> get_object("module");
		$this -> mod_domain -> get_module("domain");
		if ($this -> mod_domain -> is_exists()) {
		
			$this -> usr_domain = $this -> mod_domain -> get_domain();
			
			if (!$this -> usr_domain -> is_exists()) {
				
				// 超管網域
				if($_SERVER["HTTP_HOST"] == $this -> mod_domain -> admin_domain){
					return;
				}
				
				exit("此單位尚未註冊");
			}
			$this -> is_domain = true;
			@session_start();
			$_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"] = $this -> usr_domain -> id;
			@session_write_close();
		}
		
	}
}
?>