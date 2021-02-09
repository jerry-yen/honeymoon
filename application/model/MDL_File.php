<?php
require_once(__DIR__ . "/MDL_FieldValueHandler.php");

class MDL_File extends MDL_FieldValueHandler {
	
	
	public function get_file_path($width = 0, $height = 0){
		$controller = Base_Controller::get_instance();
		
		$file_path = $controller -> module_image -> get_full_path($this -> data["path"], $width, $height);
		
		if(file_exists($file_path) && is_file($file_path)){
			$file_path = $controller -> module_image -> get_relative_path($this -> data["path"], $width,$height);
		}
		else{
			$file_path = $controller -> module_image -> get_relative_path($this -> data["path"], 0,0);
		}
		
		if($file_path == $controller -> config_machine_relative_full_upload_path . "/"){
			return "";
		}
		
		return str_replace("//","/",$file_path);
    }
	
	public function delete(){
		// 刪除相關關連
		
		// 刪除相關圖片
		
		// 刪除相關檔案
		
		parent::delete();
	}
}
?>