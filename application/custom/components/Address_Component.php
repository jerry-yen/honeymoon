<?php
class Address_Component extends Component{

		protected $same_field_name = "";

        public function render($attributes = array()){
                
                // 屬性
                $attribute_string = "";
                foreach($attributes as $name => $attr_value){
                        $attribute_string .= $name . '="' . $attr_value . '" ';
                }
				
				$value = $this -> get_value();
				
				$variable = explode(":", $value);
				for($i=0;$i<4;$i++){
					if(!isset($variable[$i])){
						$variable[$i] = "";
					}
				}
				
                
                // 提示
                $tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span><br/>") : "";
                $render = "";
				
				// same{field:address};
				if(preg_match("/same{field:(.*?)}/",$this -> element,$res)){
					$this -> same_field_name = $res[1];
					$render = "<input type=\"checkbox\" name=\"{$this -> variable}_same\" type=\"Y\"> 同上<br /><br />";
				}
				
				$render .= "<input name=\"{$this -> variable}_zip\" type=\"text\" readonly=\"readonly\" value=\"{$variable[0]}\" style=\"width:10%;display:inline;\" {$attribute_string}>";
				$render .= "<select name=\"{$this -> variable}_county\" data-selected=\"{$variable[1]}\" style=\"width:20%;display:inline;\" {$attribute_string}>
					<option value=\"\"></option>
				</select>";
				$render .= "<select name=\"{$this -> variable}_township\" data-selected=\"{$variable[2]}\" style=\"width:20%;display:inline;\" {$attribute_string}>
					<option value=\"\"></option>
				</select>";
				$render .= "<input type=\"text\" style=\"width:50%;display:inline;\" name=\"{$this -> variable}_address\" value=\"{$variable[3]}\" {$attribute_string}/>";
				
                return "{$tip}<br />{$render}";
        }

		public function set_value( $value ){
        		$zip = $this -> variable . "_zip";
				$county = $this -> variable . "_county";
				$township = $this -> variable . "_township";
				$address = $this -> variable . "_address";
				
				$variable[] = $this -> controller -> module_io -> {$zip};
				$variable[] = $this -> controller -> module_io -> {$county};
				$variable[] = $this -> controller -> module_io -> {$township};
				$variable[] = $this -> controller -> module_io -> {$address};
				
				parent::set_value(implode(":", $variable));

        }
		
		public function script(){
?>
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/dk-tw-citySelector/dk-tw-citySelector.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('form').dk_tw_citySelector("select[name='<?php echo $this -> variable; ?>_county']", "select[name='<?php echo $this -> variable; ?>_township']","input[name='<?php echo $this -> variable; ?>_zip']");
	});
	
	<?php if($this -> same_field_name != ""): ?>
	
	$(window).load(function(){
		// $("input[name='<?php echo $this -> variable; ?>_same']").parents(".icheckbox_minimal").click(function(){
			$(".iCheck-helper").click(function(){
			
			var check_input = $("input[name='<?php echo $this -> variable; ?>_same']").parents(".icheckbox_minimal");
			//console.log(check_input.hasClass("checked"));
			if(check_input.hasClass("checked")){
			// if($(this).prop("checked")){
				
				var zip = $("input[name='<?php echo $this -> same_field_name; ?>_zip']").val();
				var county = $("select[name='<?php echo $this -> same_field_name; ?>_county']").val();
				var township = $("select[name='<?php echo $this -> same_field_name; ?>_township']").val();
				var address = $("input[name='<?php echo $this -> same_field_name; ?>_address']").val();
				
				
				$("select[name='<?php echo $this -> variable; ?>_county'] option, select[name='<?php echo $this -> variable; ?>_township'] option").removeAttr("selected");
				$("select[name='<?php echo $this -> variable; ?>_county'] option[value='" + county + "']").attr("selected","selected");
				$("select[name='<?php echo $this -> variable; ?>_county']").change();
				$("select[name='<?php echo $this -> variable; ?>_township'] option[value='" + township + "']").attr("selected","selected");
				$("select[name='<?php echo $this -> variable; ?>_township']").change();
				$("input[name='<?php echo $this -> variable; ?>_address']").attr("value",address);
				
			}
			else{
				
				$("select[name='<?php echo $this -> variable; ?>_county'] option, select[name='<?php echo $this -> variable; ?>_township'] option").removeAttr("selected");
				$("select[name='<?php echo $this -> variable; ?>_county']").change();
				$("select[name='<?php echo $this -> variable; ?>_township']").change();
				$("input[name='<?php echo $this -> variable; ?>_address']").attr("value","");
			}
			
		});
		
	});
	<?php endif; ?>
</script>
<?php
		}
}
?>