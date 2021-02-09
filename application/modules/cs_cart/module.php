<?php
/**
 * 購物車 模組
 */
class Cs_cart extends Base_Module {

	/**
	 * 將商品收進購物車
	 * 但需有產品基本欄位
	 * id 識別碼
	 * title 名稱
	 * price 價格
	 * count 數量
	 * img 圖片
	 * spec 規格
	 *
	 * @param array $cs_item 商品項目
	 * @param boolean $replace 相同商品是否覆蓋(更新)過去  (預設為 false)
	 * @return boolean $flag 是否加入成功
	 */
	public function checkin($cs_item = array(), $update = false) {
		@session_start();

		if(gettype($cs_item) == "object"){
			$cs_item = get_object_vars($cs_item);
		}


		if(!isset($cs_item["count"]) || $cs_item["count"] == 0){
			return false;
		}


		// 檢查是否重複
		foreach ($_SESSION["CS_CART"] as $key => $item) {
			$item = json_decode($item);
			if ($item == "") {
				continue;
			}


			if ($item -> id == $cs_item["id"]) {

				// 重複，需用複蓋的
				if (isset($item -> spec) && isset($cs_item["spec"])) {

					if($item -> spec == $cs_item["spec"] && $update){
						$_SESSION["CS_CART"][$key] = json_encode($cs_item);
						return true;
					}
					else {
						$_SESSION["CS_CART"][] = json_encode($cs_item);
						return true;
					}
				}
				else if ($update) {
					$_SESSION["CS_CART"][$key] = json_encode($cs_item);
					return true;
				} else {
					return false;
				}
			}

		}

		// 都沒有重複，直接新增
		$_SESSION["CS_CART"][] = json_encode($cs_item);
		@session_write_close();

		return true;
	}

	public function remove_item($id){
		@session_start();
		foreach ($_SESSION["CS_CART"] as $key => $item) {
			$item = json_decode($item);
			if ($item == "") {
				continue;
			}

			if ($id == $item -> id) {
				if ($spec != "") {
					if ($item -> spec == $spec) {
						unset($_SESSION["CS_CART"][$key]);
					} else {
						return array();
					}
				}

				unset($_SESSION["CS_CART"][$key]);
			}
		}
		@session_write_close();
	}

	/**
	 * 取得商品資訊
	 * @param string $id 商品識別碼
	 * @param string $spec 商品規格
	 * @return object $item
	 */
	public function get_item($id, $spec = "") {
		@session_start();
		foreach ($_SESSION["CS_CART"] as $key => $item) {
			$item = json_decode($item);
			if ($item == "") {
				continue;
			}

			if ($id == $item -> id) {
				if ($spec != "") {
					if ($item -> spec == $spec) {
						return $item;
					} else {
						return array();
					}
				}

				return $item;
			}
		}
		@session_write_close();
		return array();
	}
	
	/**
	 * 取得購物車中所有商品項目
	 * @param array $items
	 */
	public function get_items(){
		
		$items = array();
		
		@session_start();
		foreach ($_SESSION["CS_CART"] as $key => $item) {
			$item = json_decode($item);
			if ($item == "") {
				continue;
			}

			// 順便小計
			$item -> sum = $item -> price * $item -> count;
			$items[] = $item;
		}
		@session_write_close();
		
		return $items;
	}

	/**
	 * 目前購物車中的商品內容數量
	 * @return integer $count
	 */
	public function get_item_count() {
	
		$count = 0;

		@session_start();
		foreach ($_SESSION["CS_CART"] as $key => $item) {
			$item = json_decode($item);
			if ($item == "") {
				continue;
			}

			$count += (int)$item -> count;
		}
		@session_write_close();
		return $count;
	}
	
	/**
	 * 取得總額
	 * @param array $disc_or_fees 折扣或費用
	 * @example
	 * 
	 * $disc_or_fees = array(
	 * 	"運費" => 100,
	 * 	"會員優惠" => -50
	 * );
	 *
	 */
	public function get_total($disc_or_fees = array()){
		
		if(!is_array($disc_or_fees)){
			$disc_or_fees = array();
		}
		
		$total = 0;

		@session_start();
		foreach ($_SESSION["CS_CART"] as $key => $item) {
			$item = json_decode($item);
			if ($item == "") {
				continue;
			}

			$total += (int)($item -> count * $item -> price);
		}
		@session_write_close();
		
		foreach($disc_or_fees as $key => $price){
			$total += $price;
		}
		
		return $total;
	}
	
	

	/**
	 * 清空購物車
	 */
	public function clear() {
		@session_start();
		unset($_SESSION["CS_CART"]);
		unset($_SESSION["CS_CART_USER"]);
		unset($_SESSION["CS_CART_SHIPPING"]);
		unset($_SESSION["CS_CART_PAYMENT"]);
		@session_write_close();
	}
	
