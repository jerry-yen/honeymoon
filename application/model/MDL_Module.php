<?php
require_once(__DIR__ . "/MDL_FieldValueHandler.php");

class MDL_Module extends MDL_FieldValueHandler
{

	/**
	 * 取得模組
	 */
	public function get_module($code)
	{
		$where[] = "code=?";
		$values[] = $code;
		$module = $this->single_query($where, $values);
		if (!$module->is_exists()) {
			return;
		}
		$extend = explode("*", $code);
		// 共用資料表
		// 需將原本被共用的模組識別碼移過來，否則會無法搜尋得到 moduleId 為原始ID的資料！資料就無法共用
		if (count($extend) > 1) {
			$code = $extend[0];
			$source_module = $this->single_query(array("code=?"), array($code));
			if ($source_module->is_exists()) {
				$module->id = $source_module->id;
			}
		}

		$this->extend_field_value($module);
		$this->load_data($module->to_array());
	}

	/**
	 * 取得模組
	 */
	public function get_modules($moduleType = array())
	{
		$where = array();
		$values = array();
		if (is_array($moduleType)) {
			if ($moduleType == array()) {
				$where = array();
				$values = array();
			} else {
				$where[] = 'moduleType IN ("' . implode('","', $moduleType) . '")';
			}
		} else {
			$where[] = "moduleType=?";
			$values[] = $moduleType;
		}
		$modules = $this->query($where, $values);
		foreach ($modules as $key => $module) {
			$this->extend_field_value($module);
			$modules[$key] = $module;
		}
		return $modules;
	}

	/**
	 * 新增項目
	 * @param MDL_Item $item
	 * @return string $id
	 */
	public function add_item(&$item)
	{
		$item->moduleId = $this->id;
		if (!isset($item->createTime)) {
			$item->createTime = date("Y-m-d H:i:s");
		}
		if (!isset($item->updateTime)) {
			$item->updateTime = date("Y-m-d H:i:s");
		}
		if (!isset($item->topTime)) {
			$item->topTime = "0000-00-00 00:00:00";
		}

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$item->domainId = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])) {
			$item->langId = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"];
		}
		@session_write_close();

		if ($item->is_exists()) {
			$item->insert(false, false);
			$new_id = $item->id;
		} else {
			$new_id = $item->insert(false, true);
		}
		return $new_id;
	}

	/**
	 * 新增分類
	 * @param MDL_Class $class
	 * @return string $id
	 */
	public function add_class(&$class)
	{
		$class->moduleId = $this->id;
		$class->level = 1;

		if (!isset($class->createTime)) {
			$class->createTime = date("Y-m-d H:i:s");
		}
		if (!isset($class->updateTime)) {
			$class->updateTime = date("Y-m-d H:i:s");
		}

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$class->domainId = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])) {
			$class->languageId = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"];
		}
		@session_write_close();


		if ($class->is_exists()) {
			$class->insert(false, false);
			$new_id = $class->id;
		} else {
			$new_id = $class->insert(false, true);
		}
		return $new_id;
	}

	/**
	 * 搜尋項目
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 */
	public function get_items($where = array(), $values = array(), $sort = array("topTime DESC", "sortTime ASC", "sequence ASC", "createTime DESC"))
	{
		$where[] = "moduleId=?";
		$values[] = $this->id;
		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();
		return $this->get_objects($this->code, $where, $values, $sort, $this, $this->Item_Field_Metadata_Type);
	}

	/**
	 * 搜尋單一筆項目
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 */
	public function get_single_item($where = array(), $values = array(), $sort = array())
	{
		$where[] = "moduleId=?";
		$values[] = $this->id;

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		return $this->get_object($this->code, $where, $values, $sort, $this, $this->Item_Field_Metadata_Type);
	}

	/**
	 * 取得一個空項目
	 */
	public function get_empty_item()
	{
		$item = $this->get_empty_object($this->code, $this, $this->Item_Field_Metadata_Type);
		return $item;
	}

	/**
	 * 搜尋分類
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 */
	public function get_classes($where = array(), $values = array(), $sort = array("sortTime ASC", "sequence ASC", "createTime DESC"), $is_special_class = false)
	{
		// 此函式屬於模組，所以由模組取得的目錄「必定」是第一層
		$where[] = "level=?";
		$values[] = 1;

		$where[] = "moduleId=?";
		$values[] = $this->id;

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		if ($is_special_class) {
			$classes = $this->get_objects($this->code . "_class", $where, $values, $sort, $this, $this->Class_Special_Field_Metadata_Type);
		} else {
			$classes = $this->get_objects($this->code . "_class", $where, $values, $sort, $this, $this->Class_Field_Metadata_Type);
		}

		return $classes;
	}

	/**
	 * 搜尋分類
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 */
	public function get_all_classes($where = array(), $values = array(), $sort = array("level ASC", "sortTime ASC", "sequence ASC", "createTime DESC"))
	{

		$where[] = "moduleId=?";
		$values[] = $this->id;

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		$classes = $this->get_objects($this->code . "_class", $where, $values, $sort, $this, $this->Class_Field_Metadata_Type);
		return $classes;
	}

	/**
	 * 搜尋分類
	 * @param Array $where 條件
	 * @param Array $values 條件值 (避免SQL Injection)
	 * @param Array $sort 排序條件
	 */
	public function get_single_class($where = array(), $values = array(), $sort = array(), $is_special_class = false)
	{
		$where[] = "moduleId=?";
		$values[] = $this->id;

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		if ($is_special_class) {
			return $this->get_object($this->code . "_class", $where, $values, $sort, $this, $this->Class_Special_Field_Metadata_Type);
		} else {
			return $this->get_object($this->code . "_class", $where, $values, $sort, $this, $this->Class_Field_Metadata_Type);
		}
	}

	/**
	 * 取得一個空項目分類
	 */
	public function get_empty_class($is_special_class = false)
	{

		if ($is_special_class) {
			return $this->get_empty_object($this->code . "_class", $this, $this->Class_Special_Field_Metadata_Type);
		} else {
			return $this->get_empty_object($this->code . "_class", $this, $this->Class_Field_Metadata_Type);
		}
	}

	/**
	 * 取得網域元件
	 */
	public function get_domain()
	{

		$where[] = "domain=?";
		$values[] = $_SERVER["HTTP_HOST"];

		return $this->get_object($this->code, $where, $values, array(), $this, $this->Item_Field_Metadata_Type);
	}

	public function delete()
	{
		// 如底下仍有分類
		$classes = $this->get_classes();
		foreach ($classes as $class) {
			$class->delete();
		}

		// 如底下仍有項目
		$items = $this->get_items();
		foreach ($items as $item) {
			$item->delete();
		}

		parent::delete();
	}
}
