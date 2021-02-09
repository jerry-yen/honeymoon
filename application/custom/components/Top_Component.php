<?php
class Top_Component extends Component {
	
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
		
		// 取得資料集
		$this -> get_datas();
		if(count($this -> datas) != 2){
			return "資料集錯誤";	
		}
		
		// 提示
		$tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span>") : "";
		$value = $this -> get_value();
		
		// 如為空值則代入預設值
		if (is_null($value) || $value == "" || $value == "0000-00-00 00:00:00") {
			$value = date("Y-m-d H:i:s");
			$checked = "";
		}
		else{
			$checked = ($value != "") ? "checked" : "";
		}
		
		$render = "{$tip}<br/><input type=\"checkbox\" name=\"{$this -> variable}\" value=\"{$value}\" {$checked}/>";
		

		return $render;
	}
	
	public function set_value( $value ){
		// $this -> value = ($value == "" || $value == "0000-00-00 00:00:00") ? "" : $value;
		$this -> value = ($value == "") ? "0000-00-00 00:00:00" : $value;
	}
	
	public function get_title(){
		
		$value = $this -> get_value();
		$this -> get_datas();
		
		if(count($this -> datas) != 2){
			return "資料集錯誤";	
		}
		
		return ($value != "" && $value != "0000-00-00 00:00:00") ? $this -> datas[0] : $this -> datas[1];
	}
	
	/**
	 * 取得資料集
	 */
	private function get_datas(){
		
		// 已取過就不重複讀取
		if($this -> datas != array()){
			return;
		}
		
		if (preg_match("/data-source{(.*?)};/", $this -> element, $element)) {
			$source = $element[1];
			$this -> datas = explode(",",$source);
			
		}
		
	}
	
	public function script(){
		
		// 取得資料集
		$this -> get_datas();
		if(count($this -> datas) != 2){
			return ;	
		}
		
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>

<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/switch-button/jquery.switchButton.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/switch-button/jquery.switchButton.js" type="text/javascript"></script>
<?php
		}
		 
		
		 
?>
<script type="text/javascript">
	$(document).ready(function(){
		var component = $("input[name='<?php echo $this -> variable; ?>']");
		component.switchButton({
			on_label: '<?php echo $this -> datas[0]; ?>',
          	off_label: '<?php echo $this -> datas[1]; ?>',
          	height:23,
          	width:60,
          	button_width:30
       	});
       	component.siblings("ins").remove();
       	component.parents(".icheckbox_minimal").removeClass("icheckbox_minimal");
	});
</script>
<?php
	}
}
?>