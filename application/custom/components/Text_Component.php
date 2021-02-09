<?php
class Text_Component extends Component{

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
				
                return "{$tip}<br/><input type=\"text\" name=\"{$this -> variable}\" value=\"{$value}\" {$str_attribute}/>{$invalid_string}";
        }

        public function get_title(){
                $value = $this -> get_value();
                $string = array();
                while(mb_strlen($value, 'utf-8') > 0){
                        $string[] = mb_substr($value,0,10,'utf-8');
                        $value = mb_substr($value,10,10000,'utf-8');
                }
                return implode("<br />", $string);
        }
}
?>