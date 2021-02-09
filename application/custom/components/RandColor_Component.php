<?php
class RandColor_Component extends Component{

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

        public function set_value($value){
        	if(trim($this -> get_value()) == ""){
        		$rnd = array("#ec821b","#03837a","#a40b5d","#71495e","#255856","#ad6e3a","#65054b","#3a6986","#000000","#ff0101");
        		$this -> value = $rnd[rand(0,count($rnd)-1)];
        	}
        }

        public function get_title(){
        	return "<div style='width:100px;background-color:" . $this -> value . ";'>&nbsp;</div>";
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