<?php
class TextArea_Component extends Component{

        public function render($attributes = array()){
                
				// 驗證失敗呈現
				$invalid_string = "";
				if($this -> valid_error_message != ""){
					$attributes["class"] = (isset($attributes["class"])) ? ($attributes["class"] . " invalid") : "invalid";
					$invalid_string = "<span class='invalid_message'>{$this -> valid_error_message}</span>";
				}
				
				// 載入元素控制的額外屬性
				if (preg_match("/ext-attrs{(.*?)};/", $this -> element, $element)) {
					$attr_str = $element[1];
					$attrs = explode(",",$attr_str);
					foreach($attrs as $attr){
						$attr_array = explode(":", $attr);
						$attributes[$attr_array[0]] = $attr_array[1];
					}
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
				
                return "{$tip}<br/><textarea name=\"{$this -> variable}\" {$str_attribute}/>{$value}</textarea>{$invalid_string}";
        }
}
?>