<?php
class CheckinSelect_Component extends Component {

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
		
		$select = "<select name=\"{$this -> variable}\" {$str_attribute} >";
		foreach($this -> datas as $data){
			$selected = ($value == $data[0]) ? "selected" : "";
			$select .= "<option value=\"{$data[0]}\" {$selected}>{$data[1]}</option>";
		}
		$select .= "</select>";
		
		return "{$tip}<br/>{$select}{$invalid_string}";
	}

	public function get_title(){
		$value = $this -> get_value();
		return "<select class='checkinselect' id='" . $this -> item -> id . "'>
			<option value='0' " . (($value == 0) ? 'selected' : '') . ">未簽到</option>
			<option value='1' " . (($value == 1) ? 'selected' : '') . ">簽到</option>
		</select>";
	}

	public function get_text(){
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