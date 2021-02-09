<?php
class FieldsButton_Component extends Component {
	
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
		
		
        $fieldMetadatas = (is_string($value)) ? json_decode($value) : $value;
		
		include($this -> config["full_application_path"] . "/custom/config/components.php");
		
		$render = "{$tip} <div id='{$this -> variable}_field_add' style='float:right;margin-bottom:5px;'><button class=\"btn btn-success glyphicon glyphicon-plus\" type='button' title=\"新增欄位\"></button></div><br />";
		$render .= '<div class="box-body table-responsive no-padding">
						<table class="table table-hover">
							
							<tr>
 								<th>&nbsp;</th>
								<th>按鈕名稱</th>
								<th>按鈕變數</th>
								<th>輸入類型</th>
								<th>類別名稱</th>
								<th>指令</th>
							</tr>
							';
		$render .='<tbody class="' . $this -> variable . '_items">';

		foreach($fieldMetadatas as $fieldMetadata){
			$render .= $this -> get_row_object($fieldMetadata);
		}
		$render .= $this -> get_empty_row_object();
		
		
		$render .='</tbody>';                            
		$render .='</table>
					</div>';
		
        return $render;
		

		
	}

	private function get_row_object($fieldMetadata){
		$render ='			<tr class="' . $this -> variable . '_item">
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_name[]" value="' . $fieldMetadata -> {$this -> variable . "_field_name"} . '"></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_variable[]" value="' . $fieldMetadata -> {$this -> variable . "_field_variable"} . '"></td>
                            	<td>
                            		<select class="form-control" name="' . $this -> variable . '_field_type[]">
                            		';
		
									$selected = ("submit" == $fieldMetadata -> {$this -> variable . "_field_type"}) ? "selected" : "";
        							$render .='<option value="submit" ' . $selected . '>送出</option>';
									
									$selected = ("button" == $fieldMetadata -> {$this -> variable . "_field_type"}) ? "selected" : "";
        							$render .='<option value="button" ' . $selected . '>按鈕</option>';
        							
									$selected = ("reset" == $fieldMetadata -> {$this -> variable . "_field_type"}) ? "selected" : "";
        							$render .='<option value="reset" ' . $selected . '>重置</option>';
		
			
		
		
		$render .='                            		
                            		</select>
                            	</td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_class[]" value="' . $fieldMetadata -> {$this -> variable . "_field_class"}. '"></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}

	private function get_empty_row_object(){
		$render ='			<tr class="' . $this -> variable . '_empty" style="display:none;">
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_name[]" value=""></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_variable[]" value=""></td>
                            	<td>
                            		<select class="form-control" name="' . $this -> variable . '_field_type[]">
                            		';
									
        							$render .='<option value="submit">送出</option>';
        							$render .='<option value="button">按鈕</option>';
        							$render .='<option value="reset">重置</option>';
                            		
									
		$render .='                            		
                            		</select>
                            	</td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_class[]" value=""></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}
	
	public function get_title(){
		return strip_tags($this -> get_value());
	}
	
	public function set_value( $value ){
		$field_name = $this -> variable . "_field_name";
		$field_variable = $this -> variable . "_field_variable";
		$field_type = $this -> variable . "_field_type";
		$field_class = $this -> variable . "_field_class";
		
		
		$count = count($this -> controller -> module_io -> {$field_name});
		
		$fields = array();
		for($i=0;$i<$count-1;$i++){
			
			if($this -> controller -> module_io -> {$field_variable}[$i] == ""){
				continue;
			}
			
			$fields[] = array(
				$field_name => $this -> controller -> module_io -> {$field_name}[$i],
				$field_variable => $this -> controller -> module_io -> {$field_variable}[$i],
				$field_type => $this -> controller -> module_io -> {$field_type}[$i],
				$field_class => $this -> controller -> module_io -> {$field_class}[$i]
			);
		}
		
		$this -> value = json_encode($fields);
		
	}
	
	public function script(){
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
<?php
}
?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".<?php echo $this -> variable?>_items").sortable({
			handle : ".dropable"
		});
		
		$("#<?php echo $this -> variable; ?>_field_add").click(function(){
			var new_object = $(".<?php echo $this -> variable?>_empty").clone();
			$(".<?php echo $this -> variable?>_empty").before(new_object);
            new_object.addClass("<?php echo $this -> variable?>_item").removeClass("<?php echo $this -> variable?>_empty");
			new_object.show();
			
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