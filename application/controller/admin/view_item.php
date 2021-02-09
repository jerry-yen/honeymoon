<?php
class View_Item_Controller extends AdminController {

	/**
	 * @var 模組物件
	 */
	public $module = array();
	
	/**
	 * @var 項目
	 */
	protected $item = array();
	
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
		$this -> load_custom_button();		}

	/**
	 * 載入項目資訊
	 */
	private function load_item() {

		// 如為設定頁面
		if($this -> module -> single == "Y"){
			$this -> item = $this -> module -> get_single_item();
			if(!$this -> item -> is_exists()){
				$this -> module -> add_item($this -> item);
			}
		}
		
		// 清單項目的修改項目功能
		else{
			$this -> item = $this -> module -> get_single_item(array("id=?"),array($this -> module_io -> id));
			if(!$this -> item -> is_exists()){
				$this -> module_showbox -> set_message("查無此項目");
				$this -> module_go -> back();
			}
		}
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
			
			if(in_array($fieldMetadata -> fieldMetadata_field_type, array("Image","File","Cart"))){
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
				$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
			}
			
			$this -> item -> update();
			
			$this -> module_showbox -> set_message("修改項目成功!");
			$this -> module_go -> back();
		}
	}
	
	/**
	 * 修改密碼頁面專用
	 */
	public function fix_password(){
		$valid_success = true;
		foreach($this -> module -> fieldMetadata as $fieldMetadata){
				
			$field = $this -> item -> {$fieldMetadata -> fieldMetadata_field_variable};
			$value = $this -> module_io -> {$fieldMetadata -> fieldMetadata_field_variable};
		
			// 把輸入值設定至元件
			$field -> set_value( $value );
				
			// 到目前為止是否所有欄位都驗證成功？
			$valid_success = $valid_success & ( $field -> get_valid_error_message() == "" );
		}	
		
		if($valid_success){
			$old_password = $this -> item -> old_password;
			$new_password = $this -> item -> new_password;
			$confirm = $this -> item -> confirm;
			
			if($new_password -> get_value() != $confirm -> get_value() ){
				$this -> module_showbox -> set_message( $new_password -> get_name() . "與" . $confirm -> get_name() . "不相符!");
				return;
			}
			
			// 修改預設帳號未來如果修改的是其他帳號，可替換 $this -> mod_login 變數
			$update_object = $this -> mod_login;
			
			if( $update_object -> password != $old_password -> get_value()){
				$this -> module_showbox -> set_message( $old_password -> get_name() . "錯誤!");
				return;
			}
			
			$update_object -> password = $new_password -> get_value();
			$update_object -> update();
			
			$this -> module_showbox -> set_message( "密碼修改成功!");
			
		}
	}

	public function import(){
		
		$module = $this -> module_dao -> get_object("module");
		$module -> get_module("title");
		
		$titles = explode("\r\n",$this -> module_io -> content);
		foreach($titles as $title){
			if(trim($title) == ""){
				continue;
			}
			$item = $module -> get_single_item(array("title=?"),array($title));
			if(!$item -> is_exists()){
				$item -> title = $title;
				$module -> add_item($item);
			}	
		}
	}
}
?>