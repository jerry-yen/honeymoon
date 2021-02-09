<?php
class Note_Controller extends AdminController {

	/**
	 * @var 模組物件
	 */
	public $module = array();
	
	protected $item = array();
	public function main() {
		// 取得模組代碼 並 取得模組元件
		$module_code = $this -> module_io -> mod;
		$this -> module = $this -> module_dao -> get_object("module");
		$this -> module -> get_module($module_code);	}

	
}
?>