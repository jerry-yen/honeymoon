<?php
class Component
{

	/**
	 * @var 欄位中文名稱
	 */
	protected $name;

	/**
	 * @var 變數名稱
	 */
	protected $variable;

	/**
	 * @var 欄位型態
	 */
	protected $type;

	/**
	 * @var 檢查元素
	 */
	protected $element;

	/**
	 * @var 預設值
	 */
	protected $default;

	/**
	 * @var 提示
	 */
	protected $tip;

	/**
	 * @var 值
	 */
	protected $value;

	/**
	 * @var 系統設定值
	 */
	protected $config;

	/**
	 * @var 所屬模組
	 */
	protected $module;

	/**
	 * @var 欄位所屬的物件(可用於與其他欄位溝通)
	 */
	protected $item;

	/**
	 * @var 正在執行的 Controller
	 */
	protected $controller;

	/**
	 * @var 驗證錯誤訊息
	 */
	protected $valid_error_message = "";

	/**
	 * 建構子
	 * @param Object $value 欄位值
	 * @param Array $fieldMetadata 欄位元資訊
	 * @param MDL_Item 欄位所屬的物件 (可用來取得欄位值與取得其他欄位的值)
	 */
	public function __construct($value, $fieldMetadata, &$item)
	{
		$this->controller = Base_Controller::get_instance();
		$this->item = $item;
		$this->config = $this->controller->configs;
		$this->value = $value;
		$this->name = $fieldMetadata["name"];
		$this->variable = $fieldMetadata["variable"];
		$this->type = $fieldMetadata["type"];
		$this->element = $fieldMetadata["element"];
		$this->default = $fieldMetadata["default"];
		$this->tip = $fieldMetadata["tip"];
		$this->list = (isset($fieldMetadata["list"])) ? $fieldMetadata["list"] : "N";
	}

	/**
	 * 欄位名稱
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * 欄位變數
	 */
	public function get_variable()
	{
		return $this->variable;
	}

	/**
	 * 欄位元素
	 */
	public function get_element()
	{
		return $this->element;
	}

	/**
	 * 欄位預設值
	 */
	public function get_default()
	{
		return $this->default;
	}

	public function set_default($default)
	{
		$this->default = $default;
	}

	/**
	 * 欄位提示字
	 */
	public function get_tip()
	{
		return $this->tip;
	}

	public function set_item(&$item)
	{
		$this->item = $item;
	}

	/**
	 * 欄位值
	 * (如為空值，則回傳預設值)
	 */
	public function get_value()
	{

		$value = $this->value;
		if (!isset($value) || is_null($value) || $value == "") {
			$value = $this->get_default();
		}
		return $value;
	}

	/**
	 * 更新欄位值，同時依 Element 的設定來過濾資料
	 * @param Variable $value (不限型態)
	 */
	public function set_value($value)
	{

		if (!isset($value) || is_null($value) || $value == "") {
			$value = $this->get_default();
		}

		// 格式過濾
		$filter = $this->controller->module_filter;

		$allow = array();
		if (preg_match("/filter-allow{(.*?)};/", $this->element, $param)) {
			$allow = explode(",", $param[1]);
		}

		$value = $filter->filter_allow($value, $allow);

		// 更新值到物件的屬性
		$this->value = $value;

		// 格式驗證
		$validator = $this->controller->module_validation;

		if (preg_match("/required;/", $this->element) && !$validator->hasValue($value)) {
			$this->valid_error_message = $this->name . "為必填欄位";
		}

		if (preg_match("/min-length{(\d+)};/", $this->element, $param) && !$validator->min_length($value, $param[1])) {
			$this->valid_error_message = $this->name . "長度必需大於{$param[1]}個字";
		}

		if (preg_match("/max-length{(\d+)};/", $this->element, $param) && !$validator->max_length($value, $param[1])) {
			$this->valid_error_message = $this->name . "長度必需小於{$param[1]}個字";
		}

		if (preg_match("/email;/", $this->element) && !$validator->email($value)) {
			$this->valid_error_message = $this->name . "並不符合信箱格式";
		}

		if (preg_match("/no-repeat{code:(.*?)};/", $this->element, $param)) {
			$code = $param[1];
			$items = $this->controller->get_items($code, array('id != ?', $this->variable . '=?'), array($this->item->id, $this->value));
			if(count($items) > 0){
				$this->valid_error_message = '這個' . $this->name . "已被使用";
			}
			
		}
	}

	/**
	 * Radio, Select, Checkbox 等元件的值與顯示的資料可能不相同
	 * 如顯示原始值請使用 get_value(), 如要顯示文字資料則顯示 get_title()
	 */
	public function get_title()
	{
		return $this->get_value();
	}

	/**
	 * 驗證錯誤訊息
	 */
	public function get_valid_error_message()
	{
		return $this->valid_error_message;
	}

	public function is_group_start()
	{
		return false;
	}

	public function is_group_end()
	{
		return false;
	}

	/**
	 * 顯示欄位元件(TextBox, Select…等)
	 * @param Array $attributes 元件的HTML屬性值
	 */
	public function render($attributes = array())
	{
	}

