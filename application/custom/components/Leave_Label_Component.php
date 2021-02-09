<?php
class Leave_label_Component extends Component {

	private $datas = array();

	public function render($attributes = array()) {

		// 驗證失敗呈現
		$invalid_string = "";
		if($this -> valid_error_message != ""){
			$attributes["class"] = (isset($attributes["class"])) ? ($attributes["class"] . " invalid") : "invalid";
					
			$invalid_string = "<span class='invalid_message'>{$this -> valid_error_message}</span>";
		}
				
				
		// 屬性
		$str_attribute = "";
        foreach($attributes as $name => $attr_value){
        	$str_attribute .= $name . '="' . $attr_value . '" ';
		}
        $id = $this -> get_value();
        $value = $this -> get_title();
							
        return "<br/><span {$str_attribute} style=\"height:auto;\">{$value}<span><input type=\"hidden\" name=\"{$this -> variable}\" value=\"{$id}\" />";
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

}
?>