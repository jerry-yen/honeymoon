<?php
/**
 * 開發者控制器
 * 自動驗證是否登入
 */
class MasterController extends Base_Controller {
	
	/**
	 * @var 開發者設定參數
	 */
	protected $master_config = array();
	
	/**
	 * 開發者設定參數載入
	 */
	public function global_code(){
		include($this -> config_full_application_path . "/custom/config/master.php");
		$this -> master_config = $master_config;
		
		$this -> module_loader -> load("showbox");
		
		// 非登入頁，需要驗證是否已登入
		if(basename($this -> config_full_url) != "login.php"){
			@session_start();
			if(!isset($_SESSION["developer-login-session"])){
				$this -> module_showbox -> set_message("抱歉，您可能尚未登入 或 閒置太久！請重新登入！");
				$this -> module_go -> page("login.php");
			}
			@session_write_close();
		}
	}

	/**
	 * 開發者載入面版
	 */
	public function load_view() {
	
		$path = str_replace($this -> configs["full_root_path"] . "/master", "", $this -> configs["full_execute_php_path"]);
		$view_path = $this -> configs["full_view_path"] . "/master/" . $this -> configs["master_theme"] . $path;
		$theme_path = $this -> configs["machine_relative_view_path"] . "/master/" . $this -> configs["master_theme"];

		ob_start();
		include ($view_path);
		$content = ob_get_contents();
		ob_end_clean();

		$content = preg_replace("/<(link|script|img)(.*?)(src|href)=\"([^\/].*?)\"/", "<$1$2$3=\"{$theme_path}/$4\"", $content);
		$content = preg_replace("/url\('([^\/].*?)'\)/s", "url('{$theme_path}/$1')", $content);
		$content = str_replace("{$theme_path}/http://", "http://", $content);
		$content = str_replace("http://{$_SERVER["HTTP_HOST"]}http://", "http://", $content);

		echo $content;
	}

}
?>