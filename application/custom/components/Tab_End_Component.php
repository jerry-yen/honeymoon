<?php
class Tab_End_Component extends Component {
	
	private static $tab_name = array();
	private static $tab_content = array();


	/**
	 * 是否有標題(群組標籤就沒有標題)
	 */
	public function has_title() {
		return false;
	}
	
	public function is_group_end(){
		return true;
	}
	
	public function add_component($component){
		@session_start();
		$last_index = count($_SESSION["tabs"]) - 1;
		$last_index = ($last_index < 0) ? 0 : $last_index;
		$_SESSION["tabs_content"][$last_index] .= $component;
		@session_write_close();
	}

	public function render($attributes = array()) {
		
		@session_start();
		
		$render = "<div class=\"nav-tabs-custom\">
						<ul class=\"nav nav-tabs\">";
						foreach($_SESSION["tabs"] as $key => $tab){
							$active = ($key == 0) ? "active" : "";
							$render .="<li class=\"{$active}\"><a href=\"#tab_{$key}\" data-toggle=\"tab\">{$tab["name"]}</a></li>";
						}
        $render .="		</ul>
						<div class=\"tab-content\">";
						
						foreach($_SESSION["tabs_content"] as $key => $content){
							$active = ($key == 0) ? "active" : "";
		$render .="			<div class=\"tab-pane {$active}\" id=\"tab_{$key}\">
							{$content}
							</div><!-- /.tab-pane -->";
						}
                            
		$render .="		</div><!-- /.tab-content -->
					</div><!-- nav-tabs-custom -->"	;
		
	//	print_r($_SESSION["tab_component"]);
		@session_write_close();
		
		return $render;
	}
	
}
?>