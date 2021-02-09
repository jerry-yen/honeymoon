<?php
class Checkin_Button_Component extends Component{
	
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
				
                return "{$tip}<br/><input type=\"button\" name=\"{$this -> variable}\" value=\"打卡明細\" {$str_attribute}/>{$invalid_string}";
        }
		
		public function get_title(){
			$this -> script();
			return "<input type=\"button\" name=\"{$this -> variable}\" rel=\"{$this -> item -> id}\" value=\"打卡明細\" class=\"btn btn-primary detail\" style=\"background-color: #f39c12;border-color: #f39c12\" />";
		}
		
		public function script(){
		
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/tinymce-4.2.5/tinymce.min.js"></script>

<?php
		}
?>
<script type="text/javascript">
$(document).ready(function(){
	$(".detail").click(function(){
		var id = $(this).attr("rel");
		location.href='list_item.php?mod=checkin&mid=' + id;
	});
});
</script>
<?php
}
}
?>