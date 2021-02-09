<?php
/**
 * 資料庫執行模組
 */
class Db extends Base_Module {

	/**
	 * @var 實體
	 */
	private static $instance;

	/**
	 * @var 資料庫連線
	 */
	private $link = null;

	/**
	 * @var 參數
	 */
	private $params = array();

	/**
	 * @var 原始語法
	 */
	private $sql = "";

	/**
	 * 取得資料庫實體
	 * @return Database::$instance
	 */
	public static function & get_database() {
		if (false == (self::$instance instanceof self)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		parent::__construct();
		self::$instance = &$this;
		$this -> link = mysqli_connect($this -> module_configs["host"] . ":" . $this -> module_configs["port"], $this -> module_configs["user"], $this -> module_configs["password"]) or die("Database Parameter Invalid.");
		mysqli_query($this -> link, "SET NAMES '" . $this -> module_configs["charset"] . "'") or die("CharSet Error");
		mysqli_select_db($this -> link, $this -> module_configs["database"]) or die("Database Selected Error");
	}

	/**
	 * 查詢執行
	 * @access public
	 * @param string $sql SQL語法
	 * @return item[] $results
	 */
	public function & execute_query() {

		$count = 0;

		// 是否分頁
		if ($this -> controller -> module_loader -> is_exists("pagination")) {
			$count = $this -> execute_query_num();
		}

		$sql = $this -> into_command();

		// 是否分頁
		if ($this -> controller -> module_loader -> is_exists("pagination") && $this -> controller -> module_pagination -> get_lock_status() == "UNLOCK") {

			$this -> controller -> module_pagination -> set_total_count($count);

			$per_page = $this -> controller -> module_pagination -> get_count_per_page();
			$start_index = $this -> controller -> module_pagination -> get_start_index();

			if ($per_page > -1) {
				// 計算分頁數量取得SQL的 LIMIT參數值
				$sql = preg_replace("/;|limit .*?$/i", "", $sql) . (" LIMIT " . $start_index . "," . $per_page) . ";";
			}
		}
		
		$res = mysqli_query($this -> link, $sql);

		$results = array();
		while ($res && $row = mysqli_fetch_assoc($res)) {
			$results[] = $row;
		}
		unset($row);
		unset($res);
		return $results;
	}

	/**
	 * 查詢單筆結果
	 * @return item $result
	 */
	public function & execute_single_query() {
		$sql = $this -> into_command();
		$res = mysqli_query($this -> link, $sql);
		$result = null;
		if ($row = mysqli_fetch_assoc($res)) {
			$result = $row;
		}
		unset($row);
		unset($res);
		return $result;
	}

	/**
	 * 查詢結果筆數
	 * @return integer $count
	 */
	public function execute_query_num() {

		$count = 0;

		$sql = $this -> into_command(false);

		// 取得此查詢總筆數
		$count_sql = preg_replace(array('/SELECT.*?FROM /Asi', '/SELECT \*,/Asi', '/ORDER BY .*/'), array('SELECT COUNT(*) AS counter FROM ', 'SELECT COUNT(*) AS counter,', ''), $sql);

		$res = mysqli_query($this -> link, $count_sql);

		if ($res && $row = mysqli_fetch_assoc($res)) {
			$count = isset($row["counter"]) ? $row["counter"] : 0;
		}
		unset($row);
		unset($res);

		return $count;

	}

	/**
	 * 執行新增、修改、刪除
	 * @access public
	 * @param string $sql SQL語法
	 */
	public function execute() {
		$sql = $this -> into_command();
		mysqli_query($this -> link, $sql);
	}

	public function __destruct() {
		@mysqli_close($this -> link);
		unset($this -> link);
	}

	/**
	 * 帶入字串參數
	 */
	public function set_string($value) {
		if ($value === null) {
			$this -> params[] = "NULL";
		} else {
			$this -> params[] = "'" . mysqli_real_escape_string($this -> link, $value) . "'";
		}
	}

	/**
	 * 帶入數值參數
	 */
	public function set_number($value) {
		if ($value === null) {
			$this -> params[] = "NULL";
		} else if (is_numeric($value)) {
			$this -> params[] = $value;
		}
	}

	/**
	 * 帶入UUID 參數
	 */
	public function set_uuid() {
		if (isset($this -> controller -> modules["code"])) {
			$this -> params[] = "'" . $this -> controller -> modules["code"] -> get_uuid() . "'";
		} else {
			throw new Exception("Code module is not found!");
		}
	}

	public function set_data($data) {

		// 進資料庫資訊不可為陣列
		if (is_array($data)) {
			ob_start();
			print_r($data);
			$err_data = ob_get_contents();
			ob_end_clean();
			throw new Exception("Can't save array : " . $err_data);
		}

		switch(true) {
			case is_null($data) :
				$this -> params[] = "NULL";
				break;
			case is_double($data) :
			case is_float($data) :
			case is_int($data) :
			case is_integer($data) :
			case is_long($data) :
				$this -> set_number($data);
				break;
			case is_string($data) :
				$this -> set_string($data);
		}
	}

	/**
	 * 設定語法
	 * @param string $sql
	 */
	public function set_command($sql) {
		$this -> sql = &$sql;
	}

	/**
	 * 帶入參數至原始語法
	 * @return string $sql
	 */
	private function into_command($clear = true) {
		$datas = explode("?", $this -> sql);
		if (isset($this -> params) && count($this -> params) != count($datas) - 1) {
			return "";
		}
		
		$sql = "";
		foreach ($datas as $key => $data) {
			$param = isset($this -> params[$key]) ? $this -> params[$key] : "";
			$sql .= $data . $param;
		}

		if ($clear) {
			$this -> sql = "";
			unset($this -> params);
		}

		$sql = str_replace("<prefix>", $this -> module_configs["prefix"], $sql);

		if ($this -> controller -> module_loader -> is_exists("logger") && $clear) {
			$this -> controller -> module_logger -> log($sql, "database");
		}
		
		return $sql;
	}

}
?>