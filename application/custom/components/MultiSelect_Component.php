<?php
class MultiSelect_Component extends Component {
	
	private static $include_scripts = false;
	
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
		$value = json_decode($value);
		if($value == "" || $value == "null") $value = array();
		
		// 資料來源
		$this -> datas = ($this -> datas == array()) ? $this -> get_data_source() : $this -> datas;
		$select = "<select name=\"{$this -> variable}[]\" {$str_attribute} multiple=\"multiple\" >";
		foreach($this -> datas as $data){
			$selected = (in_array($data[0],$value)) ? "selected" : "";
			$select .= "<option value=\"{$data[0]}\" {$selected}>{$data[1]}</option>";
		}
		$select .= "</select>";
		

		return "{$tip}<br/>{$select}{$invalid_string}";
	}

	public function set_value($value){
		$this -> value = json_encode($value);		
	}

	public function get_data(){
		return $this -> get_data_source();
	}

	public function get_title(){
		$values = $this -> get_value();
		$values = json_decode($values, true);
		$titles = array();
		$data = $this -> get_data_source();
		foreach($values as $value){
			if(isset($data[$value])){
				$titles[] = '<div class="label bg-blue" style="margin:3px;">' . $data[$value][1] . '</div>';
			}
		}

		return implode('', $titles);
	}
	
	
	
	public function script(){
		
		
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.sumoselect/jquery.sumoselect.min.js"></script>
<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.sumoselect/sumoselect.css" rel="stylesheet" rel="stylesheet" type="text/css"/>
<?php
		}
		 
		
		 
?>
<script type="text/javascript">
	$(document).ready(function(){
		var component = $("select[name='<?php echo $this -> variable; ?>[]']");
		component.SumoSelect();
		$(".SumoSelect > .CaptionCont").css("width","500px");
	});
</script>
<?php
	}
}
?>