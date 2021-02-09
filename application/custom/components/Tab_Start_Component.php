<?php
class Tab_Start_Component extends Component {
	
	


	/**
	 * 是否有標題(群組標籤就沒有標題)
	 */
	public function has_title() {
		return false;
	}
	
	public function is_group_start(){
		return true;
	}
	
	public function init(){
		@session_start();
		unset($_SESSION["tabs"]);
		unset($_SESSION["tabs_content"]);
		@session_write_close();
	}
	
	public function create_tab(){
		@session_start();
		$_SESSION["tabs"][] = array(
			"name" => $this -> name,
			"variable" => $this -> variable
		);
		@session_write_close();
	}
	
	public function add_component($component){
		@session_start();
		$last_index = count($_SESSION["tabs"]) - 1;
		$last_index = ($last_index < 0) ? 0 : $last_index;
		$_SESSION["tabs_content"][$last_index] = $component;
		@session_write_close();
	}
	
}
?>