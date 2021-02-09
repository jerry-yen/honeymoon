<?php
class Landing_Page_Template_Component extends Component {

	private $datas = array();
	
	private static $include_scripts = false;

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
		
		$select = "<iframe width=\"100%\" height=\"500\" frameborder=\"0\" src=\"http://11/preview.php?id=\"></iframe>";
		

		return "{$tip}<br/>{$select}{$invalid_string}";
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
	
	public function get_data_source(){
		$datas = array();
		$dirs = scandir($this -> config["full_application_path"] . "/custom/template");
		foreach($dirs as $dir){
			if(in_array($dir, array(".",".."))){
				continue;
			}
			$datas[] = array($dir,$dir);
		}
		
		return $datas;
		
	}
	
	public function script(){
?>
<script type="text/javascript">
	$("select[name='<?php echo $this -> variable; ?>']").change(function(){
		var value = $(this).val();
		if(value != ""){
			$.get("<?php echo $this -> config["machine_relative_application_path"]; ?>/custom/template/" + value + "/index.php", function(result){
				tinymce.activeEditor.setContent(result);
				console.log(result)
			});
		}
	});
		
</script>
<?php
	}

}
?>