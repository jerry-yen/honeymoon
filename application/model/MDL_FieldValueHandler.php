<?php
class MDL_FieldValueHandler extends DataAccessObject {
	
	protected $Item_Field_Metadata_Type = array(
			"fieldMetadataName" => "fieldMetadata",
			"name" => "fieldMetadata_field_name",
			"variable" => "fieldMetadata_field_variable",
			"type" => "fieldMetadata_field_type",
			"element" => "fieldMetadata_field_element",
			"default" => "fieldMetadata_field_default",
			"tip" => "fieldMetadata_field_tip",
			"list" => "fieldMetadata_field_list",
	);
	
	protected $Class_Field_Metadata_Type = array(
			"fieldMetadataName" => "class_fieldMetadata",
			"name" => "class_fieldMetadata_field_name",
			"variable" => "class_fieldMetadata_field_variable",
			"type" => "class_fieldMetadata_field_type",
			"element" => "class_fieldMetadata_field_element",
			"default" => "class_fieldMetadata_field_default",
			"tip" => "class_fieldMetadata_field_tip",
			"list" => "class_fieldMetadata_field_list",
	);
	
	protected $Class_Special_Field_Metadata_Type = array(
			"fieldMetadataName" => "class_special_fieldMetadata",
			"name" => "class_special_fieldMetadata_field_name",
			"variable" => "class_special_fieldMetadata_field_variable",
			"type" => "class_special_fieldMetadata_field_type",
			"element" => "class_special_fieldMetadata_field_element",
			"default" => "class_special_fieldMetadata_field_default",
			"tip" => "class_special_fieldMetadata_field_tip",
			"list" => "class_special_fieldMetadata_field_list",
	);
	
	protected $Search_Field_Metadata_Type = array(
			"fieldMetadataName" => "fieldSearch",
			"name" => "fieldSearch_field_name",
			"variable" => "fieldSearch_field_variable",
			"type" => "fieldSearch_field_type",
			"element" => "fieldSearch_field_element",
			"default" => "fieldSearch_field_default",
			"tip" => "fieldSearch_field_tip",
			"list" => "fieldSearch_field_list",
	);
	
	/**
	 * @var 所屬模組
	 */
	protected $module;
	
