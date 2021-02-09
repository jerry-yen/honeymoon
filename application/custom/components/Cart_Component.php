<?php
class Cart_Component extends Component{

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
                $items = $this -> get_value();
				if(is_string($items)){
					$items = json_decode($items);
				}
				$rows = "";
				foreach($items as $item){
					$rows .= "<tr><td style=\"border:1px #ccc solid;padding:10px 10px;\">" . $item -> title . "</td><td style=\"border:1px #ccc solid;padding:0px 10px;\">" . $item -> count . "</td><td style=\"border:1px #ccc solid;padding:0px 10px;\">" . $item -> price . "</td></tr>";
				}
				
				
                return "<table style=\"width:100%;border:1px #ccc solid;\">
                	<tr>
                		<td style=\"border:1px #ccc solid;text-align:center;font-weight:bold;\">產品名稱</td><td style=\"border:1px #ccc solid;text-align:center;font-weight:bold;\">數量</td><td style=\"border:1px #ccc solid;text-align:center;font-weight:bold;\">單價</td>
                	</tr>
                	" . $rows . "
                </table>";
        }

		public function set_value($value){
			$this -> value = json_encode($value);
		}
}
?>