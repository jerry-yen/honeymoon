<?php
class Pattern_Component extends Component{

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
				
				$value = htmlentities($value);
				
                return "{$tip}<br/><input type=\"text\" name=\"{$this -> variable}\" value=\"{$value}\" {$str_attribute}/>{$invalid_string}";
        }

		public function update_value_from_io(){
			$this -> object -> {$this -> variable} = $this -> controller -> module_io -> {$this -> variable};
		}
}
?>