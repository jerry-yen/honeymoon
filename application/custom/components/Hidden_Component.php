<?php
class Hidden_Component extends Component{

        public function render($attributes = array()){
        		$enums = array();
				if(preg_match("/enum{(.*?)};/", $this -> element, $element)){
					$enums = explode(",", $element[1]);
				}
			
                // 提示
                $value = $this -> get_value();
				if(count($enums) > 0 && ($value == "" || is_null($value))){
					$value = $enums[rand(0,count($enums))];
				}
                return "<br/><input type=\"hidden\" name=\"{$this -> variable}\" value=\"{$value}\" />";
        }
		
		public function script(){	
?>
<script>
	$(document).ready(function(){

		$("input[name='<?php echo $this -> variable; ?>']").parents(".form-group").hide();
			
	});
</script>
<?php
	}
}
?>