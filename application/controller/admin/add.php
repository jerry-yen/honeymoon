<?php
class Add_Controller extends AdminController {

	public function main() {
			
				$this -> is_special_level = true;
			
				
				if(in_array($fieldMetadata -> class_special_fieldMetadata_field_type, array("Image","File"))){
					continue;
				}
				
				
				if(in_array($fieldMetadata -> class_fieldMetadata_field_type, array("Image","File"))){
					continue;
				}
				
			
			
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
			
			
			
}
