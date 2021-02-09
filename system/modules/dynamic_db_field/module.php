<?php
/**
 * 資料庫動態欄位執行模組
 */
class Dynamic_Db_Field extends Base_Module {
	
	protected $db_name;
	protected $is_class = false;
	protected $extend = false;
	
	public function set_db_name($db_name, $is_class = false){
		$this -> db_name = $db_name;
		$this -> is_class = $is_class;
	}

	public function create_table($module, $fieldMetadatas){
		
		if(isset($module -> ignore_database) && $module -> ignore_database -> get_value() == "Y"){
			return false;
		}
		
		$extend = explode("*", $this -> db_name);
		// 共用資料表
		if(count($extend) > 1){
			$this -> db_name = $extend[0];
			$this -> extend = true;
		}
		
		if($this -> is_class){
			$this -> db_name .= "_class";
		}
		
		
		
		$can_create_table = false;
		
		foreach($fieldMetadatas as $fieldMetadata){
			$metadata_name = $fieldMetadata . "_field_name";
			$field_count = count($this -> controller -> module_io -> {$metadata_name});
			
			if($field_count > 1){
				$can_create_table = true;
				break;
			}
		}
		
		if(!$can_create_table){
			return false;
		}
		
		// 判斷資料表是否存在
		$check_table_sql = "SHOW TABLES LIKE '<prefix>{$this -> db_name}';";
		$this -> controller -> module_db -> set_command($check_table_sql);
		$res = $this -> controller -> module_db -> execute_query();
		// 不存在則建立
		if(count($res) == 0){
			$create_table_sql = "CREATE TABLE <prefix>{$this -> db_name} (
									id varchar(40) PRIMARY KEY NOT NULL COMMENT '識別碼', 
									domainId varchar(40) NULL COMMENT '網域識別碼',
									langId varchar(40) NULL COMMENT '語系識別碼',
									moduleId varchar(40) NULL COMMENT '模組識別碼',
									parentId varchar(40) NULL COMMENT '父分類或父項目識別碼',
									level int(11) NULL COMMENT '分類層數',
									extFieldValue TEXT NULL COMMENT '額外欄位資訊',
									createTime DATETIME NULL COMMENT '建立時間',
									updateTime DATETIME NULL COMMENT '更新時間',
									sortTime DATETIME NULL COMMENT '排序時間',
									sequence int(11) NULL COMMENT '排列順序',
									topTime DATETIME NULL COMMENT '置頂時間'
								) ENGINE=MyISAM; ";
			$this -> controller -> module_db -> set_command($create_table_sql);
			$res = $this -> controller -> module_db -> execute_single_query();
		}
		
		return true;
	}
	
	public function get_table_fields(){
		// 取得原有欄位
		$describe_sql = "DESCRIBE <prefix>{$this -> db_name};";
		$this -> controller -> module_db -> set_command($describe_sql);
		$res = $this -> controller -> module_db -> execute_query();
		
		// 0:能刪； 1:不能刪
		$fields = array();
		foreach ($res as $field) {
			$fields[$field["Field"]] = 0;
		}
		
		// 欄位固定班底，不能刪
		$fields["id"] = 1;
		$fields["domainId"] = 1;
		$fields["langId"] = 1;
		$fields["moduleId"] = 1;
		$fields["parentId"] = 1;
		$fields["level"] = 1;
		$fields["extFieldValue"] = 1;
		$fields["createTime"] = 1;
		$fields["updateTime"] = 1;
		$fields["sortTime"] = 1;
		$fields["sequence"] = 1;
		$fields["topTime"] = 1;
		
		return $fields;
	}
	public function alter_table($module, $fieldMetadatas = array()){
		
		$db_name = $this -> create_table($module, $fieldMetadatas);
		if(!$db_name){
			return;
		}
		$fields = $this -> get_table_fields();

		foreach($fieldMetadatas as $fieldMetadata){
		
			$metadata_name = $fieldMetadata . "_field_name";
			$metadata_variable = $fieldMetadata . "_field_variable";
			$metadata_type = $fieldMetadata . "_field_type";
			$metadata_element = $fieldMetadata . "_field_element";
			$field_count = count($this -> controller -> module_io -> {$metadata_name});
		
			for($i=0 ; $i < $field_count -1 ; $i++){
				$name = $this -> controller -> module_io -> {$metadata_name}[$i];
				$variable = $this -> controller -> module_io -> {$metadata_variable}[$i];
				$type = $this -> controller -> module_io -> {$metadata_type}[$i];
				$element = $this -> controller -> module_io -> {$metadata_element}[$i];
				
				// 原本不儲存的欄位(新增)
				if(!isset($fields[$variable])){
					
					$datatype = "VARCHAR(100)";
					if(in_array($type,array("jQueryDate","Html5Date"))){
						$datatype = "DATETIME";
					}
					if(in_array($type,array("Number"))){
						$datatype = "INT(11)";
					}
					if(in_array($type,array("TextArea","HtmlEditor"))){
						$datatype = "TEXT";
					}
					$describe_sql = "ALTER TABLE <prefix>{$this -> db_name} ADD {$variable} {$datatype}  COMMENT '{$name}';";
					$this -> controller -> module_db -> set_command($describe_sql);
					$res = $this -> controller -> module_db -> execute_query();
				}
				else{
					$fields[$variable] ++;
				}
			}
		}

		if(!$this -> extend){
			// 去掉不要的欄位
			foreach($fields as $key => $value){
				if($value == 0){
					$describe_sql = "ALTER TABLE <prefix>{$this -> db_name} DROP COLUMN {$key};";
					$this -> controller -> module_db -> set_command($describe_sql);
					$res = $this -> controller -> module_db -> execute_query();
				}
			}
		}
	}	

}
?>