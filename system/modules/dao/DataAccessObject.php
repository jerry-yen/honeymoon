<?php
/**
 * 資料存取元件
 */
class DataAccessObject {
	/**
	 * @var 資料列
	 */
	protected $data = array();

	/**
	 * @var 資料表名稱
	 */
	protected $table_name = "";

	public function __set($name, $value) {
		if (method_exists($this, "set_" . $name)) {
			$this -> data[$name] = $this -> {"set_".$name}($value);
		} else {
			$this -> data[$name] = $value;
		}
	}

	public function __get($name) {

		if (method_exists($this, "get_" . $name)) {
			return $this -> {"get_".$name}((isset($this -> data[$name])) ? $this -> data[$name] : null);
		} else {
			return (isset($this -> data[$name])) ? $this -> data[$name] : null;
		}
	}

	public function __init($data) {
		$this -> data = $data;
	}

	public function __isset($name) {
		return isset($this -> data[$name]);
	}

	public function __unset($name) {
		unset($this -> data[$name]);
	}

	public function __construct($table_name, $data = null) {
		$this -> table_name = $table_name;
		if (isset($data)) {
			$this -> load_data($data);
		}
	}

	/**
	 * 初始載入資訊
	 * @param string | array $data 記錄資訊
	 */
	public function load_data($data = null) {
		if (!isset($data)) {
			return;
		}

		if (is_array($data)) {
			$this -> data = $data;
		} else {

			$controller = Base_Controller::get_instance();

			$sql = "SELECT * FROM <prefix>{$this->table_name} ";
			if (is_int($data) || is_float($data)) {
				$sql .= "WHERE `id` = {$data}";
			} else {
				$sql .= "WHERE `id` = '{$data}'";
			}

			$controller -> module_db -> set_command($sql);
			$res = $controller -> module_db -> execute_single_query();

			if ($res) {
				$this -> __init($res);
			}
			unset($res);
		}
	}

	/**
	 * 新增資料
	 * @param boolean $auto 自動編號
	 * @param boolean $uuid 自動UID編號
	 * @return string | int 此筆記錄新增的編號
	 */
	public function insert($auto = true, $uuid = false) {

		$controller = Base_Controller::get_instance();

		// 資料庫自動編號
		if ($auto) {
			unset($this -> data["id"]);
		}
		// 自動產生 UID
		if ($uuid) {

			if (!$controller -> module_loader -> is_exists("code")) {
				throw new Exception("Code module is not found!");
			}
			$this -> data["id"] = $controller -> module_code -> get_uuid();
		}

		$fields = array();
		$params = array();
		foreach ($this->data as $field_name => $value) {
			$fields[] = $field_name;
			$params[] = "?";
			$controller -> module_db -> set_data($value);
		}

		$sql = "INSERT INTO <prefix>{$this->table_name} (" . implode(",", $fields) . ") VALUES (" . implode(",", $params) . ");";

		$controller -> module_db -> set_command($sql);
		$controller -> module_db -> execute();

		if ($auto) {
			$this -> data["id"] = mysql_insert_id();
		}
		return $this -> data["id"];
	}

	/**
	 * 更新資料
	 */
	public function update() {

		if (!isset($this -> data["id"])) {
			throw new Exception("Id is undefined!");
		}

		$controller = Base_Controller::get_instance();

		$pairs = array();
		foreach ($this->data as $field_name => $value) {
			$pairs[] = $field_name . "=?";
			$controller -> module_db -> set_data($value);
		}

		$controller -> module_db -> set_data($this -> data["id"]);

		$sql = "UPDATE <prefix>{$this->table_name} SET " . implode(",", $pairs) . " WHERE id=?;";

		$controller -> module_db -> set_command($sql);
		$controller -> module_db -> execute();

		return true;
	}

	/**
	 * 刪除資料
	 */
	public function delete() {

		if (!isset($this -> data["id"])) {
			throw new Exception("Id is undefined!");
		}

		$controller = Base_Controller::get_instance();

		$controller -> module_db -> set_data($this -> data["id"]);

		$sql = "DELETE FROM <prefix>{$this->table_name} WHERE id=?;";

		$controller -> module_db -> set_command($sql);
		$controller -> module_db -> execute();

		return true;
	}

