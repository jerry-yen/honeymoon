<?php
require_once ("DataAccessObject.php");

/**
 * 資料存取元件模組
 * 需先引用 db 模組
 */
class Dao extends Base_Module {

	public function __construct() {
		parent::__construct();
		// 沒有先引用 database 模組

		if (!$this -> controller -> module_loader -> is_exists("db")) {
			throw new Exception("Database module is not found!");
		}
	}

	/**
	 * 產生新的資料存取物件
	 * @param string $table_name 類別名稱
	 * @param string | array $data 預設的查詢資訊
	 */
	public function get_object($table_name, $data = null) {
		$units = preg_split("/[\-_\.]/i", $table_name);
		foreach ($units as $key => $unit) {
			$units[$key] = ucfirst($units[$key]);
		}
		$class = implode("", $units);

		$class_name = "MDL_" . $class;
		if(file_exists($this -> system_configs["full_model_path"] . "/" . $class_name . ".php")){
			require_once ($this -> system_configs["full_model_path"] . "/" . $class_name . ".php");
		}
		else{
			$class_name = "MDL_Universal";
			require_once ($this -> system_configs["full_model_path"] . "/MDL_Universal.php");
		}
// echo $table_name . "\r\n";
		$object = new $class_name($table_name);
		$object -> load_data($data);
		return $object;
	}

}
?>