<?php

/**

 * 管理者控制器

 * 自動驗證是否登入

 */

class AdminController extends DomainController
{

	protected $mod_login;

	protected $menus = array();

	protected $permissions = array("all");

	protected $languages = array();

	/**
	 * 如每頁都會執行到的程式碼，可放在此函式
	 */
	public function global_code()
	{

		parent::global_code();
		@session_start();
		if (!isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"])) {
			$_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"] = "";
		}
		if (!isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"])) {
			$_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"] = "";
		}
		@session_write_close();

		$this->mod_login = $this->module_dao->get_object("module");
		$this->mod_login->get_module("login");

		$this->module_loader->load("showbox");


		// 非登入頁，需要驗證是否已登入
		$base_file = basename($this->config_full_url);
		$base_file = explode("?", $base_file);
		$base_file = $base_file[0];

		if ($base_file != "login.php") {

			@session_start();
			if (!isset($_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"]) || $_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"] == "") {
				$this->module_showbox->set_message("抱歉，您可能尚未登入 或 閒置太久！請重新登入！");
				$this->module_go->page("/admin");
			}
			@session_write_close();
		}
		@session_start();

		if ($_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"] != "super-admin") {

			$module = $this->module_dao->get_object("module");
			$module->get_module($_SESSION[$_SERVER["HTTP_HOST"] . "-login-module-code"]);
			if ($module->is_exists()) {

				$id = $_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"];
				$item = $module->get_single_item(array('id=?'), array($id), array());
				$this->mod_login = $item;
				if (isset($item->permission)) {
					$param_code = $item->permission->get_value();
					$this->permissions = explode(":", $param_code);
				}
				/*
				$param_code = $module -> permission;
				$module -> get_module($param_code);
				if($module -> is_exists()){
					$this -> permissions = explode(":", $module -> manager);
				}
				*/
			}
		}

		$this->mod_language = $this->module_dao->get_object("module");
		$this->languages = $this->mod_language->get_modules("Language");

		@session_write_close();
		$this->load_menu();
	}

	/**
	 * 載入選單
	 */
	private function load_menu()
	{
		$mod_menu = $this->module_dao->get_object("module");
		$mod_menu->get_module("menu");

		$this->menus = ($mod_menu->menu == "") ? array() : $mod_menu->menu;
		// print_r($this -> menus);
		foreach ($this->menus as $menu_key => $menu) {
			foreach ($menu->items as $item_key => $item) {
				$module = $this->module_dao->get_object("module");
				$module->get_module($item);

				if ($module->is_exists() && (in_array($item, $this->permissions) || $this->permissions == array("all"))) {
					$menu->items[$item_key] = $module;
				} else {
					unset($menu->items[$item_key]);
				}
			}
			$this->menus[$menu_key] = $menu;
		}
	}

	/**
	 * 取得分類
	 * @param string $module_code 模組代碼
	 * @param string $id 項目識別碼
	 * @return MDL_Item 項目
	 */
	public function get_single_class($module_code, $id, $where = array(), $values = array(), $sort = array())
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		$where[] = "id=?";
		$values[] = $id;

		$class = $this->module->get_single_class($where, $values, $sort);

		if ($class->level == $this->module->class_field_use_level) {
			$class = $this->module->get_single_class($where, $values, $sort, true);
		}
		/*
		if(!$class -> is_exists()){
			$this -> module_alert -> set_message("查無分類");
			$this -> module_go -> back();
		}
		 */
		return $class;
	}

	/**
	 * 取得項目
	 * @param string $module_code 模組代碼
	 * @return MDL_Item 項目
	 */
	public function get_items($module_code, $where = array(), $values = array(), $sort = array("topTime DESC", "sortTime ASC", "sequence ASC", "createTime DESC"), $count_per_page = 0)
	{
		$module = $this->module_dao->get_object("module");
		$module->get_module($module_code);
		if (!$module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		if ($count_per_page > 0) {
			$this->module_pagination->set_count_per_page($count_per_page);
			$this->module_pagination->unlock();
		}
		$items = $module->get_items($where, $values, $sort);
		if ($count_per_page > 0) {
			$this->module_pagination->lock();
		}

		return $items;
	}

	/**
	 * 取得項目
	 * @param string $module_code 模組代碼
	 * @param string $id 項目識別碼
	 * @return MDL_Item 項目
	 */
	public function get_single_item($module_code, $id, $where = array(), $values = array(), $sort = array())
	{
		$module = $this->module_dao->get_object("module");
		$module->get_module($module_code);
		if (!$module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		$where[] = "id=?";
		$values[] = $id;
		$item = $module->get_single_item($where, $values, $sort);

		if (!$item->is_exists()) {
			$item->moduleId = $module->id;
			// $this -> module_alert -> set_message("查無項目");
			// $this -> module_go -> back();
		}


		return $item;
	}

	/**
	 * 取得項目
	 * @param string $module_code 模組代碼
	 * @param string $id 項目識別碼
	 * @return MDL_Item 項目
	 */
	public function get_single_no_id_item($module_code, $where = array(), $values = array(), $sort = array())
	{
		$module = $this->module_dao->get_object("module");
		$module->get_module($module_code);
		if (!$module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		$item = $module->get_single_item($where, $values, $sort);

		if (!$item->is_exists()) {
			$item->moduleId = $module->id;
			// $this -> module_alert -> set_message("查無項目");
			// $this -> module_go -> back();
		}


		return $item;
	}

	/**
	 * 後台載入面版
	 */
	public function load_view()
	{

		$path = str_replace($this->configs["full_root_path"] . "/admin", "", $this->configs["full_execute_php_path"]);
		$view_path = $this->configs["full_view_path"] . "/admin/" . $this->configs["admin_theme"] . $path;
		$theme_path = $this->configs["machine_relative_view_path"] . "/admin/" . $this->configs["admin_theme"];

		ob_start();
		include($view_path);
		$content = ob_get_contents();
		ob_end_clean();

		$content = preg_replace("/<(link|script|img|td)(.*?)(src|href|background)=\"([^\/].*?)\"/", "<$1$2$3=\"{$theme_path}/$4\"", $content);
		$content = preg_replace("/url\('([^\/].*?)'\)/s", "url('{$theme_path}/$1')", $content);
		$content = str_replace("{$theme_path}/http://", "http://", $content);
		$content = str_replace("{$theme_path}/https://", "https://", $content);
		$content = str_replace("http://{$_SERVER["HTTP_HOST"]}http://", "http://", $content);

		echo $content;
	}
}
