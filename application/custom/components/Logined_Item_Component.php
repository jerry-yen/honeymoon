<?php
class Logined_Item_Component extends Component {

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
		$value = $this -> get_title();
		
		return "{$tip}<br/><input type=\"text\" name=\"{$this -> variable}\" readonly value=\"{$value}\" {$str_attribute}/>{$invalid_string}";
	}
	
	public function set_value($value){
		
		$extend = explode("###",$value);
		if(count($extend) == 2){
			$this -> value = $extend[1];
			return;
		}
		
		@session_start();		
		if($this -> item -> title -> get_value() != ""){
			$this -> value = $this -> item -> title -> get_value();
		}
		else{
			$this -> value = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"];
		}
		@session_write_close();
		
	}
	
	public function get_title(){
		if (preg_match("/field{(.*?)};/", $this -> element, $element)) {
			$param = explode(",",$element[1]);
			$module_name = $param[0];
			$field_name = $param[1];
			@session_start();
			$value = ($this -> value == "")?($_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"]):$this -> value;
			@session_write_close();
			$item = $this -> controller -> get_single_item($module_name, $value);
			return $item -> {$field_name} -> get_value();
		}
		else{
			return "未指定資料連結";
		}
	}
	
}
?>