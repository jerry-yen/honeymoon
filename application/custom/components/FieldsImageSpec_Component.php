<?php
class FieldsImageSpec_Component extends Component {
	
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
								<th>材質圖片</th>
								<th>尺寸(多尺寸請依半型逗號隔開)</th>
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
        if($fieldMetadata -> {$this -> variable . "_field_image"} == ""){
        	$render .=           	'<td><input type="file" class="form-control" name="' . $this -> variable . '_field_image[]" value="' . $fieldMetadata -> {$this -> variable . "_field_image"} . '"></td>';
        }                   
		else{
			$render .=           	'<td><img src="' . $this -> config["machine_relative_full_upload_path"] . $fieldMetadata -> {$this -> variable . "_field_image"} . '" width="30" height="30"><input type="hidden" name="' . $this -> variable . '_field_image[]" value="' . $fieldMetadata -> {$this -> variable . "_field_image"} . '"></td>';
		}
		
        $render .=             	'<td><input type="text" class="form-control" name="' . $this -> variable . '_field_spec[]" value="' . $fieldMetadata -> {$this -> variable . "_field_spec"} . '"></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}

	private function get_empty_row_object(){
		$render ='			<tr class="' . $this -> variable . '_empty" style="display:none;">
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>
                            	<td><input type="file" class="form-control" name="' . $this -> variable . '_field_image[]" value=""></td>
                            	<td><input type="text" class="form-control" name="' . $this -> variable . '_field_spec[]" value=""></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}
	
	public function get_title(){
		return strip_tags($this -> get_value());
	}
	
	public function set_value( $value ){
		
		$field_spec = $this -> variable . "_field_spec";
		$field_image = $this -> variable . "_field_image";
		
		$files = $this -> upload();
		
		$spec_count = count($this -> controller -> module_io -> {$field_spec});
		$image_count = count($this -> controller -> module_io -> {$field_image});
		$files_count = count($files);

		$fields = array();
		for($i=0;$i<$image_count;$i++){
			$fields[] = array(
				$field_image => $this -> controller -> module_io -> {$field_image}[$i],
				$field_spec => $this -> controller -> module_io -> {$field_spec}[$i],
			);
		}

		for($i=0;$i<$files_count-1;$i++){
			$fields[] = array(
				$field_image => $files[$i],
				$field_spec => $this -> controller -> module_io -> {$field_spec}[$image_count + $i],
			);
		}
		
		// 與舊資料比對，舊資料沒出現在新陣列代表要刪除了該實體圖片
		$value = $this -> get_value();
		if($value == ""){
			$value = array();
		}
		$fieldMetadatas = ( is_array($value) || is_object($value)) ? $value : json_decode($value);
		
		foreach($fieldMetadatas as $fieldMetadata){
			$flag = false;
			foreach($fields as $field){
				if( $fieldMetadata -> {$field_image} == $field[$field_image] ){
					$flag = true;
					break;
				} 
			}
			
			if(!$flag){
				unlink($this -> config["full_upload_path"] . $fieldMetadata -> {$field_image});
			}
			
		}
		
		
		$this -> value = json_encode($fields);
		
	}
	
	private function upload(){

		$controller = Base_Controller::get_instance();

		// 篩選檔案類型
		$allow_types = array();
		if(preg_match("/allow{(.*?)};/", $this -> element, $element)){
			$allow_types = explode(",",$element[1]);
		}
		
		// 上傳檔案
		$files = $controller -> module_file -> upload($this -> variable . "_field_image", $allow_types);
		$new_files = array();
		// 將檔案資訊儲存至資料庫
		foreach($files as $file){
			$new_files[] = $file -> path;
		}
		
		return $new_files;
		
		
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