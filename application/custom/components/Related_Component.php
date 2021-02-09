<?php
class Related_Component extends Component {
	
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
		if($value == ""){
			$value = array();
		}
		$fieldMetadatas = ( is_array($value) || is_object($value)) ? $value : json_decode($value);
		
        //$fieldMetadatas = ($value == "" || $value == array()) ? array() : json_decode($value);
		
		include($this -> config["full_application_path"] . "/custom/config/components.php");
		
		$render = "{$tip} <div id='{$this -> variable}_field_add' style='float:right;margin-bottom:5px;'><button class=\"btn btn-success glyphicon glyphicon-plus\" type='button' title=\"新增欄位\"></button></div><br />";
		$render .= '<div class="box-body table-responsive no-padding">
						<table class="table table-hover">
							
							<tr>
 								<th>&nbsp;</th>
								<th>產品名稱</th>
								<th>Title</th>
								<th>產品圖片</th>
								<th>指令</th>
							</tr>
							';
		$render .='<tbody class="' . $this -> variable . '_items">';
		$render .= $this -> get_row_object($fieldMetadata);
			
		
		$render .='</tbody>';                            
		$render .='</table>
					</div>';
		
        return $render;
		

		
	}

	private function get_row_object($fieldMetadata){
		
		$ids = $this -> get_value();
		
		$module_action = $this -> controller -> module_dao -> get_object("module");
		$module_action -> get_module("product");
		$render = "";
		foreach($ids as $id){
			$product = $module_action -> get_single_item(array("id=?"),array($id));
			$render .= '			<tr class="' . $this -> variable . '_item">
	                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>';
	        $render .=           	'<td>' . $product -> title -> get_value() . '</td>';
			$render .=           	'<td>' . $product -> en_title -> get_value() . '</td>';
			$render .=             	'<td><img src="' . $product -> cover -> get_image() -> get_file_path() . '" width="150" /></td>
	                                <td>
	                                	<button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button>
	                                	<input type="hidden" name="' . $this -> variable . '_id[]" value="' . $product -> id . '">
	                                </td>
	                            </tr>';
		}
		// 
		
		
		return $render;
	}
	
	public function get_title(){
		return strip_tags($this -> get_value());
	}
	
	public function set_value( $value ){
		
		$field_id = $this -> variable . "_id";
		$results = $this -> controller -> module_io -> {$field_id};
		$results = (is_array($results)) ? $results : array();

		$this -> value = json_encode($results);
		
	}
	
	
	
	public function script(){
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/colorbox/colorbox.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>
<?php
}
?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".<?php echo $this -> variable?>_items").sortable({
			handle : ".dropable"
		});
		
		$("#<?php echo $this -> variable; ?>_field_add").click(function(){

			$("#<?php echo $this -> variable; ?>_field_add").colorbox({
				href: "related.php?var=<?php echo $this -> variable; ?>",
				iframe : true,
				width: "50%", 
				height: "600"
			});
			
			$(".<?php echo $this -> variable?>_items").sortable('disable');
			
			$(".delete").unbind("click");
			$(".delete").click(function(){
				$(this).parents(".<?php echo $this -> variable?>_item").remove();  
			});
		});
		
		$(".delete").click(function(){
			$(this).parents(".<?php echo $this -> variable?>_item").remove();
		});
	}); 
</script>
<?php
}
}
?>