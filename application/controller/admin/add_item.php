<?php
class Add_Item_Controller extends AdminController {

	/**
	 * @var 模組物件
	 */
	public $module = array();

	/**
	 * @var 項目
	 */
	public $item = array();
	
	/**
	 * @var 自訂按鈕
	 */
	protected $buttons = array();

	public function main() {
		
		// 取得模組代碼 並 取得模組元件
		$module_code = $this -> module_io -> mod;
		$this -> module = $this -> module_dao -> get_object("module");
		$this -> module -> get_module($module_code);
		
		// 載入項目資訊
		$this -> load_item();
		
		// 自訂按鈕功能
		$this -> load_custom_button();
	}
	
	/**
	 * 載入項目資訊
	 */
	private function load_item() {
		// 找不到就會產生一個新的(空的)項目物件
		$this -> item = $this -> module -> get_empty_item();
	}

	/**
	 * 自訂按鈕功能
	 */	
	private function load_custom_button(){
		
		$this -> buttons = $this -> module -> fieldButton;
		if(is_null($this -> buttons) || $this -> buttons == ""){
			$this -> buttons = array();
			return;
		}
		
		
		if(is_array($this -> module -> fieldButton)){
			$this -> buttons = $this -> module -> fieldButton;
		}
		else{
			$this -> buttons = json_decode($this -> module -> fieldButton);
		}
		
	}

	/**
	 * 儲存
	 */
	public function save() {
		// 欄位驗證是否過關
		$valid_success = true;
		
		// 開始儲值輸入值至元件
		foreach($this -> module -> fieldMetadata as $fieldMetadata){
			
			if(in_array($fieldMetadata -> fieldMetadata_field_type, array("Image","File"))){
				continue;
			}
			
			$field = $this -> item -> {$fieldMetadata -> fieldMetadata_field_variable};
			$value = $this -> module_io -> {$fieldMetadata -> fieldMetadata_field_variable};
			// 把輸入值設定至元件
			$field -> set_value( $value );
			
			// 到目前為止是否所有欄位都驗證成功？
			$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
		}
		
		
		
		
		
		// 驗證成功 (儲存)
		if($valid_success){
			
			foreach($this -> module -> fieldMetadata as $fieldMetadata){
			
				if(!in_array($fieldMetadata -> fieldMetadata_field_type, array("Image","File"))){
					continue;
				}
				
				$field = $this -> item -> {$fieldMetadata -> fieldMetadata_field_variable};
				$value = $this -> module_io -> {$fieldMetadata -> fieldMetadata_field_variable};
				// 把輸入值設定至元件
				$field -> set_value( $value );
				
				// 到目前為止是否所有欄位都驗證成功？
				//$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
			}
			
			// 上一層有分類
			if($this -> module_io -> id != ""){
				// 取得父分類
				$parent_class = $this -> module -> get_single_class(array("id=?"),array($this -> module_io -> id));
				// 將項目新增在父分類上
				$parent_class -> add_item($this -> item);
			}
			
			// 此模組是沒有分類的，將新增的項目掛在模組上
			else{
				$this -> module -> add_item($this -> item);
			}
			
			$this -> module_showbox -> set_message("新增項目成功!");
			$this -> module_go -> back();
		}
	}
}

?>