<?php
class Work_Time_Component extends Component{
	
	private static $include_scripts = false;

        public function render($attributes = array()){
                
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
                
                // 提示
                $tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span>") : "";
                $value = $this -> get_value();
				
				// 如為空值則代入預設值
				if(trim($value) == ""){
					$value = $this -> get_default();
				}
				
                // return "{$tip}<br/><input type=\"email\" name=\"{$this -> variable}\" value=\"{$value}\" {$str_attribute}/>{$invalid_string}";
                $days = array(" ","星期一","星期二","星期三","星期四","星期五","星期六","星期日");
				$funs = array(
					array("name" => "", "variable" => "is_working", "type" => "switch", "align" => "center"),
					array("name" => "上班時間", "variable" => "work_time", "type" => "timepicker", "align" => "center"),
					array("name" => "午休開始時間", "variable" => "break_time_start", "type" => "timepicker", "align" => "center"),
					array("name" => "午休結束時間", "variable" => "break_time_end", "type" => "timepicker", "align" => "center"),
					array("name" => "下班時間", "variable" => "leave_time", "type" => "timepicker", "align" => "center"),
					array("name" => "一日上班時數(準備廢除)", "variable" => "work_timer", "type" => "number", "align" => "center"),
					array("name" => "遲到/加班彈性時間", "variable" => "delay_time", "type" => "number", "align" => "center")
				);
				
                $component = "<div style='width:100%;overflow-x:scroll;'>
                				<table class=\"table table-bordered\" style=\"width:1300px;\">";
								
				// 星期(標題)
                $component .= "	<tr>";
				foreach($days as $key => $day){
                	$component .= "<td style=\"text-align:center;font-weight:bold;\">{$day}</td>";
					
				}
                $component .= "	</tr>";
				$value_set = $this -> get_value();
				$value_set = json_decode($value_set);
				
				foreach($funs as $fun){
	                $component .= "	<tr>";
					foreach($days as $key => $day){
						if($key == 0){
							$component .= "<td style='padding:2px 5px;text-align:right;font-weight:bold;'>{$fun["name"]}</td>";
						}
						else{
							$input_type = "text";
							$var_name = $fun["variable"] . "_" . $key;
							$value = "value=\"" . $value_set -> {$var_name} . "\"";
							$checked = "";
							if($fun["type"] == "number"){
								$input_type = "number";
							}
							if($fun["type"] == "switch"){
								$input_type = "checkbox";
								$value = "value=\"Y\"";
								$checked = ($value_set -> {$var_name} == "Y") ? "checked":"";
							}
							
							
		                	$component .= "<td style='padding:5px 5px;text-align:center;'><input type=\"{$input_type}\" class=\"{$fun["type"]} form-control\" name=\"{$var_name}\" {$value} {$checked} style=\"width:100%;\"></td>";
						}
					}
	                $component .= "	</tr>";
				}
				
                $component .= "</table>
                		</div>";
						
				return $component;
                
        }

		public function set_value($value){
			
			$days = array(" ","星期一","星期二","星期三","星期四","星期五","星期六","星期日");
			$funs = array(
				array("name" => "", "variable" => "is_working", "type" => "switch", "align" => "center"),
				array("name" => "上班開始時間", "variable" => "work_time", "type" => "timepicker", "align" => "center"),
				array("name" => "午休開始時間", "variable" => "break_time_start", "type" => "timepicker", "align" => "center"),
				array("name" => "午休結束時間", "variable" => "break_time_end", "type" => "timepicker", "align" => "center"),
				array("name" => "下班時間", "variable" => "leave_time", "type" => "timepicker", "align" => "center"),
				array("name" => "一日上班時數(準備廢除)", "variable" => "work_timer", "type" => "number", "align" => "center"),
				array("name" => "遲到/加班彈性時間", "variable" => "delay_time", "type" => "number", "align" => "center")
			);
			
			$results = array();
			
			foreach($funs as $fun){
				foreach($days as $key => $day){
					if($key > 0){
						$var_name = $fun["variable"] . "_" . $key;
						$results[$var_name] = $this -> controller -> module_io -> {$var_name};
					}
				}
			}
			
			$this -> value = json_encode($results);
			
		}

		public function script(){
                
                if( !self::$include_scripts ){
                        self::$include_scripts = true;
?>

<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>

<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/switch-button/jquery.switchButton.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/switch-button/jquery.switchButton.js" type="text/javascript"></script>

<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.datetimepicker/jquery.datetimepicker.full.min.js"></script>

<?php
                }
?>
<script>
        $(document).ready(function(){
        		
				$.datetimepicker.setLocale('ch');
                $(".timepicker").datetimepicker({
                	datepicker:false,
      				format:'H:i',
                	step: 30
                });
                
                var component = $(".switch");
				component.switchButton({
					on_label: '上班',
		          	off_label: '放假',
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