<?php
/**
 * 按鈕觸發模組
 */
class Action extends Base_Module {

	/**
	 * 在執行主程式之前會執行此函式
	 */
	public function before_main($modules) {
		
		$param = array_merge($_POST, $_GET);
		foreach ($param as $key => $value) {
	
			if(!is_integer(strpos($key,"_PRE_")))continue;
				
			$v = explode("_PRE_",$key);
			$key = $v[1];
			
			if (method_exists($this -> controller, $key)) {
				$this -> controller -> $key();
				break;
			}
		}
	}

	/**
	 * Controller 在載入View之前會先執行此函式
	 */
	public function before_load_view($modules) {

		if(isset($this -> controller -> module)){
				
			$module_code = $this -> controller -> module -> code;
			
			$extend = explode("*", $this -> controller -> module -> code);
			// 共用資料表
			if(count($extend) > 1){
				$module_code = $extend[0] . "__" . $extend[1];
			}
			
			$hook_path = $this -> system_configs["full_application_path"] . "/custom/hook/" . ucfirst($module_code) . ".php";
			
			$hook_class = null;
			if(file_exists($hook_path)){
				require_once($hook_path);
				$hook_name = ucfirst($module_code) . "_Hook";
				
				$hook_class = new $hook_name($modules , $this -> system_configs);
			}
		}
		$param = array_merge($_POST, $_GET);
	
		foreach ($param as $key => $value) {
			
			if(isset($hook_class) && $hook_class != null && method_exists($hook_class,$key)){
				$hook_class -> $key($this -> controller, $value);
				break;
			}
			
			if (method_exists($this -> controller, $key)) {
				$this -> controller -> $key($value);
				break;
			}
			
		}
	}

}
?>