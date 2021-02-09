<?php

require_once(__DIR__ . "/MDL_FieldValueHandler.php");

class MDL_Universal extends MDL_FieldValueHandler
{

	/**
	 * 新增分類
	 * @param MDL_Class $class
	 * @return string $id
	 */
	public function add_class(&$class)
	{
		$class->moduleId = $this->moduleId;
		$class->parentId = $this->id;
		$class->level = $this->level + 1;
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
		$where[] = "parentId=?";
		$values[] = $this->id;

		$where[] = "moduleId=?";
		$values[] = $this->module->id;
		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])) {
			$where[] = "langId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"];
		}
		@session_write_close();
		return $this->get_objects($this->module->code, $where, $values, $sort, $this->module, $this->Item_Field_Metadata_Type);
	}

	/**
	 * 取得父分類
	 * @return MDL_Classes $classes
	 */
	public function get_parent($where = array(), $values = array(), $sort = array())
	{
		$where[] = "id=?";
		$values[] = $this->parentId;

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])) {
			$where[] = "langId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"];
		}
		@session_write_close();

		return $this->get_object($this->module->code . "_class", $where, $values, $sort, $this->module, $this->Class_Field_Metadata_Type);
	}

	/**
	 * 取得子分類
	 * @return MDL_Classes $classes
	 */
	public function get_sub_classes($where = array(), $values = array(), $sort = array("sortTime ASC", "sequence ASC", "createTime DESC"))
	{
		$where[] = "parentId=?";
		$values[] = $this->id;

		$where[] = "moduleId=?";
		$values[] = $this->module->id;
		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"])) {
			$where[] = "langId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-language"];
		}
		@session_write_close();
		return $this->get_objects($this->module->code . "_class", $where, $values, $sort, $this->module, $this->Class_Field_Metadata_Type);
	}

	/**
	 * 新增項目
	 * @param MDL_Item $item
	 * @return string $id
	 */
	public function add_item(&$item)
	{
		$item->moduleId = $this->moduleId;
		$item->parentId = $this->id;
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
		$item->id = $new_id;
		return $new_id;
	}

	/**
	 * 新增檔案
	 */
	public function add_file($file)
	{
		$file->itemId = $this->id;
		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$file->domainId = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();
		return $file->insert(false, true);
	}

	public function get_images()
	{
		$where[] = "itemId=?";
		$values[] = $this->id;

		$where[] = "classType=?";
		$values[] = "Image";

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		return $this->get_objects("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"), $this->module);
	}
	public function get_single_image($variable)
	{
		$where[] = "itemId=?";
		$values[] = $this->id;

		$where[] = "classType=?";
		$values[] = "Image";

		$where[] = "variable=?";
		$values[] = $variable;

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		return $this->get_object("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"), $this->module);
	}
	public function get_files()
	{
		$where[] = "itemId=?";
		$values[] = $this->id;

		$where[] = "classType=?";
		$values[] = "File";

		@session_start();
		if (isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])) {
			$where[] = "domainId=?";
			$values[] = $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"];
		}
		@session_write_close();

		return $this->get_objects("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"), $this->module);
	}
	public function get_single_file($variable)
	{
	}

	public function get_href($file_name, $mode = 2)
	{
		$url = "";
		// 標題使用
		$title = preg_replace("/[\<\>\"\#\&\%\{\}\|\^~\[\]`\?\/]/is", "", $this->title->get_value());

		switch ($mode) {
			case 1:
				$url = $file_name . "_info.php?id=" . $this->id . "&t=" . $title;
				break;
			case 2:
				$url = $file_name . "/" . $this->id . "/" . $title . ".html";
		}

		return $url;
	}


	public function delete()
	{

		$controller = Base_Controller::get_instance();

		// 刪除相關關連

		// 刪除相關圖片
		$files = $this->get_images();
		foreach ($files as $file) {
			$resizes = explode(",", $file->resize);
			foreach ($resizes as $resize) {
				$dim = explode("x", $resize);
				$width = $dim[0];
				$height = $dim[1];
				$controller->module_image->delete($file->path, $width, $height);
			}

			$controller->module_image->delete($file->path);
			$file->delete();
		}

		// 刪除相關檔案
		$files = $this->get_files();
		foreach ($files as $file) {
			$controller->module_image->delete($file->path);
			$file->delete();
		}

		// 如底下仍有分類
		$classes = $this->get_sub_classes();
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
