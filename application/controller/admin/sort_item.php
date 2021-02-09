<?php

class Sort_Item_Controller extends AdminController {

	/**
	 * 模組
	 */
	public $module = array();
	
	/**
	 * 導覽路徑
	 */
	protected $nav_class = array();
	
	/**
	 * 父分類
	 */
	protected $parent_class = array();
	
	/**
	 * 分類
	 */
	protected $items = array();
	
	/**
	 * 清單欄位
	 */
	protected $list_fields = array();

	public function main() {
		
		// 取得模組代碼 並 取得模組元件
		$module_code = $this -> module_io -> mod;
		$this -> module = $this -> module_dao -> get_object("module");
		$this -> module -> get_module($module_code);
		
		
		// 載入項目清單
		$this -> load_items();
		
		// 載入清單欄位
		$this -> load_fields();
		
		// 載入導覽
		$this -> load_nav();

	}
	
	// 載入導覽
	private function load_nav(){
		if($this -> module_io -> id != ""){
			$parent = $this -> module -> get_single_class(array("id=?"),array($this -> module_io -> id));
			while($parent -> is_exists()){
				$this -> nav_class[] = $parent;
				$parent = $parent -> get_parent();
			}
			
			$this -> nav_class = array_reverse($this -> nav_class);
		}
	}

	// 載入項目清單
	private function load_items() {
		
		if($this -> module_io -> id == ""){		
			$this -> items = $this -> module -> get_items();
		}
		else{
			$this -> parent_class = $this -> module -> get_single_class(array("id=?"),array($this -> module_io -> id));
			
			// 例外狀況
			if(!$this -> parent_class -> is_exists()){
				$this -> module_showbox -> set_message("查無此分類");
				$this -> module_go -> back();
			}
			$this -> items = $this -> parent_class -> get_items();
		}
	}
	
	
	/**
	 * 載入清單欄位資訊
	 * 開發者設定如果有將「清單」欄位項目打鉤才會出現
	 */
	private function load_fields(){
		
		$fields = $this -> module -> fieldMetadata;
		
		if(!is_array($fields)){
			$fields = json_decode($fields);
		}
		
		foreach($fields as $key => $field){
			
			// 清單預設會顯示發佈時間，如欄位會有發佈時間就不另外顯示
			if($field -> fieldMetadata_field_variable == "createTime"){
				continue;
			}
			
			// 卻顯示的清單欄位
			if($field -> fieldMetadata_field_list == "Y"){
				$this -> list_fields[] = $field;
			}
		}
	}
	
	/**
	 * 排序
	 */
	public function sort_item() {

		if (!is_array($this -> module_io -> ids)) {
			return;
		}

		foreach ($this -> module_io -> ids as $key => $id) {

			$item = $this -> module -> get_single_item(array("id=?"),array($id));
			if ($item -> is_exists()) {
				$item -> sequence = $key;
				$item -> sortTime = date("Y-m-d H:i:s");
				$item -> update();
			}
		}
		$this -> module_showbox -> set_message("排序成功!");
		$this -> module_go -> back();
		
	}

	
	
	public function return_level(){
		
		if($this -> module_io -> id != ""){
			$url_id = "&id=" . $this -> module_io -> id;
		}
		
		$this -> module_go -> page("list.php?mod=" . $this -> module_io -> mod . $url_id );
	}

}
?>