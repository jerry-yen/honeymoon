<?php
interface HM_Module {
	/**
	 * 載入系統設定值 (設定檔位置 /system/config.php )
	 */
	public function set_config($sys_configs = array());

	/**
	 * Controller 被載入時優先執行此函式
	 */
	public function init();
	
	/**
	 * Controller 在執行main之前會先執行此函式
	 */
	public function before_main($modules);

	/**
	 * Controller 在載入View之前會先執行此函式
	 */
	public function before_load_view($modules);

	/**
	 * Controller 在載入View之後會先執行此函式
	 */
	public function after_load_view($modules);
}


class Base_Module implements HM_Module {
	
	/**
	 * @var 模組設定值
	 */
	protected $module_configs = array();
	
	/**
	 * @var 系統設定值
	 */
	protected $system_configs = array();
	
	/**
	 * @var 目前正在執行的 Controller
	 */
	protected $controller;
	
	public function __construct() {
		// 取得被繼承的模組路徑
		$reflection = new ReflectionClass($this);
		$dir = dirname($reflection -> getFileName());
		unset($reflection);

		// 讀取模組設定檔
		$config = array();
		require_once ($dir . "/config.php");
		$this -> module_configs = & $configs;
		
		$this -> controller = Base_Controller::get_instance();
	}
	
	/**
	 * 載入系統設定值 (設定檔位置 /system/config.php )
	 */
	public function set_config($system_configs = array()){
		$this -> system_configs = & $system_configs;
	}

	/**
	 * Controller 被載入時優先執行此函式
	 */
	public function init(){}
	
	/**
	 * Controller 在執行main之前會先執行此函式
	 */
	public function before_main($modules){}

	/**
	 * Controller 在載入View之前會先執行此函式
	 */
	public function before_load_view($modules){}

	/**
	 * Controller 在載入View之後會先執行此函式
	 */
	public function after_load_view($modules){}
}
?>