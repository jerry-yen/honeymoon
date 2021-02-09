<?php

/**
 * 使用者控制器
 * 自動驗證是否登入
 */
class UserController extends DomainController
{

	protected $information;
	protected $member_identity = '';
	protected $footer = '';
	protected $check_information;

	public function global_code()
	{
		$this -> information = $this -> get_item("information");
		$this -> check_information = $this -> get_item("check_info");

		@session_start();
		if (isset($_SESSION['identity'])) {
			$this->member_identity = $_SESSION['identity'];
		}
		@session_write_close();

		$this->footer = $this->get_item('footer');
	}

	/**	
	 * 取得所有分類(不分層)
	 * @param string $module_code 模組代碼
	 * @return MDL_Item 項目
	 */
	public function get_all_classes($module_code, $where = array(), $values = array(), $sort = array("sortTime ASC", "sequence ASC", "createTime DESC"), $count_per_page = 0)
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		if ($count_per_page > 0) {
			$this->module_pagination->set_count_per_page($count_per_page);
			$this->module_pagination->unlock();
		}

		$classes = $this->module->get_all_classes($where, $values, $sort);
		if ($count_per_page > 0) {
			$this->module_pagination->lock();
		}



		if (count($classes) > 0) {

			if ($classes[0]->level == $this->module->class_field_use_level) {

				if ($count_per_page > 0) {
					$this->module_pagination->set_count_per_page($count_per_page);
					$this->module_pagination->unlock();
				}
				$classes = $this->module->get_all_classes($where, $values, $sort, true);
				if ($count_per_page > 0) {
					$this->module_pagination->lock();
				}
			}
		}
		return $classes;
	}

	/**
	 * 取得分類
	 * @param string $module_code 模組代碼
	 * @return MDL_Item 項目
	 */
	public function get_classes($module_code, $where = array(), $values = array(), $sort = array("sortTime ASC", "sequence ASC", "createTime DESC"), $count_per_page = 0)
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		if ($count_per_page > 0) {
			$this->module_pagination->set_count_per_page($count_per_page);
			$this->module_pagination->unlock();
		}

		$classes = $this->module->get_classes($where, $values, $sort);
		if ($count_per_page > 0) {
			$this->module_pagination->lock();
		}



		if (count($classes) > 0) {

			if ($classes[0]->level == $this->module->class_field_use_level) {

				if ($count_per_page > 0) {
					$this->module_pagination->set_count_per_page($count_per_page);
					$this->module_pagination->unlock();
				}
				$classes = $this->module->get_classes($where, $values, $sort, true);
				if ($count_per_page > 0) {
					$this->module_pagination->lock();
				}
			}
		}
		return $classes;
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
	 * 取得模版
	 * @param string $module_code 模組代碼
	 * @param string $id 項目識別碼
	 * @return MDL_Item 項目
	 */
	public function get_template($module_code)
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		return $this->module;
	}

	/**
	 * 取目前導覽路徑
	 * @param string $module_code 模組代碼
	 * @param string $classId 指定從哪一層的分類開始往上推出導覽路徑
	 * @return Array $navigation
	 */
	public function get_navigation($module_code, $classId, $delete_last = false)
	{
		$navigation = array();

		$parentId = $classId;

		do {
			$class = $this->get_single_class($module_code, $parentId);

			if (!$class->is_exists()) {
				break;
			}

			$navigation[] = $class;
			$parentId = $class->parentId;
		} while ($class->parentId != "");

		if ($delete_last) {
			unset($navigation[0]);
		}
		return array_reverse(array_values($navigation));
	}

	/**
	 * 取得項目
	 * @param string $module_code 模組代碼
	 * @return MDL_Item 項目
	 */
	public function get_items($module_code, $where = array(), $values = array(), $sort = array("topTime DESC", "sortTime ASC", "sequence ASC", "createTime DESC"), $count_per_page = 0)
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		if ($count_per_page > 0) {
			$this->module_pagination->set_count_per_page($count_per_page);
			$this->module_pagination->unlock();
		}
		$items = $this->module->get_items($where, $values, $sort);
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
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		$where[] = "id=?";
		$values[] = $id;
		$item = $this->module->get_single_item($where, $values, $sort);

		if (!$item->is_exists()) {
			$item->moduleId = $this->module->id;
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
	 * 取得項目
	 * @param string $module_code 模組代碼
	 * @return MDL_Item 項目
	 */
	public function get_item($module_code, $where = array(), $values = array(), $sort = array())
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);

		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		$item = $this->module->get_single_item($where, $values, $sort);

		if (!$item->is_exists()) {
			$this->module_alert->set_message("查無項目");
			$this->module_go->back();
		}

		return $item;
	}

	/**
	 * 取得項目
	 * @param string $module_code 模組代碼
	 * @return MDL_Item 項目
	 */
	public function initial($module_code)
	{
		$this->module = $this->module_dao->get_object("module");
		$this->module->get_module($module_code);
		if (!$this->module->is_exists()) {
			$this->module_alert->set_message("模組錯誤");
			$this->module_go->back();
		}

		return $this->module->get_empty_item();
	}

	/**
	 * 儲存至資料庫
	 */
	public function save_to_db(&$item, $show_message = true)
	{
		if ($this->module == array()) {
			return;
		}

		$message = "";
		foreach ($this->module->fieldMetadata as $metadata) {
			if ($metadata->fieldMetadata_field_variable != "cart") {
				$item->{$metadata->fieldMetadata_field_variable}->set_value(nl2br($this->module_io->{$metadata->fieldMetadata_field_variable}));
				if ($item->{$metadata->fieldMetadata_field_variable}->get_valid_error_message() != "") {
					$message .= $item->{$metadata->fieldMetadata_field_variable}->get_valid_error_message() . "\\r\\n";
				}
			}
		}

		if ($message != "") {
			// 客製化
			@session_start();
			if (isset($_SESSION["LANG"]) && $_SESSION["LANG"] == "_en") {
				$message = str_replace("公司名稱", "Company", $message);
				$message = str_replace("聯絡電話", "TEL", $message);
				$message = str_replace("聯絡人", "Contact Person", $message);
				$message = str_replace("地址", "Address", $message);
				$message = str_replace("姓名", "Name", $message);
				$message = str_replace("傳真", "Fax", $message);
				$message = str_replace("國籍", "Country", $message);
				$message = str_replace("主旨", "Subject", $message);
				$message = str_replace("聯絡信箱", "Email", $message);
				$message = str_replace("留言", "Message", $message);
				$message = str_replace("為必填欄位", " is required", $message);
				$message = str_replace("並不符合信箱格式", " is invalid", $message);
			}
			@session_write_close();
			$this->module_alert->set_message($message);
			return false;
		} else {
			$this->module->add_item($item);

			if ($show_message) {

				@session_start();
				if (isset($_SESSION["LANG"]) && $_SESSION["LANG"] == "_en") {
					$this->module_alert->set_message("Thanks for your message, we will get back to you as soon as possible.");
				} else {
					$this->module_alert->set_message($this->module->success);
				}
				@session_write_close();
			}
			return true;
		}
	}

	/**
	 * 發送信件
	 */
	public function send_mail(&$item, $user_email = array())
	{
		if ($this->module == array()) {
			return;
		}

		$mod_website = $this->module_dao->get_object("module");
		$mod_website->get_module("information");
		$website = $mod_website->get_single_item();

		$mail_module = $this->module_dao->get_object("module");
		$mail_module->get_module("mailbox");

		$adminEmails = explode(",", $website->email->get_value());
		$firstAdminEmail = $adminEmails[0];

		$this->module_mailbox->load_config(array(
			"publish"       => $mail_module->smtp_open,          	// 是否啟用 SMTP
			"secure"        => $mail_module->smtp_secure,        	// SMTP 安全協定
			"host"          => $mail_module->smtp_host,  			// SMTP主機位址
			"port"          => $mail_module->smtp_port,         	// SMTP Port
			"auth"          => $mail_module->smtp_verify,         // 是否做登入驗證
			"user"          => $mail_module->smtp_account,       	// 登入帳號
			"password"      => $mail_module->smtp_password,     	// 密碼
			"from_address"  => $firstAdminEmail,	// 寄件信箱
			"from_name"     => $website->title->get_value(),   	// 寄件名稱
			"reply_address" => $firstAdminEmail,	// 回覆信箱
		));


		$subject = $this->module->subject;
		$content = $this->module->mail_content;

		foreach ($this->module->fieldMetadata as $metadata) {
			$field_name = $metadata->fieldMetadata_field_variable;
			if ($field_name != "cart") {
				$subject = str_replace("{{$field_name}}", $item->{$field_name}->get_value(), $subject);

				if ($field_name == "viewform") {
					$content = str_replace("{{$field_name}}", $item->{$field_name}->get_title(), $content);
				} else {
					$content = str_replace("{{$field_name}}", $item->{$field_name}->get_value(), $content);
				}
			}
		}

		if (isset($item->cart)) {
			$content = str_replace("{cart_list}", $item->cart->render(), $content);
		}

		$this->module_mailbox->set_subject($subject);
		$this->module_mailbox->set_content(html_entity_decode($content));

		if (count($user_email) > 0) {
			$this->module_mailbox->set_to($user_email);
			$this->module_mailbox->set_bcc($adminEmails);
		} else {
			$this->module_mailbox->set_to($adminEmails);
		}


		$this->module_mailbox->send();
	}

	/**
	 * 發送信件
	 */
	public function quick_send_mail($subject, $content, $user_email = array())
	{

		$mail_module = $this->module_dao->get_object("module");
		$mail_module->get_module("mailbox");

		$this->module_mailbox->load_config(array(
			"publish"       => ($mail_module->smtp_open != "Y") ? "N" : "Y",          	// 是否啟用 SMTP
			"secure"        => $mail_module->smtp_secure,        	// SMTP 安全協定
			"host"          => $mail_module->smtp_host,  			// SMTP主機位址
			"port"          => $mail_module->smtp_port,         	// SMTP Port
			"auth"          => $mail_module->smtp_verify,         // 是否做登入驗證
			"user"          => $mail_module->smtp_account,       	// 登入帳號
			"password"      => $mail_module->smtp_password,     	// 密碼
			"from_address"  => $mail_module->mail,				// 寄件信箱
			"from_name"     => $mail_module->mail_name,   		// 寄件名稱
			"reply_address" => $mail_module->mail_reply,			// 回覆信箱
		));

		$this->module_mailbox->set_subject($subject);
		$this->module_mailbox->set_content($content);

		$this->module_mailbox->set_to($user_email);


		$this->module_mailbox->send();
	}

	/**
	 * 簡訊發送 API
	 * 
	 * @param string $account 帳號
	 * @param string $password 密碼
	 * @param string $content 簡訊內容
	 * @param string $phone 手機號碼
	 * @param string $subject 主旨(optional)，簡訊廠商做記號用
	 * 
	 */
	public function send_sms($account, $password, $content, $phone, $subject = '')
	{
		$content = urlencode($content);
		$subject = urlencode($subject);

		$url = ("http://api.every8d.com/API21/HTTP/sendSMS.ashx?UID=$account&PWD=$password&MSG=$content&DEST=$phone&SB=$subject");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$results = explode(',', $result);
		return $results;
		/*
        return array(
            'credit' => $results[0],    // 剩餘點數
            'sended' => $results[1],    // 發送通數
            'cost' => $results[2],      // 本次發送扣餘的點數
            'unsend' => $results[3],    // 因額度不足而未發送的筆數
            'batch_id' => $results[4],  // 本次發送的識別碼
        );
        */
	}



	/**
	 * 新增產品至購物車
	 * @param string $item_id 產品識別碼
	 * @param int $count 數量 ( 詢價車預設為 1 )
	 * @param int $price 單價 ( 詢價車預設為0，因為價格不公開 )
	 */
	public function add_cart($item_id, $count = 1, $price = 0, $extValue = array())
	{

		$items = $this->get_cart_items();
		foreach ($items as $key => $item) {
			if ($item["EXT"] == json_encode($extValue) && $item_id == $item["ID"]) {
				$this->update_cart($key, $item_id, $count, $price, $extValue);
				return;
			}
		}

		$new_id = uniqid(true);
		@session_start();
		$_SESSION["HM_CART"][$new_id]["ID"] = $item_id;
		$_SESSION["HM_CART"][$new_id]["COUNT"] = $count;
		$_SESSION["HM_CART"][$new_id]["PRICE"] = $price;
		$_SESSION["HM_CART"][$new_id]["EXT"] = json_encode($extValue);
		@session_write_close();
	}

	/**
	 * 更新購物車中指定產品資訊
	 * @param string $id 購物車項目識別碼
	 * @param string $item_id 產品識別碼
	 * @param int $count 數量 ( 詢價車預設為 1 )
	 * @param int $price 單價 ( 詢價車預設為0，因為價格不公開 )
	 */
	public function update_cart($id, $item_id, $count = 1, $price = 0, $extValue = array())
	{
		@session_start();
		$_SESSION["HM_CART"][$id]["ID"] = $item_id;
		$_SESSION["HM_CART"][$id]["COUNT"] = $count;
		$_SESSION["HM_CART"][$id]["PRICE"] = $price;
		$_SESSION["HM_CART"][$id]["EXT"] = json_encode($extValue);
		@session_write_close();
	}

	/**
	 * 刪除購物車中指定產品資訊
	 * @param string $id 購物車項目識別碼
	 */
	public function delete_cart($id)
	{
		@session_start();
		unset($_SESSION["HM_CART"][$id]);
		@session_write_close();
	}

	/**
	 * 清除購物車
	 */
	public function clear_cart()
	{
		@session_start();
		unset($_SESSION["HM_CART"]);
		@session_write_close();
	}

	/**
	 * 取得購物車中指定產品資訊
	 * @param string $id 購物車項目識別碼
	 */
	public function get_cart_item($id)
	{
		@session_start();
		$item = $_SESSION["HM_CART"][$id];
		@session_write_close();
		$item["EXT"] = json_decode($item["EXT"]);
		return $item;
	}

	/**
	 * 目前購物車的件數
	 * @return int $count
	 */
	public function get_cart_count()
	{
		@session_start();
		if (!isset($_SESSION["HM_CART"])) {
			$_SESSION["HM_CART"] = array();
		}
		$count = count($_SESSION["HM_CART"]);
		@session_write_close();
		return $count;
	}

	public function get_cart_items()
	{
		@session_start();
		$items = (isset($_SESSION["HM_CART"])) ? $_SESSION["HM_CART"] : array();
		@session_write_close();
		return $items;
	}

	public function get_cart_total($shipping_fee = 0)
	{
		$total = 0;
		$items = $this->get_cart_items();
		foreach ($items as $item) {
			$total += $item["COUNT"] * $item["PRICE"];
		}
		return $total + $shipping_fee;
	}


	public function load_view()
	{

		$path = str_replace($this->configs["full_root_path"], "", $this->configs["full_execute_php_path"]);

		$view_in_root = true;

		if ($view_in_root) {
			$view_path = $this->configs["full_root_path"] . $path;
			$theme_path = $this->configs["machine_relative_root_path"];
		} else {

			if (isset($_GET["theme"])) {
				$this->configs["user_theme"] = $_GET["theme"];
			}

			$view_path = $this->configs["full_view_path"] . "/user/" . $this->configs["user_theme"] . $path;
			$theme_path = $this->configs["machine_relative_view_path"] . "/user/" . $this->configs["user_theme"];
		}

		@session_start();
		ob_start();
		include($view_path);
		$content = ob_get_contents();
		ob_end_clean();

		$content = preg_replace("/<(link|script|img|a)(.*?)(src|href)=\"([^#\/\?].*?)\"/", "<$1$2$3=\"{$theme_path}/$4\"", $content);
		// $content = preg_replace("/\/javascript:/","javascript:",$content);
		$content = preg_replace("/href=\".*?javascript:/", "href=\"javascript:", $content);
		$content = preg_replace("/href=\".*?mailto:/", "href=\"mailto:", $content);
		$content = preg_replace("/href=\".*?tel:/", "href=\"tel:", $content);

		$content = preg_replace("/url\('([^\/].*?)'\)/s", "url('{$theme_path}/$1')", $content);
		$content = str_replace("{$theme_path}/http://", "http://", $content);
		$content = str_replace("{$theme_path}/https://", "https://", $content);
		$content = str_replace("http://{$_SERVER["HTTP_HOST"]}http://", "http://", $content);
		$content = str_replace("https://{$_SERVER["HTTP_HOST"]}https://", "https://", $content);
		//$content = preg_replace("/window,document,'script','([^\/].*?)'/","window,document,'script','{$theme_path}/$1'",$content);
		// $this -> module_loader -> load("loadingblock");

		// 載入屏壁	
		// $content = $this -> module_loadingblock -> block_it($content);

		// HTML 內容壓縮
		//echo $this -> compress_html($content);
		echo $content;

		@session_write_close();
	}

	public function print_test($value)
	{
		echo "<!-- \r\n";
		print_r($value);
		echo " -->\r\n";
	}

	/** 
	 * 压缩html : 清除换行符,清除制表符,去掉注释标记 
	 * @param $string 
	 * @return压缩后的$string 
	 * */
	public function compress_html($string)
	{
		$string = str_replace("\r\n", '', $string); //清除换行符 
		$string = str_replace("\n", '', $string); //清除换行符 
		$string = str_replace("\t", '', $string); //清除制表符 
		return $string;
	}

	/**
	 * 取得分類(無論幾層的第一個分類)底下的第一個項目的圖片
	 */
	public function get_first_image($class, $module_code, $image_field_name)
	{
		$subs = $class->get_sub_classes(array("publish='Y'"));
		$last_class = $class;

		while (count($subs) > 0) {
			$last_class = $subs[0];
			$subs = $last_class->get_sub_classes(array("publish='Y'"));
		}

		$items = $last_class->get_items(array("publish='Y'"));


		if (count($items) > 0) {
			return $items[0]->{$image_field_name}->get_image()->get_file_path();
		} else {
			$module = $this->module_dao->get_object("module");
			$module->get_module($module_code);
			$empty = $module->get_single_item(array("FALSE"));
			return $empty->{$image_field_name}->get_image()->get_file_path();
		}
	}
}
