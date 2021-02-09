<?php
class FieldsPrice2_Component extends Component {
	
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
		
		
		$render = "{$tip} <div id='{$this -> variable}_field_add' style='float:right;margin-bottom:5px;'><button class=\"btn btn-success glyphicon glyphicon-plus\" type='button' title=\"新增欄位\"></button></div><br />";
		$render .= '<div class="box-body table-responsive no-padding">
						<table class="table table-hover">
							
							<tr>
 								<th>&nbsp;</th>
								<th>規格</th>
								<th>價格</th>
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
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>';
		$render .=           	'<td><input type="text" class="form-control" name="' . $this -> variable . '_field_spec[]" value="' . $fieldMetadata -> {$this -> variable . "_field_spec"} . '"></td>';
		$render .=             	'<td><input type="text" class="form-control" name="' . $this -> variable . '_field_price[]" value="' . $fieldMetadata -> {$this -> variable . "_field_price"} . '"></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}

	private function get_empty_row_object(){
		$render ='			<tr class="' . $this -> variable . '_empty" style="display:none;">
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_spec[]" value=""></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_price[]" value=""></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}
	
	public function get_title(){
		$value = $this -> get_value();
		if($value == ""){
			$value = array();
		}
		$prices = ( is_array($value) || is_object($value)) ? $value : json_decode($value);
		
		$price_fiels_name = $this -> variable . "_field_price";

		if(count($prices) == 0){
			return "$0";
		}
		else if(count($prices) == 1){
			return "$" . $prices[0] -> $price_fiels_name;
		}
		else{
			$max = 0;
			$min = 100000000;
			foreach($prices as $price){
				if($price -> $price_fiels_name > $max){
					$max = $price -> $price_fiels_name;
				}
				
				if($price -> $price_fiels_name < $min){
					$min = $price -> $price_fiels_name;
				}
			}
			
			return "$" . $min . " ~ $" . $max;
		}
	}
	
	public function set_value( $value ){
		
		
		$field_spec = $this -> variable . "_field_spec";
		$field_price = $this -> variable . "_field_price";
		
		
		$price_count = count($this -> controller -> module_io -> {$field_price});


		$fields = array();
		for($i=0;$i<$price_count;$i++){
			
			if($this -> controller -> module_io -> {$field_price}[$i] == ""){
				continue;
			}
			
			$fields[] = array(
				$field_spec => $this -> controller -> module_io -> {$field_spec}[$i],
				$field_price => $this -> controller -> module_io -> {$field_price}[$i],
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