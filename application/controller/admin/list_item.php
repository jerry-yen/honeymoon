<?php

class List_Item_Controller extends AdminController
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

	protected $skip_createTime_field = false;

	protected $skip_command_field = false;


	/**
	 * 客製化功能變數
	 */
	protected $status_count = array();

	public function main()
	{
		// 取得模組代碼 並 取得模組元件
		$module_code = $this->module_io->mod;
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);

		// 載入項目清單
		$this->load_items();

		// 載入清單欄位
		$this->load_fields();

		// 載入導覽
		$this->load_nav();

		$this->load_search_component();


		// Hook

		$extend = explode("*", $module_code);
		// 共用資料表
		if (count($extend) > 1) {
			$module_code = $extend[0] . "__" . $extend[1];
		}



		$hook_path = $this->config_full_application_path . "/custom/hook/" . ucfirst($module_code) . ".php";
		$hook_class = null;
		if (file_exists($hook_path)) {
			require_once($hook_path);
			$hook_name = ucfirst($module_code) . "_Hook";
			$hook_class = new $hook_name($this->modules, $this->configs);

			if ($hook_class != null && method_exists($hook_class, "load_items")) {
				$this->items = $hook_class->load_items($this->items);
			}

			if ($hook_class != null && method_exists($hook_class, "load_fields")) {
				$this->list_fields = $hook_class->load_fields($this->list_fields);
			}

			if ($hook_class != null && method_exists($hook_class, "skip_createTime_field")) {
				$this->skip_createTime_field = $hook_class->skip_createTime_field();
			}

			if ($hook_class != null && method_exists($hook_class, "skip_command_field")) {
				$this->skip_command_field = $hook_class->skip_command_field();
			}

			if ($hook_class != null && method_exists($hook_class, "skip_search_field")) {
				$this->skip_search_field = $hook_class->skip_search_field();
			}

			if ($hook_class != null && method_exists($hook_class, "replace_item_setting")) {
				$replace_item = $hook_class->replace_item_setting();
				if ($replace_item != "") {
					$this->module->item_setting = $hook_class->replace_item_setting();
				}
			}

			if ($hook_class != null && method_exists($hook_class, "replace_module")) {
				$this->module = $hook_class->replace_module($this->module);
			}
		}


		if ($this->module_io->mod == 'member' && isset($this->module_io->course_id)) {
			$this->status_count[0] = $this->status_count[1] = $this->status_count[2] = $this->status_count[3] = 0;
			$joins = $this->get_items('join_course', array('course_id=?'), array($this->module_io->course_id));
			foreach ($joins as $key => $join) {
				$this->status_count[$join->status->get_value()]++;
			}
		}
	}

	// 載入導覽
	private function load_nav()
	{
		if ($this->module_io->id != "") {
			$parent = $this->module->get_single_class(array("id=?"), array($this->module_io->id));
			while ($parent->is_exists()) {
				$this->nav_class[] = $parent;
				$parent = $parent->get_parent();
			}

			$this->nav_class = array_reverse($this->nav_class);
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
			$fieldMetadata["default"] = "";
			$fieldMetadata["tip"] = "";

			require_once($this->config_full_application_path . "/custom/components/" . $fieldMetadata["type"] . "_Component.php");
			$component_name = $fieldMetadata["type"] . "_Component";

			$this->search_fields[$fieldMetadata["variable"]] = new $component_name($this->module_io->{$fieldMetadata["variable"]}, $fieldMetadata, $this->module);
		}
	}

	// 載入項目清單
	private function load_items()
	{

		/* 
		 * 驗證判斷
		 * 如果網址沒有ID參數(ID為父層的分類ID)，則代表這次此模組「必需」沒有分類 (分類層數為0)
		 * 例外狀況：但又查得到這個模組是有分類的！因此導回分類清單頁
		 * 正常狀況：取得項目清單
		 **/

		$where = array();
		$values = array();
		if ($this->module->login_self == "Y") {
			$fields = $this->module->fieldMetadata;

			if (!is_array($fields)) {
				$fields = json_decode($fields);
			}



			foreach ($fields as $key => $field) {

				// 清單預設會顯示發佈時間，如欄位會有發佈時間就不另外顯示
				if ($field->fieldMetadata_field_type == "Logined_Item") {
					@session_start();
					$where[] = $field->fieldMetadata_field_variable . "=?";
					$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"];
					@session_write_close();
					break;
				}
			}
		}

		if ($this->module_io->id == "") {

			// 例外狀況
			$this->item_classes = $this->module->get_classes();

			if (count($this->item_classes) > 0) {
				$this->module_go->page("list.php?mod=" . $this->module_io->mod);
			}



			// 正常狀況
			$this->module_pagination->set_count_per_page($this->module->item_count_per_page);
			$this->module_pagination->unlock();
			$this->items = $this->module->get_items($where, $values);
			$this->module_pagination->lock();
		}

		/* 
		 * 驗證判斷
		 * 如果網址有ID參數(ID為父層的分類ID)，則代表這次此模組「必需」有分類 (分類層數 > 0)
		 * 例外狀況：但這個ID查不到任何分類，跳離開這一頁(回上一頁)
		 * 正常狀況：取得父分類底下的所有項目清單
		 **/ else {
			$this->parent_class = $this->module->get_single_class(array("id=?"), array($this->module_io->id));

			// 例外狀況
			if (!$this->parent_class->is_exists()) {
				$this->module_showbox->set_message("查無此分類");
				$this->module_go->back();
			}

			// 正常狀況
			$this->module_pagination->set_count_per_page($this->module->item_count_per_page);
			$this->module_pagination->unlock();
			$this->items = $this->parent_class->get_items($where, $values);
			$this->module_pagination->lock();
		}
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
	 * 新增項目
	 */
	public function add_item()
	{
		if ($this->module_io->id == "") {
			$this->module_go->page("add_item.php?mod=" . $this->module_io->mod);
		} else {
			$this->module_go->page("add_item.php?mod=" . $this->module_io->mod . "&id=" . $this->module_io->id);
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
		$this->module_go->back(0);
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
		$this->module_go->back(0);
	}

	/**
	 * 排序項目
	 */
	public function sort_item()
	{
		if ($this->module_io->id == "") {
			$this->module_go->page("sort_item.php?mod=" . $this->module_io->mod);
		} else {
			$this->module_go->page("sort_item.php?mod=" . $this->module_io->mod . "&id=" . $this->module_io->id);
		}
	}

	/**
	 * 上一頁
	 */
	public function return_level()
	{
		if ($this->parent_class->parentId == "") {
			$this->module_go->page("list.php?mod=" . $this->module_io->mod);
		} else {
			$this->module_go->page("list.php?mod=" . $this->module_io->mod . "&id=" . $this->parent_class->parentId);
		}
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

		if ($this->module->code == 'signup*v_signup') {
			$head = array('姓名', 'ID', '課程代碼', '出席記錄', '社工師積分', '公務人員時數', '保護性社工', '認證時數');
			$fields = array('student_title', 'student_identity', 'course_code', '', '', '', '', 'course_datetimes');
			foreach ($this->items as $key => $item) {
				foreach ($fields as $field) {
					if ($field == '') {
						$data[$key][] = '';
					} else {
						$component = $item->{$field};

						if ($field == 'course_datetimes') {

							if (!is_array($component)) {
								$component = json_decode($component);
							}
							$hours = 0;
							$times = $component;
							foreach ($times as $time) {
								$hours += $time->datetimes_field_hour;
							}

							$component = $hours;
						}

						if (method_exists($component, 'get_text')) {
							$data[$key][] = $component->get_text();
						} else if (gettype($component) == 'object') {
							$data[$key][] = trim(strip_tags($component->get_title()));
						} else {
							$data[$key][] = $component;
						}
					}
				}
			}
		} else {
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
		}
		$this->module_loader->load('hmexcel');
		$this->module_hmexcel->set_header($head);
		$this->module_hmexcel->set_data($data);
		$this->module_hmexcel->export($this->module->title);
		exit;
	}

	public function import()
	{
		// 取得上傳的檔案
		$files = $this->module_file->upload('import');
		if (count($files) == 0) {
			$this->module_alert->set_message('檔案上傳錯誤!');
			$this->module_go->back();
		}

		// 讀取 xlsx 的檔案
		$this->module_loader->load('phpexcel_export');
		$excelFilePath = $this->config_full_upload_path . $files[0]->path;
		$type = PHPExcel_IOFactory::identify($excelFilePath);
		$objReader = PHPExcel_IOFactory::createReader($type);
		$objPHPExcel = $objReader->load($excelFilePath);
		$rows = array();
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			$rows = $worksheet->toArray();
			break;
		}

		if ($this->module->code == 'signup*v_signup') {
			$fields = array('student_title', 'student_identity', 'course_code', 'hours', 'signup_datetime');

			foreach ($rows as $r_key => $row) {
				// 跳過標題
				if ($r_key == 0) {
					continue;
				}

				if($row[1] == ''){
					continue;
				}
				
				print_r($row);
				$student = $this->get_single_no_id_item('students', array('identity=?'), array($row[1]));
				if (!$student->is_exists()) {
					continue;
				}
				
				$course = $this->get_single_no_id_item('courses', array('code=?'), array($row[2]));
				if (!$course->is_exists()) {
					continue;
				}

				$item = $this->get_single_no_id_item("signup*v_signup", array('student=?', 'course=?'), array($student->id, $course->id));
				if ($item->is_exists()) {
					continue;
				}

				$new_item = $this->get_single_no_id_item($this->module->code);
				$new_item->checkin->set_value(1);
				$new_item->hours->set_value($row[3]);
				$new_item->student->set_value($student->id);
				$new_item->course->set_value($course->id);
				$new_item->createTime->set_value($row[4]);
				$new_item->status->set_value('1');
				$new_item->smsSend->set_value('N');
				$new_item->emailSend->set_value('N');
				$new_item->insert(false, true);
			}
			exit;
		} else {
			// 取得欄位後設資料
			$fields = $this->module->fieldMetadata;

			// 判斷資料是否重複的關鍵欄位
			$identity_fields = array();
			foreach ($fields as $key => $field) {
				if (in_array($field->fieldMetadata_field_variable, array('code', 'identity'))) {
					$identity_fields[] = $field->fieldMetadata_field_variable;
				}
			}
			foreach ($rows as $r_key => $row) {
				// 跳過標題
				if ($r_key == 0) {
					continue;
				}


				$item = $this->get_single_no_id_item($this->module->code);
				// 轉換成資料表的記錄
				foreach ($row as $c_key => $value) {
					if (!isset($fields[$c_key])) {
						continue;
					}

					$item->{$fields[$c_key]->fieldMetadata_field_variable}->redata($value);
				}
				/*
	if (isset($item->updateTime)) {
		$item->updateTime->set_value(date('Y-m-d H:i:s'));
	}
*/
				$where = array();
				$value = array();
				foreach ($identity_fields as $field) {
					$where[] = $field . '=?';
					if (gettype($item->{$field}) == 'object') {
						$value[] = $item->{$field}->get_value();
					} else {
						$value[] = $item->{$field};
					}
					//$value[] = $item->{$field}->get_value();
				}
				if (!is_array($value) || count($value) == 0 || (count($value) == 1 && $value[0] == '')) {
					unset($item);
					continue;
				}

				$check = $this->get_single_no_id_item($this->module->code, $where, $value);
				if ($check->is_exists()) {
					if (gettype($item->updateTime) == 'object') {
						$item->updateTime->set_value(date('Y-m-d H:i:s'));
					} else {
						$item->updateTime = date('Y-m-d H:i:s');
					}
					$item->id = $check->id;
					$item->update();
				} else {
					if (gettype($item->createTime) == 'object') {
						$item->createTime->set_value(date('Y-m-d H:i:s'));
					} else {
						$item->createTime = date('Y-m-d H:i:s');
					}
					if (gettype($item->updateTime) == 'object') {
						$item->updateTime->set_value(date('Y-m-d H:i:s'));
					} else {
						$item->updateTime = date('Y-m-d H:i:s');
					}

					$item->insert(false, true);
				}
				unset($item);
				unset($check);
			}
		}


		$this->module_alert->set_message('上傳成功');
		$this->module_go->back();
	}
}
