<?php

class Search_Item_Controller extends AdminController
{

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

	protected $status_count = array();

	protected $course;


	public function main()
	{

		// 取得模組代碼 並 取得模組元件
		$module_code = $this->module_io->mod;
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);

		// 載入搜尋元件
		$this->load_search_component();

		// 載入項目清單
		$this->load_items();

		// 載入清單欄位
		$this->load_fields();

		if ($this->module_io->mod == 'signup*v_signup' && isset($this->module_io->course)) {
			for ($i = 0; $i < 4; $i++) {
				$items = $this->get_items($this->module_io->mod, array('course=?', 'status=?'), array($this->module_io->course, $i));
				$this->status_count[$i] = count($items);
			}

			$this -> course = $this -> get_single_item('courses', $this->module_io->course);
		}
	}


	/**
	 * 載入搜尋元件
	 */
	private function load_search_component()
	{

		if (!is_array($this->module->fieldSearch)) {
			$this->module->fieldSearch = json_decode($this->module->fieldSearch);
			$this->module->fieldSearch = is_null($this->module->fieldSearch) ? array() : $this->module->fieldSearch;
		}

		require_once($this->config_full_application_path . "/custom/components/Component.php");

		foreach ($this->module->fieldSearch as $field) {
			$fieldMetadata["name"] = $field->fieldSearch_field_name;
			$fieldMetadata["variable"] = $field->fieldSearch_field_variable;
			$fieldMetadata["type"] = $field->fieldSearch_field_type;
			$fieldMetadata["element"] = $field->fieldSearch_field_element;
			$fieldMetadata["default"] = $field->fieldSearch_field_where;
			$fieldMetadata["tip"] = "";

			require_once($this->config_full_application_path . "/custom/components/" . $fieldMetadata["type"] . "_Component.php");
			$component_name = $fieldMetadata["type"] . "_Component";

			$this->search_fields[$fieldMetadata["variable"]] = new $component_name($this->module_io->{$fieldMetadata["variable"]}, $fieldMetadata, $this->module);
		}
	}

	// 載入項目清單
	private function load_items()
	{

		$where = array();
		$values = array();

		foreach ($this->search_fields as $key => $component) {
			$value = $this->module_io->{$key};
			if (!isset($value) || trim($value) == '') {
				$this->search_fields[$key]->set_default('');
				continue;
			}

			$sql = $component->get_default();


			if (preg_match_all("/(LIKE )?\?/i", $sql, $param)) {
				foreach ($param[0] as $param) {
					if ($param == "LIKE ?") {
						$values[] = "%" . $value . "%";
					} else {
						$values[] = $value;
					}
				}
			}
			$where[] = $sql;
		}

		$this->module_pagination->set_count_per_page($this->module->item_count_per_page);
		$this->module_pagination->unlock();
		$this->items = $this->module->get_items($where, $values);
		$this->module_pagination->lock();
	}


	/**
	 * 載入清單欄位資訊
	 * 開發者設定如果有將「清單」欄位項目打鉤才會出現
	 */
	private function load_fields()
	{

		$fields = $this->module->fieldMetadata;

		if (!is_array($fields)) {
			$fields = json_decode($fields);
		}

		foreach ($fields as $key => $field) {

			// 清單預設會顯示發佈時間，如欄位會有發佈時間就不另外顯示
			if ($field->fieldMetadata_field_variable == "createTime") {
				continue;
			}

			// 卻顯示的清單欄位
			if ($field->fieldMetadata_field_list == "Y") {
				$this->list_fields[] = $field;
			}
		}
	}

	/**
	 * 批次刪除
	 */
	public function delete()
	{

		if (!is_array($this->module_io->ids)) {
			return;
		}

		foreach ($this->module_io->ids as $id) {

			$item = $this->module->get_single_item(array("id=?"), array($id));
			if ($item->is_exists()) {
				$item->delete();
			}
		}
		$this->module_showbox->set_message("刪除成功!");
		$this->module_go->back(0);
	}

	/**
	 * 批次上架
	 */
	public function publish()
	{

		if (!is_array($this->module_io->ids)) {
			return;
		}

		foreach ($this->module_io->ids as $id) {

			$item = $this->module->get_single_item(array("id=?"), array($id));
			if ($item->is_exists()) {
				$item->publish = "Y";
				$item->update();
			}
		}
		$this->module_showbox->set_message("批次上架成功!");
		$this->module_go->back();
	}

	/**
	 * 批次下架
	 */
	public function unpublish()
	{

		if (!is_array($this->module_io->ids)) {
			return;
		}

		foreach ($this->module_io->ids as $id) {

			$item = $this->module->get_single_item(array("id=?"), array($id));
			if ($item->is_exists()) {
				$item->publish = "";
				$item->update();
			}
		}
		$this->module_showbox->set_message("批次下架成功!");
		$this->module_go->back();
	}

	/**
	 * 上一頁
	 */
	public function return_level()
	{
		$this->module_go->to("list");
	}

	/**
	 * 搜尋
	 */
	public function search()
	{
		$params = array();
		foreach ($this->search_fields as $key => $component) {
			$value = $this->module_io->{$key};
			if (!isset($value) || $value == "") continue;
			$params[] = $component->get_variable() . "=" . $value;
		}
		if (count($params) > 0) {
			$this->module_go->page("search_item.php?mod=" . $this->module_io->mod . "&" . implode("&", $params));
		} else {
			$this->module_showbox->set_message("請輸入卻搜尋的條件");
		}
	}

	public function export()
	{
		$fields = $this->module->fieldMetadata;
		foreach ($fields as $field) {
			$head[] = $field->fieldMetadata_field_name;
		}

		$data = array();

		foreach ($this->items as $key => $item) {
			foreach ($fields as $field) {
				$component = $item->{$field->fieldMetadata_field_variable};
				if (method_exists($component, 'get_text')) {
					$data[$key][] = $component->get_text();
				} else {
					$data[$key][] = trim(strip_tags($component->get_title()));
				}
			}
		}

		$this->module_loader->load('hmexcel');
		$this->module_hmexcel->set_header($head);
		$this->module_hmexcel->set_data($data);
		$this->module_hmexcel->export($this->module->title);
		exit;
	}
}
