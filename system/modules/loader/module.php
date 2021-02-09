<?php
/**
 * 模組載入器
 */
class Loader extends Base_Module {
	/**
	 * 載入模組
	 * @param string $module_name 模組名稱
	 */
	public function load($module_name) {
		// $this -> controller = Base_Controller::get_instance();
		if (!isset($this -> controller -> modules[$module_name])) {			if (file_exists($this -> system_configs["full_application_path"] . "/modules/" . $module_name . "/module.php")) {				require_once ($this -> system_configs["full_application_path"] . "/modules/" . $module_name . "/module.php");			} else {
				require_once ($this -> system_configs["full_system_path"] . "/modules/" . $module_name . "/module.php");
			}
			$class_name = ucfirst($module_name);
			$this -> controller -> modules[$module_name] = new $class_name();
			$this -> controller -> modules[$module_name] -> set_config($this -> controller -> configs);
		}
	}	/**
	 * 卸載模組
	 * @param string $module_name 模組名稱
	 */
	public function unload($module_name) {
		if (isset($this -> controller -> modules[$module_name])) {
			unset($this -> controller -> modules[$module_name]);
		}
	}	/**
	 * 模組是否存在
	 * @param string $module_name 模組名稱
	 */
	public function is_exists($module_name) {
		return isset($this -> controller -> modules[$module_name]);
	}}?>