	/**
	 * 取得模組物件
	 * @return MDL_Module $module
	 */
	/*
	public function get_module(){
		return $this -> module_dao -> get_object("module", $this -> moduleId);
	}
	*/
	/**
	 * 搜尋物件
	 * @param string $table 此物件所屬的表單
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 * @param MDL_Module $module 此物件所屬的模組
	 */
	public function get_objects($table, $where = array(), $values = array(), $sort = array(), $module = null, $fieldMetadataType = array()){
			
		$extend = explode("*", $table);
		
		// 共用資料表
		if(count($extend) > 1){
			$table = $extend[0];
			if(isset($extend[1]) && $extend[1] != ''){
				$table = $extend[1];
			}
		}
		
		@session_start();
		if(isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])){
			if(in_array(strtolower($table),array("module"))){
				return array();
			}
		}
		@session_write_close();
		
		$controller = Base_Controller::get_instance();
		$obj_object = $controller -> module_dao -> get_object($table);
		$objects = $obj_object -> query($where, $values, $sort);
		
		// 展開所有物件的欄位
		foreach($objects as $key => $object){
			$this -> extend_field_value($object);
			$this -> initial_component($object, $module, $fieldMetadataType);
			$object -> module = & $module;
			$objects[$key] = $object;
		}
		
		return $objects;
	}
	
	/**
	 * 搜尋單一物件
	 * @param string $table 此物件所屬的表單
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 * @param MDL_Module $module 此物件所屬的模組
	 */
	public function get_object($table, $where = array(), $values = array(), $sort = array(), $module = null, $fieldMetadataType = array()){
		
		$extend = explode("*", $table);
		// 共用資料表
		if(count($extend) > 1){
			$table = $extend[0];
			if(isset($extend[1]) && $extend[1] != ''){
				$table = $extend[1];
			}
		}
		
		@session_start();
		if(isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])){
			if(in_array(strtolower($table),array("module","user"))){
				return array();
			}
		}
		@session_write_close();
		
		$controller = Base_Controller::get_instance();
		$obj_object = $controller -> module_dao -> get_object($table);
		$objects = $obj_object -> query($where, $values, $sort);
		
		if(count($objects) > 0){
			// 展開物件的欄位
			
			$this -> extend_field_value($objects[0]);
			$this -> initial_component($objects[0], $module, $fieldMetadataType);
			$objects[0] -> module = & $module;
			return $objects[0];
		}
	
		// 找不到則回應一個空物件
		return $this -> get_empty_object($table, $module, $fieldMetadataType, false); 	
	}
	
	/**
	 * 取得空物件
	 * @param string $table 此物件所屬的表單
	 * @param MDL_Module $module 此物件所屬的模組
	 */
	public function get_empty_object($table, $module = null, $fieldMetadataType = array(), $is_new = true){
		
		$extend = explode("*", $table);
		// 共用資料表
		if(count($extend) > 1){
			$table = $extend[0];
		}
		
		
		$controller = Base_Controller::get_instance();
		$empty_object = $controller -> module_dao -> get_object($table);
		if($is_new){
			$empty_object -> id = $controller -> module_code -> get_uuid();
		}
	
		$this -> extend_field_value($empty_object);
		$this -> initial_component($empty_object, $module, $fieldMetadataType);

		$empty_object -> module = & $module;
		return $empty_object; 
	}
	
	/**
	 * 覆寫父類別 insert function
	 */
	public function insert($auto = true, $uuid = false) {

		$this->table_name = str_replace('v_','',$this->table_name);
		
		// 額外欄位
		$extFieldValue = array();
		// 取得資料表的欄位
		$db_fields = $this -> get_fields();
		
		// 取得元件轉換成陣列的資料(才能儲存至資料庫)
		$to_db_data = $this -> component_to_array();
		
		// 將額外資訊集中在一個欄位
		foreach ($to_db_data as $key => $value) {
			
			if(in_array(gettype($value),array("array","object"))){
				$to_db_data[$key] = json_encode($value);
			}
			
			if (!isset($db_fields[$key])) {
				$extFieldValue[$key] = $to_db_data[$key];
				unset($to_db_data[$key]);
			}
		}
		$to_db_data["extFieldValue"] = json_encode($extFieldValue);
		
		/* 
		 * 暫存原本有元件的資料，並替換為「陣列」資料！
		 * 儲存結束之後，再換回來 
		 **/
		 
		$component_data = $this -> data;
		$this -> data = $to_db_data;
		$id = parent::insert($auto, $uuid);
		$this -> data = $component_data;
		
		return $id;
	}

	/**
	 * 覆寫父類別 update function
	 */
	public function update() {

		$this->table_name = str_replace('v_','',$this->table_name);
		// 額外欄位
		$extFieldValue = array();
		// 取得資料表的欄位
		$db_fields = $this -> get_fields();
		
		// 取得元件轉換成陣列的資料(才能儲存至資料庫)
		$to_db_data = $this -> component_to_array();
		
		// 將額外資訊集中在一個欄位
		foreach ($to_db_data as $key => $value) {
			if(in_array(gettype($value),array("array","object"))){
				$to_db_data[$key] = json_encode($value);
			}

			if (!isset($db_fields[$key])) {
				$extFieldValue[$key] = $to_db_data[$key];
				unset($to_db_data[$key]);
			}			
		}
		$to_db_data["extFieldValue"] = json_encode($extFieldValue);
		
		/* 
		 * 暫存原本有元件的資料，並替換為「陣列」資料！
		 * 儲存結束之後，再換回來 
		 **/
				
		$component_data = $this -> data;
		$this -> data = $to_db_data;
		$res = parent::update();
		$this -> data = $component_data;
		
		return $res;
	}
	
	/**
	 * 取得資料陣列函數 ( 過程需將元件轉換成資料 )
	 * @return $data
	 */
	public function component_to_array(){
		
		$data = array();
		
		foreach ($this -> data as $key => $value) {
			$new_value = $value;
			if(gettype($new_value) == "object"){
				$class_name = get_class($new_value);
				if(preg_match("/.*?_Component/i",$class_name)){
					$new_value = $new_value -> get_value();
				}
			}
			$data[$key] = $new_value;
		}
		return $data; 
	}
	
	/**
	 * 展開額外資訊
	 * @param Object $object
	 * @return Object $object 
	 */
	public function extend_field_value(& $object){
			
		if(isset($object -> fieldMetadata)){
			if(!is_array($object -> fieldMetadata) && !is_object($object -> fieldMetadata)){
				$object -> fieldMetadata = json_decode($object -> fieldMetadata);
			}
		}
		
		if(isset($object -> extFieldValue)){
			
			$data = json_decode($object -> extFieldValue);
			foreach($data as $key => $value){
				
				if($this -> is_json($value)){
					$value = json_decode($value);
				}
				
				$object -> {$key} = $value;
			}
			unset($object -> extFieldValue);
		}
	}
	
	/**
	 * 將元件資訊載入欄位
	 * @param Object $object
	 * @return Object $object 
	 */
	public function initial_component(& $object, $module, $fieldMetadataType){
		
		if($fieldMetadataType == array()) return;
		
		$controller = Base_Controller::get_instance();
		
		require_once ($controller -> config_full_application_path . "/custom/components/Component.php");

		$fieldMetadatas = $module -> {$fieldMetadataType["fieldMetadataName"]};

		if($this -> is_json($fieldMetadatas)){
			$fieldMetadatas = json_decode($fieldMetadatas);
		}
		
		foreach ($fieldMetadatas as $fieldMetadata) {
			//print_r($fieldMetadata);

			$meta_field["name"] = $fieldMetadata -> {$fieldMetadataType["name"]};
			$meta_field["variable"] = $fieldMetadata -> {$fieldMetadataType["variable"]};
			$meta_field["type"] = $fieldMetadata -> {$fieldMetadataType["type"]};
			$meta_field["element"] = $fieldMetadata -> {$fieldMetadataType["element"]};
			$meta_field["default"] = $fieldMetadata -> {$fieldMetadataType["default"]};
			$meta_field["tip"] = $fieldMetadata -> {$fieldMetadataType["tip"]};
			$meta_field["list"] = $fieldMetadata -> {$fieldMetadataType["list"]};
			
			require_once ($controller -> config_full_application_path . "/custom/components/" . $meta_field["type"] . "_Component.php");
			$component_name = $meta_field["type"] . "_Component";
			$object -> {$meta_field["variable"]} = new $component_name($object -> {$meta_field["variable"]} , $meta_field, $object);
		
		}

		
	}
	
	
	
}
?>