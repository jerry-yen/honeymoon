<?php

class Sort_Controller extends AdminController {

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
	protected $item_classes = array();
	
	/**
	 * 清單欄位
	 */
	protected $list_fields = array();

	public function main() {
		
		// 取得模組代碼 並 取得模組元件
		$module_code = $this -> module_io -> mod;
		$this -> module = $this -> module_dao -> get_object("module");
		$this -> module -> get_module($module_code);
		
		// 載入分類清單
		$this -> load_item_classes();
		
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

	// 載入分類清單
	private function load_item_classes() {
		
		/*
		 * 如果網址沒有ID參數，代表根目錄
		 **/
		if($this -> module_io -> id == ""){
			$this -> is_special_level = ($this -> module -> class_field_use_level == 1);
			$this -> item_classes = $this -> module -> get_classes(array(), array(), array("sortTime ASC", "sequence ASC", "createTime DESC"), $this -> is_special_level);
		}
		
		/*
		 * 如果網址有ID參數，代表根目錄
		 **/
		else{
			$this -> parent_class = $this -> module -> get_single_class(array("id=?"), array($this -> module_io -> id));
			if(!$this -> parent_class -> is_exists()){
				$this -> module_showbox -> set_message("查無此分類");
				$this -> module_go -> back();
			}
			
			$this -> is_special_level = (($this -> parent_class -> level + 1) == $this -> module -> class_field_use_level);
			
			$this -> item_classes = $this -> parent_class -> get_sub_classes(array(), array(), array("sortTime ASC", "sequence ASC", "createTime DESC"), $this -> is_special_level);
		}
		
	}

	private function load_fields(){
		if($this -> is_special_level){
			$fields = $this -> module -> class_special_fieldMetadata;
		}
		else{
			$fields = $this -> module -> class_fieldMetadata;
		} 
		
		foreach($fields as $key => $field){
			
			$special = ($this -> is_special_level) ? "special_" : "";
			
			// 清單預設會顯示發佈時間，如欄位會有發佈時間就不另外顯示
			$variable = "class_" . $special . "fieldMetadata_field_variable";
			if($field -> {$variable} == "createTime"){
				continue;
			}
			
			$list = "class_" . $special . "fieldMetadata_field_list";
			if($field -> {$list} == "Y"){
				$this -> list_fields[] = $field;
			}
		}
	}
	
	/**
	 * 排序
	 */
	public function sort_class() {

		if (!is_array($this -> module_io -> ids)) {
			return;
		}

		foreach ($this -> module_io -> ids as $key => $id) {

			$item_class = $this -> module -> get_single_class(array("id=?"),array($id));
			if ($item_class -> is_exists()) {
				$item_class -> sequence = $key;
				$item_class -> sortTime = date("Y-m-d H:i:s");
				$item_class -> update();
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