<?php
class Label_Group_Component extends Component{

		/**
		 * 無標題
		 */
		public function has_title(){
			return false;
		}

        public function render($attributes = array()){
			return "<div class=\"label_group\">{$this -> name}</div>";
        }
}
?>