<?php

class Member_List_Controller extends AdminController {

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
	 * 項目
	 */
	protected $items = array();
	
	/**
	 * 清單欄位
	 */
	protected $list_fields = array();
	
	/**
	 * 搜尋欄位
	 */
	protected $search_fields = array();
	
	protected $skip_createTime_field = false;
	
	protected $skip_command_field = false;

	public function main() {
		// 取得模組代碼 並 取得模組元件
		$this -> module = $this -> module_dao -> get_object("module");
		$this -> module -> get_module('join_course');
		$joins = $this -> module -> get_items(array('course_id=?'), array($this -> module_io -> course_id));

		$this -> module -> get_module('member');
		foreach($joins as $key => $join){
			$member = $this -> module -> get_single_item(array('id=?'), array($join -> member_id -> get_value()));
			$joins[$key] -> member_title = $member -> title -> get_value();
		}
		
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
	
	/**
	 * 載入搜尋元件
	 */
	private function load_search_component(){
		
		if(!is_array($this -> module -> fieldSearch)){
			$this -> module -> fieldSearch = json_decode($this -> module -> fieldSearch);
			$this -> module -> fieldSearch = is_null($this -> module -> fieldSearch) ? array() : $this -> module -> fieldSearch; 
		}
		
		require_once ($this -> config_full_application_path . "/custom/components/Component.php");
		
		foreach($this -> module -> fieldSearch as $field){
			$fieldMetadata["name"] = $field -> fieldSearch_field_name;
			$fieldMetadata["variable"] = $field -> fieldSearch_field_variable;
			$fieldMetadata["type"] = $field -> fieldSearch_field_type;
			$fieldMetadata["element"] = $field -> fieldSearch_field_element;
			$fieldMetadata["default"] = "";
			$fieldMetadata["tip"] = "";
			
			require_once ($this -> config_full_application_path . "/custom/components/" . $fieldMetadata["type"] . "_Component.php");
			$component_name = $fieldMetadata["type"] . "_Component";
			
			$this -> search_fields[$fieldMetadata["variable"]] = new $component_name($this -> module_io -> {$fieldMetadata["variable"]}, $fieldMetadata, $this -> module);
		}
	}

	// 載入項目清單
	private function load_items() {
		
		/* 
		 * 驗證判斷
		 * 如果網址沒有ID參數(ID為父層的分類ID)，則代表這次此模組「必需」沒有分類 (分類層數為0)
		 * 例外狀況：但又查得到這個模組是有分類的！因此導回分類清單頁
		 * 正常狀況：取得項目清單
		 **/
		
		$where = array();
		$values = array();
		if($this -> module -> login_self == "Y"){
			$fields = $this -> module -> fieldMetadata;
		
			if(!is_array($fields)){
				$fields = json_decode($fields);
			}
			
			
		
			foreach($fields as $key => $field){
			
				// 清單預設會顯示發佈時間，如欄位會有發佈時間就不另外顯示
				if($field -> fieldMetadata_field_type == "Logined_Item"){
					@session_start();
					$where[] = $field -> fieldMetadata_field_variable . "=?";
					$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"];
					@session_write_close();
					break;
				}
			
			
			}
			
		}
		
		if($this -> module_io -> id == ""){
				
			// 例外狀況
			$this -> item_classes = $this -> module -> get_classes();
			
			if(count($this -> item_classes) > 0){
				$this -> module_go -> page("list.php?mod=" . $this -> module_io -> mod);
			}
			
			
			
			// 正常狀況
			$this -> module_pagination -> set_count_per_page($this -> module -> item_count_per_page);
			$this -> module_pagination -> unlock();
			$this -> items = $this -> module -> get_items($where, $values);
			$this -> module_pagination -> lock();
			
		}
		
		/* 
		 * 驗證判斷
		 * 如果網址有ID參數(ID為父層的分類ID)，則代表這次此模組「必需」有分類 (分類層數 > 0)
		 * 例外狀況：但這個ID查不到任何分類，跳離開這一頁(回上一頁)
		 * 正常狀況：取得父分類底下的所有項目清單
		 **/
		 
		else{
			$this -> parent_class = $this -> module -> get_single_class(array("id=?"),array($this -> module_io -> id));
			
			// 例外狀況
			if(!$this -> parent_class -> is_exists()){
				$this -> module_showbox -> set_message("查無此分類");
				$this -> module_go -> back();
			}
			
			// 正常狀況
			$this -> module_pagination -> set_count_per_page($this -> module -> item_count_per_page);
			$this -> module_pagination -> unlock();
			$this -> items = $this -> parent_class -> get_items($where, $values);
			$this -> module_pagination -> lock();
			
			
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
	 * 新增項目
	 */
	public function add_item() {
		if($this -> module_io -> id == ""){
			$this -> module_go -> page("add_item.php?mod=" . $this -> module_io -> mod);
		}
		else{
			$this -> module_go -> page("add_item.php?mod=" . $this -> module_io -> mod . "&id=" . $this -> module_io -> id);
		}
		
	}

	/**
	 * 批次刪除
	 */
	public function delete() {

		if (!is_array($this -> module_io -> ids)) {
			return;
		}

		foreach ($this -> module_io -> ids as $id) {

			$item = $this -> module -> get_single_item(array("id=?"),array($id));
			if ($item -> is_exists()) {
				$item -> delete();
			}
		}
		$this -> module_showbox -> set_message("刪除成功!");
		$this -> module_go -> back(0);
		
	}

	/**
	 * 批次上架
	 */
	public function publish() {

		if (!is_array($this -> module_io -> ids)) {
			return;
		}

		foreach ($this -> module_io -> ids as $id) {

			$item = $this -> module -> get_single_item(array("id=?"),array($id));
			if ($item -> is_exists()) {
				$item -> publish = "Y";
				$item -> update();
			}
		}
		$this -> module_showbox -> set_message("批次上架成功!");
		$this -> module_go -> back(0);
		
	}
	
	/**
	 * 批次下架
	 */
	public function unpublish() {

		if (!is_array($this -> module_io -> ids)) {
			return;
		}

		foreach ($this -> module_io -> ids as $id) {

			$item = $this -> module -> get_single_item(array("id=?"),array($id));
			if ($item -> is_exists()) {
				$item -> publish = "";
				$item -> update();
			}
		}
		$this -> module_showbox -> set_message("批次下架成功!");
		$this -> module_go -> back(0);
		
	}
	
	/**
	 * 排序項目
	 */
	public function sort_item() {
		if($this -> module_io -> id == ""){
			$this -> module_go -> page("sort_item.php?mod=" . $this -> module_io -> mod);
		}
		else{
			$this -> module_go -> page("sort_item.php?mod=" . $this -> module_io -> mod . "&id=" . $this -> module_io -> id);
		}
		
	}
	
	/**
	 * 上一頁
	 */
	public function return_level(){
		if( $this -> parent_class -> parentId == ""){
			$this -> module_go -> page("list.php?mod=" . $this -> module_io -> mod);
		}
		else{
			$this -> module_go -> page("list.php?mod=" . $this -> module_io -> mod . "&id=" . $this -> parent_class -> parentId);
		}
	}
	
	/**
	 * 搜尋
	 */
	public function search(){
		$params = array();
		foreach($this -> search_fields as $key => $component){
			$value = $this -> module_io -> {$key};			
			if(!isset($value) || $value == "") continue;
			$params[] = $component -> get_variable() . "=" . $value; 
		}
		if(count($params) > 0){
			$this -> module_go -> page("search_item.php?mod=" . $this -> module_io -> mod . "&" . implode("&", $params));
		}
		else{
			$this -> module_showbox -> set_message("請輸入卻搜尋的條件");
		}
	}

	/**
	 * 上班打卡
	 */
	public function checkin(){
		// 取得今日指定用戶的所有打卡記錄
		@session_start();
		
		$this -> member = $this -> get_single_item("member", $_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"]);
		$items = $this -> get_items("checkin",array("DATE_FORMAT(check_date_time,'%Y-%m-%d')=?","title=?"),array(date("Y-m-d"),$this -> member -> id),array("createTime DESC"));
		
		$this -> module_loader -> load("utility");
		
		$this -> work_setting = $this -> module_utility -> get_member_work_setting($_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"]);
		
		@session_write_close();
		

		if($this -> work_setting["is_working"] != "Y"){
			$this -> module_alert -> set_message("本日放假，不需打卡！");
			$this -> module_go -> back(0);
		}
		
		
		
		// 本次打卡時間
		$this -> check_date_time = date("Y-m-d H:i:s");

		
		// 本日第一次打卡
		if(count($items) == 0){
			$item = $this -> get_single_item("checkin","");
			$item -> title -> set_value("");
			$item -> check_date_time -> set_value($this -> check_date_time);
			$item -> check_ip -> set_value($_SERVER["REMOTE_ADDR"]);
			$item -> status -> set_value(1); // 上班
			
			$this -> work_status = "上班";
			
			$module = $this -> module_dao -> get_object("module",$item -> moduleId);
			$this -> last_item_id = $module -> add_item($item);
		}
		else{
			
			$prev_item = $items[0];
			$status = $prev_item -> status -> get_value();

			
			$item = $this -> get_single_item("checkin","");
			$item -> title -> set_value("");
			$item -> check_date_time -> set_value($this -> check_date_time);
			$item -> check_ip -> set_value($_SERVER["REMOTE_ADDR"]);
			$item -> status -> set_value(($status == "1")?"2":"1"); // 下班
			$this -> work_status = ($status == "1")?"下班":"上班";
			$module = $this -> module_dao -> get_object("module",$item -> moduleId);
			$this -> last_item_id = $module -> add_item($item);
		}
		
		$this -> overtimes = $this -> module_utility -> get_overtime(strtotime($this -> check_date_time), $items, $this -> work_setting);
		$this -> is_overtime = (count($this -> overtimes) > 0);
		
		if(!$this -> is_overtime){
			$this -> module_alert -> set_message("您於，\"{$this -> check_date_time} 打卡「{$this -> work_status}」\"");
			$this -> module_go -> back(0);
		}
		
	}
}
?>