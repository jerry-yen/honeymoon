<?php
interface HM_Controller {

	/**
	 * Controller 被載入時優先執行此函數
	 */
	public function init();

	/**
	 * 框架執行初始化之後，會先執行此函數進行身份驗證
	 * 如系統有任何需要驗證身份的程式碼皆可寫在這邊
	 */
	public function verification();

	/**
	 * 如每頁都會執行到的程式碼，可放在此函式
	 */
	public function global_code();

	/**
	 * 在執行主程式之前會執行此函式
	 */
	public function before_main();

	/**
	 * 主程式
	 */
	public function main();

	/**
	 * Controller 在載入View之前會先執行此函式
	 */
	public function before_load_view();

	/**
	 * Controller 在載入View 函式
	 */
	public function load_view();

	/**
	 * Controller 在載入View之後會先執行此函式
	 */
	public function after_load_view();
}

/**
 * 框架的原生 Controller
 */
class Base_Controller implements HM_Controller {

	/**
	 * @var 模組陣列
	 */
	public $modules = array();

	/**
	 * @var 設定值陣列
	 */
	public $configs = array();
	
	/**
	 * @var Controller 靜態物件
	 */
	protected static $instance;

	/**
	 * 由框架取得所有模組 及 設定值 之後傳給 Controller
	 * 讓所有程式都能使用
	 */
	public function __construct(&$modules, &$configs) {
		$this -> modules = &$modules;
		$this -> configs = &$configs;
		self::$instance = &$this;
		

	}

	/**
	 * 執行所有含有 init函式的模組
	 */
	public function init() {
		foreach ($this -> modules as $module) {
			if (method_exists($module, "init")) {
				$module -> init();
			}
		}
	}

	/**
	 * 不做任何驗證 ( 如繼承的 Controller 有需求則覆寫即可 )
	 */
	public function verification() {
	}

	/**
	 * 不做任何動作 ( 如繼承的 Controller 有需求則覆寫即可 )
	 */
	public function global_code() {
	}
	
	/**
	 * 執行所有含有 before_main 函式的模組
	 */
	public function before_main() {
		foreach ($this -> modules as $module) {
			if (method_exists($module, "before_main")) {
				$module -> before_main($module);
			}
		}
	}

	/**
	 * 不做任何動作 ( 如繼承的 Controller 有需求則覆寫即可 )
	 */
	public function main() {
	}

	/**
	 * 執行所有含有 before_load_view 函式的模組
	 */
	public function before_load_view() {
		foreach ($this -> modules as $module) {
			if (method_exists($module, "before_load_view")) {
				$module -> before_load_view($this -> modules);
			}
		}
	}

	/**
	 * 載入View (載入同名檔案)
	 */
	public function load_view() {
		$view_path = $this -> config_full_execute_php_path;
		ob_start();
		include ($view_path);
		$view_content = ob_get_contents();
		ob_end_clean();
		echo $view_content;
	}

	/**
	 * 執行所有含有 before_load_view 函式的模組
	 */
	public function after_load_view() {
		foreach ($this -> modules as $module) {
			if (method_exists($module, "after_load_view")) {
				$module -> after_load_view($this -> modules);
			}
		}
	}

	/**
	 * Magic Method 可快速取得模組及設定值
	 * 範例：
	 * 	$this -> module_database -> query();
	 * 	$this -> config_system_path;
	 */
	public function __get($name) {
		$v = explode("_", $name);

		// 取得模組
		if ($v[0] == "module") {
			unset($v[0]);
			$module_name = implode("_", $v);
			if(isset($this -> modules[$module_name])){
				return $this -> modules[$module_name];
			}
			else{
				throw new Exception("Not Found Module '{$module_name}' !");
			}
		}

		// 取得設定值
		if ($v[0] == "config") {
			unset($v[0]);
			$config_name = implode("_", $v);
			return (isset($this -> configs[$config_name])) ? $this -> configs[$config_name] : null;
		}

		return null;
	}

	/**
	 * 可隨時取得 Controller
	 */
	public static function & get_instance() {
		return self::$instance;
	}

}
?>