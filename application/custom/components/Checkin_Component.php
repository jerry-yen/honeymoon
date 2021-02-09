<?php
class Checkin_Component extends Component{

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
				$data = strip_tags($this -> get_title());
							
                return "<br/><span {$str_attribute} style=\"height:auto;\">{$data}<span><input type=\"hidden\" name=\"{$this -> variable}\" value=\"{$value}\" />";
        }
		
		public function get_title(){
			$value = $this -> get_value();
			$this -> datas = ($this -> datas == array()) ? $this -> get_data_source() : $this -> datas;
			
			foreach($this -> datas as $data){
				if($value == $data[0]){
					return "<span class='check_status_{$data[0]}'>" . $data[1] . "</span>";
				}
			}
		}
		
		
}
?>