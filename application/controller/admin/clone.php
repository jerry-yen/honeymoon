<?php
class Clone_Controller extends AdminController {

	/**
	 * @var 模組物件
	 */
	public $module = array();
	
	/**
	 * @var 分類
	 */
	protected $item_class = array();
	public function main() {

		// 取得模組代碼 並 取得模組元件
		$module_code = $this -> module_io -> mod;
		$this -> module = $this -> module_dao -> get_object("module");
		$this -> module -> get_module($module_code);
		
		// 載入項目分類
		$this -> load_item_class();
			}

	/**
	 * 載入項目分類
	 */
	private function load_item_class() {
		$this -> item_class = $this -> module -> get_single_class(array("id=?"),array($this -> module_io -> id));
		if(!$this -> item_class -> is_exists()){
			$this -> module_showbox -> set_message("查無此分類");
			$this -> module_go -> back();
		}
	}

	/**
	 * 欄位驗證
	 */
	private function field_valid() {
		$valid = true;
		foreach ($this -> item_class -> components as $key => $component) {
			$component -> update_value_from_io();
			$valid = $valid & ( $component -> get_valid_error_message() == "" );
		}
		
		if($valid){
			unset($this -> item_class -> components);
			$this -> module -> add_item($this -> item_class);
			
			$this -> module_showbox -> set_message("複製 {$this -> item_class -> title} 成功!");
			$this -> module_go -> back();
		}
	}
	
	/**
	 * 儲存
	 */
	public function save() {
		// 欄位驗證是否過關
		$valid_success = true;
		
		// 開始儲值輸入值至元件
		foreach($this -> module -> class_fieldMetadata as $fieldMetadata){
			
			if(in_array($fieldMetadata -> class_fieldMetadata_field_type, array("Image","File"))){
				continue;
			}
			
			$field = $this -> item_class -> {$fieldMetadata -> class_fieldMetadata_field_variable};
			$value = $this -> module_io -> {$fieldMetadata -> class_fieldMetadata_field_variable};
			// 把輸入值設定至元件
			$field -> set_value( $value );
			
			// 到目前為止是否所有欄位都驗證成功？
			$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
		}
		
		// 驗證成功 (儲存)
		if($valid_success){
			
			foreach($this -> module -> class_fieldMetadata as $fieldMetadata){
			
				if(!in_array($fieldMetadata -> class_fieldMetadata_field_type, array("Image","File"))){
					continue;
				}
				
				$field = $this -> item_class -> {$fieldMetadata -> class_fieldMetadata_field_variable};
				$value = $this -> module_io -> {$fieldMetadata -> class_fieldMetadata_field_variable};
				// 把輸入值設定至元件
				$field -> set_value( $value );
				
				// 到目前為止是否所有欄位都驗證成功？
				$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
			}
			
			$this -> item_class -> createTime = date("Y-m-d H:i:s");
			$this -> item_class -> updateTime = date("Y-m-d H:i:s");
			
			// 上一層有分類
			if($this -> item_class -> parentId != ""){
				// 取得父分類
				$parent_class = $this -> item_class -> get_parent();
				// 將項目新增在父分類上
				$parent_class -> add_class($this -> item_class);
			}
			
			// 此模組是沒有分類的，將新增的項目掛在模組上
			else{
				$this -> module -> add_class($this -> item_class);
			}
			
			$this -> module_showbox -> set_message("新增分類成功!");
			$this -> module_go -> back();
		}
	}
	
	/**
	 * 取消
	 */
	public function cancel(){
		$this -> module_go -> back();
	}
}
?>