	/**
	 * 購物人資訊
	 * @param array $data
	 * @param boolean $is_success
	 */
	public function set_user_data($data = array()){
		
		if(gettype($data) == "object"){
			$data = get_object_vars($data);
		}

		$valid_message = "";
		foreach($data as $key => $value){
			$v = explode("*", $key);
			// 必填驗證
			if(count($v) == 2){
				if(!isset($value) || $value == ""){
					$valid_message .= "請輸入" . $v[0] . "\\r\\n";
				}
				else{
					$data[$v[1]] = $value;
				}
			}
			
		}
		if($valid_message != ""){
			$this -> controller -> module_alert -> set_message($valid_message);
			return false;
		}

		@session_start();
		$_SESSION["CS_CART_USER"] = $data;
		@session_write_close();
		
		return true;
	}
	
	/**
	 * 取得購物資訊
	 * @return array $data
	 */	
	public function get_user_data(){
		@session_start();
		$data = (isset($_SESSION["CS_CART_USER"])) ? $_SESSION["CS_CART_USER"] : array();
		@session_write_close();
		
		return (object)$data;
	}
	
	/**
	 * 取得購物資訊至IO模組
	 * @return boolean is_binded 是否有值綁在 IO 上
	 */
	public function get_user_data_to_io(){
		@session_start();
		$data = (isset($_SESSION["CS_CART_USER"])) ? $_SESSION["CS_CART_USER"] : array();
		foreach($data as $key => $value){
			$this -> controller -> module_io -> {$key} = $value;
		}
		@session_write_close();
		
		return (count($data) > 0);
	}

	public function set_shipping($shipping){
		@session_start();
		$_SESSION["CS_CART_SHIPPING"] = $shipping;
		@session_write_close();
	}

	public function get_shipping_text(){
		$shipping = $this -> get_shipping_value();

		switch($shipping){
			case "cod" : $text = "貨到付款"; break;
			case "unimart" : $text = "7-11 取貨付款"; break;
			case "fami" : $text = "全家取貨付款"; break;
			case "hilife" : $text = "萊爾富取貨付款"; break;
			case "unimart_p" : $text = "7-11 純取貨"; break;
			case "fami_p" : $text = "全家純取貨"; break;
			case "hilife_p" : $text = "萊爾富純取貨"; break;
			case "ship" : 
			default: $text = "黑貓宅配"; break;
		}

		return $text;
	}

	public function get_shipping_value(){
		@session_start();
		$shipping = $_SESSION["CS_CART_SHIPPING"];
		@session_write_close();

		return $shipping;
	}

	public function set_payment($payment){

		if(is_null($payment) || trim($payment) == ""){
			$this -> controller -> module_alert -> set_message("請輸入付款方式");
			return false;
		}

		@session_start();
		$_SESSION["CS_CART_PAYMENT"] = $payment;
		@session_write_close();

		return true;
	}

	public function get_payment_value(){
		@session_start();
		$payment = $_SESSION["CS_CART_PAYMENT"];
		@session_write_close();

		return $payment;
	}

	public function get_payment_text(){
		$payment = $this -> get_payment_value();

		switch($payment){
			case "ecpay_aio" : $text = "綠界金流 - (ATM, 信用卡, 超商代碼付款)"; break;
			case "credit" : $text = "信用卡轉帳"; break;
			case "cod" : $text = "貨到付款"; break;
			case "unimart" : $text = "7-11 取貨付款"; break;
			case "fami" : $text = "全家取貨付款"; break;
			case "hilife" : $text = "萊爾富取貨付款"; break;
			case "unimart_p" : $text = "7-11 純取貨"; break;
			case "fami_p" : $text = "全家純取貨"; break;
			case "hilife_p" : $text = "萊爾富純取貨"; break;
			case "atm" : 
			default: $text = "匯款轉帳"; break;
		}

		return $text;
	}

	/**
	 * 進入金流畫面
	 */
	public function goto_pay($pay_modele_name, $settings, $orderform, $test = true){
		$module_name = "module_" . $pay_modele_name;
		$this -> controller -> module_loader -> load($pay_modele_name);
		$this -> controller -> {$module_name} -> pay($settings, $orderform, $this -> get_items(), $test);

		// $this -> controller -> {$module_name} -> pay($settings, $orderform, $this -> get_items(), false);
	}
	
	/**
	 * 結帳
	 * @param string $module_code 表單模組代碼
	 * @return boolean $is_success
	 */
	public function checkout($module_code){
		$orderform = $this -> controller -> initial($module_code);
		$orderform -> cart -> set_value($this -> get_items());
		
		if($this -> controller -> save_to_db($orderform)){
			$this -> controller -> send_mail($orderform);
			return true;
		}
		
		return false;
		
	}
	
	
}
?>