	/**
	 * 基本查詢(多筆結果)
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 * @param string $table_name 表單名稱
	 */
	public function query($where = array(), $values = array(), $sort = array(), $table_name = "") {
		$controller = Base_Controller::get_instance();

		// 條件式
		$where_string = "";
		if (count($where) > 0) {
			$where_string = "WHERE " . implode(" AND ", $where);
			foreach ($values as $value) {
				$controller -> module_db -> set_data($value);
			}
		}

		// 排序條件
		$sort_string = "";
		if (count($sort) > 0) {
			$sort_string = "ORDER BY " . implode(", ", $sort);
		}
		
		// 表單名稱
		$table_name = ($table_name == "") ? $this -> table_name : $table_name;

		$sql = "SELECT * FROM <prefix>{$table_name} {$where_string} {$sort_string};";
		$controller -> module_db -> set_command($sql);
		$res = $controller -> module_db -> execute_query();

		$items = array();
		foreach ($res as $item) {
			$items[] = $controller -> module_dao -> get_object($this -> table_name, $item);
		}
		return $items;
	}
	
	/**
	 * 自定義：SQL語法
	 * @param string $sql
	 */
	public function define_query($sql){
		$controller -> module_db -> set_command($sql);
		$res = $controller -> module_db -> execute_query();

		$items = array();
		foreach ($res as $item) {
			$items[] = $controller -> module_dao -> get_object($this -> table_name, $item);
		}
		return $items;
	}

	/**
	 * 基本查詢(單筆結果)
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 */
	public function single_query($where = array(), $values = array(), $sort = array(), $table_name = "") {
		$res = $this -> query($where, $values, $sort, $table_name);

		if (count($res) > 0) {
			return $res[0];
		} else {
			$controller = Base_Controller::get_instance();
			
			// 表單名稱
			$table_name = ($table_name == "") ? $this -> table_name : $table_name;
			
			return $controller -> module_dao -> get_object($table_name);
		}
	}
	
	/**
	 * 此筆資料是否存在
	 * @return boolean
	 */
	public function is_exists() {
		return isset($this -> data["id"]);
	}
	
	/**
	 * 判斷如果不是JSON格式
	 * @param string $value
	 * @return boolean
	 */
	public function is_json($value){
		if(is_array($value)) return false;
		
    	return ! is_null(json_decode($value));
	}

	/**
	 * 取得資料陣列
	 * @return array
	 */
	public function to_array() {
		return $this -> data;
	}

	/**
	 * 取得 JSON格式
	 * @return string
	 */
	public function to_json() {
		return json_encode($this -> data);
	}

	/**
	 * 取得所有欄位名稱
	 * @return string[] $fields
	 */
	public function get_fields() {
		$controller = Base_Controller::get_instance();
		$sql = "DESCRIBE <prefix>{$this->table_name};";
		$controller -> module_db -> set_command($sql);
		$res = $controller -> module_db -> execute_query();
		$fields = array();
		foreach ($res as $field) {
			$fields[$field["Field"]] = true;
		}
		return $fields;
	}

	/**
	 * 取得限制長度的資料
	 * @param string $field_name 欄位名稱
	 * @param int $len 限制資料長度
	 * @param boolean $strip_tags 是否過濾 HTML 標籤
	 * @param string $more_string (更多)提示字
	 */
	public function get_limit_data($field_name, $len, $strip_tags = false, $more_string = "...") {
		if (isset($this -> data[$field_name])) {

			$data = ($strip_tags) ? strip_tags($this -> data[$field_name] -> get_value()) : $this -> data[$field_name];

			$source_len = mb_strlen($data, "utf-8");
			// 原始長度比設定長度還短，則顯示全部資料
			if ($len >= $source_len) {
				return $data;
			} else {
				$strlen = mb_strlen($data, 'UTF-8');
				$cutLen = 0;
				$retval = "";
				for ($i = 0; $i < $strlen; $i++) {
					$s = mb_substr($data, $i, 1, 'UTF-8');
					if (strlen($s) == 1) {
						$cutLen+=0.5;
					} else {
						$cutLen += 1;
					}
					$retval .= $s;
					if ($cutLen >= $len) {
						return $retval . $more_string;
					}
				}

				return $retval . $more_string;
			}
		} else {
			return "";
		}
	}

}
?>