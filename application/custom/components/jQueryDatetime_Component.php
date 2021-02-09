<?php
class jQueryDatetime_Component extends Component{
	
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
				
                return "{$tip}<br/><input type=\"text\" name=\"{$this -> variable}\" value=\"{$value}\" {$str_attribute}/>{$invalid_string}";
        }

		public function script(){
                
                if( !self::$include_scripts ){
                        self::$include_scripts = true;
?>

<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.datetimepicker/jquery.datetimepicker.full.min.js"></script>

<?php
                }
?>
<script>
        $(document).ready(function(){
        		
				$.datetimepicker.setLocale('ch');
                $("input[name='<?php echo $this -> variable;?>']").datetimepicker({
                	format:"Y-m-d H:i",
                	step: 30
                });
        });
</script>
<?php
        }
}
?>