<?php
class Fix_Controller extends AdminController {

	/**
	 * @var 模組物件
	 */
	public $module = array();
	
	/**
	 * @var 分類
	 */
	public $item_class = array();
	
	/**
	 * @var 是否是特殊分類層
	 */
	protected $is_special_level = false;
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

		if($this -> item_class -> level == $this -> module -> class_field_use_level){
			$this -> item_class = $this -> module -> get_single_class(array("id=?"),array($this -> module_io -> id),array(),true);
			$this -> module -> class_fieldMetadata = $this -> module -> class_special_fieldMetadata;
			$this -> is_special_level = true;
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
			$this -> item_class -> update();
			
			$this -> module_showbox -> set_message("修改 {$this -> item_class -> title} 成功!");
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
			if($this -> is_special_level){
				
				if(in_array($fieldMetadata -> class_special_fieldMetadata_field_type, array("Image","File"))){
					continue;
				}
				
				$field = $this -> item_class -> {$fieldMetadata -> class_special_fieldMetadata_field_variable};
				$value = $this -> module_io -> {$fieldMetadata -> class_special_fieldMetadata_field_variable};
			}
			else{
				
				if(in_array($fieldMetadata -> class_fieldMetadata_field_type, array("Image","File"))){
					continue;
				}
				
				$field = $this -> item_class -> {$fieldMetadata -> class_fieldMetadata_field_variable};
				$value = $this -> module_io -> {$fieldMetadata -> class_fieldMetadata_field_variable};
			}
			// 把輸入值設定至元件
			$field -> set_value( $value );
			
			// 到目前為止是否所有欄位都驗證成功？
			$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
		}
		
		// 驗證成功 (儲存)
		if($valid_success){
			
			foreach($this -> module -> class_fieldMetadata as $fieldMetadata){
			
				if($this -> is_special_level){
					
					if(!in_array($fieldMetadata -> class_special_fieldMetadata_field_type, array("Image","File"))){
						continue;
					}
					
					$field = $this -> item_class -> {$fieldMetadata -> class_special_fieldMetadata_field_variable};
					$value = $this -> module_io -> {$fieldMetadata -> class_special_fieldMetadata_field_variable};
				}
				else{
					
					if(!in_array($fieldMetadata -> class_fieldMetadata_field_type, array("Image","File"))){
						continue;
					}
					
					$field = $this -> item_class -> {$fieldMetadata -> class_fieldMetadata_field_variable};
					$value = $this -> module_io -> {$fieldMetadata -> class_fieldMetadata_field_variable};
				}
				
				// 把輸入值設定至元件
				$field -> set_value( $value );
				
				// 到目前為止是否所有欄位都驗證成功？
				//$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
			}
			
			$this -> item_class -> update();
			
			$this -> module_showbox -> set_message("修改分類成功!");
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