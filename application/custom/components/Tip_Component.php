<?php
class Tip_Component extends Component{

		/**
		 * 無標題
		 */
		public function has_title(){
			return false;
		}

        public function render($attributes = array()){
        	
			if (preg_match("/tip{(.*?)};/", $this -> element, $element)) {
				return "<div style=\"color:red;\">{$element[1]}</div>";
			}
			
			return "";
        }
}
?>