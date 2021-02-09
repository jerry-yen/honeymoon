<?php
class Radio_Component extends Component {
	
	private $datas = array();

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
		
		$value = $this -> get_value();
		
		
		// 資料來源
		$this -> datas = ($this -> datas == array()) ? $this -> get_data_source() : $this -> datas;

		$source_data = array();

		$render = "{$tip}<br/>";
		foreach($this -> datas as $data){
			$source_data[] = $data[0];
			$checked = ($value == $data[0]) ? "checked" : "";
			$render .= "<input type=\"radio\" name=\"{$this -> variable}\" value=\"{$data[0]}\" {$checked}/> {$data[1]}　";
		}

		// 其他選填項
		if (preg_match("/other{(.*?)};/", $this -> element, $element)) {
			$text = $element[1];
			$text_value = "";
			$checked = "";

			

			if(!in_array($value,$source_data)){
				$text_value = $value;
				$v = explode("#",$value);
				$checked = (count($v) == 2 && $v[0] == "other") ? "checked" : "";
				$value = (count($v) == 2 && $v[0] == "other") ? $v[1] : "";
			}
			else{
				$checked = "";
				$value = "";
			}
			
			$render .= "<input type=\"radio\" name=\"{$this -> variable}\" value=\"other\" {$checked}/>{$text} <input type=\"text\" name=\"{$this -> variable}_other\" value=\"{$value}\" {$str_attribute} style=\"display:inline; width:80px; border:0px; border-bottom:1px dotted gray; \" />";
			
		}

		return $render;
	}
	
	
	public function get_title(){
		$value = $this -> get_value();
		$this -> datas = ($this -> datas == array()) ? $this -> get_data_source() : $this -> datas;
		
		foreach($this -> datas as $data){
			if($value == $data[0]){
				return $data[1];
			}
		}
	}
	
	public function set_value( $value ){
		if($value == "other"){
			$variable = $this -> variable . "_other";
			$value = "other#" . $this -> controller -> module_io -> {$variable};
		}
		
		parent::set_value($value);
	}
	
}
?>