	/**
	 * 顯示欄位會用到的Javascript
	 */
	public function script()
	{
	}

	/**
	 * 是否有標題(群組標籤就沒有標題)
	 */
	public function has_title()
	{
		return true;
	}

	/**
	 * 是否為必填欄位
	 */
	public function is_required()
	{
		return preg_match("/required;/", $this->element);
	}

	public function redata($value)
	{
		$this->set_value($value);
	}

	/**
	 * 取得資料集
	 * @return Array $datas
	 */
	protected function get_data_source()
	{

		$datas = array();
		$params = array();

		if (preg_match("/data-source{(.*?)};/", $this->element, $element)) {

			if (preg_match("/with{(.*?)};/", $this->element, $with_element)) {
				$parts = explode(",", $with_element[1]);
				foreach ($parts as $key => $part) {
					$pairs = explode(':', $part);
					$params[$pairs[0]] = $pairs[1];
				}
			}

			$source_string = $element[1];
			$sources = explode(",", $source_string);
			foreach ($sources as $key => $source) {

				$data = explode(":", $source);
				// 載入指定模組類型的模組名稱
				if ($data[0] == "module_type") {
					$module_action = $this->controller->module_dao->get_object("module");
					$types = isset($data[1]) ? $data[1] : '';
					$modules = $module_action->get_modules(explode('/', $types));
					foreach ($modules as $module) {
						$datas[] = array($module->code, $module->title);
					}
				}
				// 載入模組的項目清單
				else if ($data[0] == "module_class") {
					$module_action = $this->controller->module_dao->get_object("module");
					$module_action->get_module($data[1]);
					$classes = $module_action->get_classes();
					foreach ($classes as $class) {
						$datas[] = array($class->id, $class->title->get_title());
					}
				}
				// 載入模組的項目清單
				else if ($data[0] == "module_recursive") {
					$module_action = $this->controller->module_dao->get_object("module");
					$module_action->get_module($data[1]);
					$classes = $module_action->get_all_classes();

					$records = array();
					// 分類已經有依據層數小到大排序過了
					foreach ($classes as $class) {
						if ($class->parentId != "") {
							$records[$class->id] = $records[$class->parentId] . "/" . $class->title->get_title();
						} else {
							$records[$class->id] = "/" . $class->title->get_title();
						}

						$datas[$class->id] = array($class->id, $records[$class->id]);
					}
				}
				// 載入模組的項目清單
				else if ($data[0] == "module_lastlevelclass") {
					$module_action = $this->controller->module_dao->get_object("module");
					$module_action->get_module($data[1]);
					$classes = $module_action->get_all_classes();

					$records = array();
					// 分類已經有依據層數小到大排序過了
					foreach ($classes as $class) {
						if ($class->parentId != "") {
							$records[$class->id] = $records[$class->parentId] . "/" . $class->title->get_title();
						} else {
							$records[$class->id] = "/" . $class->title->get_title();
						}

						$datas[$class->id] = array($class->id, $records[$class->id]);
					}

					$flag = false;
					do {
						$flag = false;
						foreach ($classes as $class) {
							if ($class->parentId != "" && isset($records[$class->parentId])) {
								unset($records[$class->parentId]);
								unset($datas[$class->parentId]);
								$flag = true;
							}
						}
					} while ($flag);
				}
				// 載入模組的項目清單
				else if ($data[0] == "module_onlylevelclass") {
					$module_action = $this->controller->module_dao->get_object("module");
					$module_action->get_module($data[1]);
					$onlylevel = $data[2];
					$classes = $module_action->get_all_classes();

					$records = array();
					// 分類已經有依據層數小到大排序過了
					foreach ($classes as $class) {
						if ($class->parentId != "") {
							$records[$class->id] = $records[$class->parentId] . "/" . $class->title->get_title();
						} else {
							$records[$class->id] = "/" . $class->title->get_title();
						}

						$datas[$class->id] = array($class->id, $records[$class->id]);
					}

					foreach ($datas as $key => $data) {
						$parts = explode('/', $data[1]);
						if (count($parts) != $onlylevel + 1) {
							unset($datas[$key]);
						}
					}
				}

				// 載入模組的項目清單
				else if ($data[0] == "module_item") {
					$module_action = $this->controller->module_dao->get_object("module");
					$module_action->get_module($data[1]);

					if (isset($params['field']) && isset($params['sort'])) {
						$items = $module_action->get_items(array(), array(), array($params['field'] . ' ' . $params['sort']));
					} else {
						$items = $module_action->get_items();
					}

					foreach ($items as $item) {
						if (isset($params['field'])) {
							$datas[$item->id] = array($item->id, $item->{$params['field']}->get_title() . ' ' . $item->title->get_title());
						} else {
							$datas[$item->id] = array($item->id, $item->title->get_title());
						}
					}
				}
				// 一般在 Element 指定固定資料
				else {
					$datas[] = $data;
				}
			}
		}
		return $datas;
	}
}
