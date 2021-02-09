<?php
class Fix_Controller extends MasterController {

	/**
	 * @var 模組元資訊
	 */
	protected $module_metadata = array();

	/**
	 * @var 模組物件
	 */
	protected $module = array();
	public function main() {

		// 取得模組代碼 並 取得模組元件
		$module_code = $this -> module_io -> mod;

		// 驗證模組是否存在
		if (!isset($this -> master_config["modules"][$module_code])) {
			$this -> module_showbox -> set_message("不存在的模組");
			$this -> module_go -> back();
		}

		// 取得模組元資訊
		$this -> module_metadata = $this -> master_config["modules"][$module_code];

		// 載入模組元件
		$this -> load_module($module_code);	}

	/**
	 * 載入模組物件資訊
	 */
	private function load_module($module_code) {
		
		$module = $this -> module_dao -> get_object("module");
		
		// 設定類型的開發者模組
		if ($this -> module_metadata["type"] == "Config") {
			
			// 取得指定代碼的模組物件
			$module -> get_module($module_code);

			// 因為是設定類型的開發者模組，所以找不到就新增一個(以利設定)
			if (!$module -> is_exists()) {
				$module -> title = $this -> module_metadata["title"];
				$module -> code = $module_code;
				$module -> moduleType = $this -> module_metadata["type"];
				$module -> fieldMetadata = json_encode($this -> module_metadata["fields"]);
				$module -> createTime = date("Y-m-d H:i:s");

				// 初始化所有自訂欄位(初始化為空值)
				foreach ($this -> module_metadata["fields"] as $key => $field) {
					$module -> {$key} = "";
				}
				
				// 新增
				$module -> insert(false, true);
			}
		}
		// 如果不是設定頁面的話
		else{
			$module = $this -> module_dao -> get_object("module", $this -> module_io -> id);
			if (!$module -> is_exists()) {
				$this -> module_showbox -> set_message("查不到模組");
				$this -> module_go -> back();
			}
		}
		
		$module -> extend_field_value($module);
		$this -> load_component($module, $this -> module_metadata["fields"]);
		
		$this -> module = $module;
	}

	/**
	 * 載入操作元件
	 * @param MDL_Module $module 模組
	 * @param Array $fieldMetadatas 欄位資訊
	 */
	private function load_component(& $module, $fieldMetadatas) {

		require_once ($this -> config_full_application_path . "/custom/components/Component.php");

		foreach ($fieldMetadatas as $key => $fieldMetadata) {
			require_once ($this -> config_full_application_path . "/custom/components/" . $fieldMetadata["type"] . "_Component.php");
			$component_name = $fieldMetadata["type"] . "_Component";
			$fieldMetadata["variable"] = $key;
			$module -> {$fieldMetadata["variable"]} = new $component_name($module -> {$fieldMetadata["variable"]} , $fieldMetadata, $module);
		}
		
	}

	/**
	 * 儲存
	 */
	public function save() {
		// 欄位驗證是否過關
		$valid_success = true;
		
		// 開始儲值輸入值至元件
		foreach($this -> module_metadata["fields"] as $key => $fieldMetadata){
			$field = $this -> module -> {$key};
			$value = $this -> module_io -> {$key};
			
			// 把輸入值設定至元件
			$field -> set_value( $value );
			
			// 到目前為止是否所有欄位都驗證成功？
			$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
		}
		
		// 驗證成功 (儲存)
		if($valid_success){

			$this -> module -> update();
			$this -> module_showbox -> set_message("修改成功!");
			
			if(!in_array($this -> module -> moduleType,array("Note","Config","Permission"))){
				$this -> module_loader -> load("dynamic_db_field");
				$this -> module_dynamic_db_field -> set_db_name($this -> module -> code -> get_value());
				$this -> module_dynamic_db_field -> alter_table($this -> module, array("fieldMetadata"));
				
				$this -> module_dynamic_db_field -> set_db_name($this -> module -> code -> get_value(), true);
				$this -> module_dynamic_db_field -> alter_table($this -> module, array("class_fieldMetadata","class_special_fieldMetadata"));
			}
			
			// 設定頁面就不要再返回上一頁了
			if($this -> module -> moduleType != "Config"){
				$this -> module_go -> back();
			}
		}
	}
	
}
?>