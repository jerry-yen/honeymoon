<?php
class FieldsCourse_Component extends Component {
	
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
		

		$module_action = $this -> controller -> module_dao -> get_object("module");
		$module_action -> get_module('join_course');
		$items = $module_action -> get_items(array('member_id=?'), array($this -> item -> id));
		
		// 提示
		$tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span>") : "";
        
		$render = '<div class="box-body table-responsive no-padding">
						<table class="table table-hover">
							
							<tr>
								<th>課程名稱</th>
								<th>報名日期</th>
								<th>報名狀態</th>
							</tr>
							';
		$render .='<tbody class="' . $this -> variable . '_items">';

		foreach($items as $item){
			$render .= $this -> get_row_object($item);
		}
		
		
		$render .='</tbody>';                            
		$render .='</table>
					</div><!-- /.box-body -->';
		
        return $render;
		

		
	}

	private function get_row_object($item){

		$module_action = $this -> controller -> module_dao -> get_object("module");
		$module_action -> get_module('course');
		$course = $module_action -> get_single_item(array('id=?'), array($item -> course_id -> get_value()));

		$render ='			<tr class="' . $this -> variable . '_item">
                            	
                            	<td>' . $course -> title -> get_value() . '</td>
                            	<td>' . $item -> join_datetime -> get_value() . '</td>
                            	
								<td>
									<select name="' . $this -> variable . '_status[' . $item -> id . ']">
										<option value="0" ' . (($item -> status -> get_value() == 0) ? 'selected' : '') . '>審核中</option>
										<option value="1" ' . (($item -> status -> get_value() == 1) ? 'selected' : '') . '>報名成功</option>
										<option value="2" ' . (($item -> status -> get_value() == 2) ? 'selected' : '') . '>備取</option>
										<option value="3" ' . (($item -> status -> get_value() == 3) ? 'selected' : '') . '>取消報名</option>
									</select>
								</td>
                            </tr>';
		return $render;
	}

	public function get_title(){
		return strip_tags($this -> get_value());
	}
	
	public function set_value($value){
		$field_status = $this -> variable . "_status";
		$datas = $this -> controller -> module_io -> {$field_status};
		$count = count($datas);
		
		foreach($datas as $key => $data){
			$module_action = $this -> controller -> module_dao -> get_object("module");
			$module_action -> get_module('join_course');
			$course = $module_action -> get_single_item(array('id=?'), array($key));
			if(!$course -> is_exists()){
				continue;
			}
			$course -> status = $data;
			$course -> update();
		}
		
		$this -> value = '';
		
	}
	
	public function alter_table($value){
		$field_name = $this -> variable . "_field_name";
		$field_variable = $this -> variable . "_field_variable";
		$field_type = $this -> variable . "_field_type";
		$field_element = $this -> variable . "_field_element";
		$field_default = $this -> variable . "_field_default";
		$field_tip = $this -> variable . "_field_tip";
		$field_list = $this -> variable . "_field_list";
		
		
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
				$field_element => $this -> controller -> module_io -> {$field_element}[$i],
				$field_default => $this -> controller -> module_io -> {$field_default}[$i],
				$field_tip => $this -> controller -> module_io -> {$field_tip}[$i],
				$field_list => $this -> controller -> module_io -> {$field_list}[$i]
			);
		}
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
			
			$(".field_list").unbind("click");
			$(".field_list").click(function(){
				var value = $(this).is(':checked')?"Y":"N";
				$(this).parents("td").children("input[type='hidden']").attr("value",value);
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