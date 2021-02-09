<?php
class Label_Component extends Component{

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
                
                
                $value = html_entity_decode($this -> get_value());
				
							
                return "<br/><span {$str_attribute} style=\"height:auto;\">{$value}<span><input type=\"hidden\" name=\"{$this -> variable}\" value=\"{$value}\" />";
        }
		
		
}
?>