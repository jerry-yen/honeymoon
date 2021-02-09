<?php
/**
 * 檔案上傳匯入元件
 * @abstract
 */
class Import_Component extends Component{

       public function render($attributes = array()) {
                
                // 驗證失敗呈現
				$invalid_string = "";
				if ($this -> valid_error_message != "") {
					$attributes["class"] = (isset($attributes["class"])) ? ($attributes["class"] . " invalid") : "invalid";
		
					$invalid_string = "<span class='invalid_message'>{$this -> valid_error_message}</span>";
				}
				
				// 屬性
				$str_attribute = "";
				foreach ($attributes as $name => $attr_value) {
					$str_attribute .= $name . '="' . $attr_value . '" ';
				}
                
                // 提示
				$tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span>") : "";
                
               $render = "{$tip}<br /><input class=\"{$this -> variable}\" type=\"file\" name=\"{$this -> variable}\" />";
			return $render;
	}

	public function set_value( $value ){
		
		
		// 沒有這個變數
		if(!isset($_FILES[$this -> variable])){
			return;
		}
		
		//　沒上傳任何檔案
		if($_FILES[$this -> variable]["error"] == 4){
			return;
		}
		
		$controller = Base_Controller::get_instance();
		$module = $controller -> module_dao -> get_object("module");
		$module -> get_module("email_list");
		
		$class_id = $this -> item -> user_class -> get_value();
		$class = $module -> get_single_class(array("id=?"),array($class_id));
		
		
		$path = $_FILES[$this -> variable]["tmp_name"];
		$content = file_get_contents($path);
		$lines = explode("\r\n", $content);
		
		
		
		foreach($lines as $line){
			$line = iconv("big5", "utf-8",$line);
			$fields = explode(",", $line);
			$email = $fields[0];
			$title = $fields[1];
			if(trim($email) == ""){
				continue;
			}
			$user = $module -> get_single_item(array("email=?"), array($email));
			if(!$user -> is_exists()){
				$user -> title = $title;
				$user -> email = $email;
				$class -> add_item($user);
			}
			
		}
		
	}

	
}
?>