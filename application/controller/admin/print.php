<?php

class Print_Controller extends AdminController {
	
	protected $debug = false;

	protected $lc_setting;

	public function main(){
		$this -> module_loader -> load("ec_logistics");
		$id = $this -> module_io -> id;
		$orderform = $this -> get_single_item("orderform", $id);

		$ship_code = $orderform -> ship_code -> get_value();

		if($orderform -> payment -> get_value() == "貨到付款"){
			exit("貨到付款，並未串接物流，因此無法列印相關單據");
		}
		

		$this -> lc_setting = $this -> get_item("lc_setting");


		if($this -> debug){
			$this -> lc_setting -> hash_key -> set_value($this -> lc_setting -> hash_key_test -> get_value());
			$this -> lc_setting -> hash_iv -> set_value($this -> lc_setting -> hash_iv_test -> get_value());
			$this -> lc_setting -> merchantID -> set_value($this -> lc_setting -> merchantID_test -> get_value());
		}


		// 黑貓物流單
		if($ship_code == ""){
			$this -> module_ec_logistics -> SHIP_PRINT(array(
						"HashKey" => $this -> lc_setting -> hash_key -> get_value(),
						"HashIV" => $this -> lc_setting -> hash_iv -> get_value(),
						"MerchantID" => $this -> lc_setting -> merchantID -> get_value()
					), $orderform -> ship_id -> get_value(), $this -> debug);
		}


		// 統一(7-11)物流單
		if($ship_code == "UNIMARTC2C"){
			$this -> module_ec_logistics -> UNIMART_PRINT(array(
						"HashKey" => $this -> lc_setting -> hash_key -> get_value(),
						"HashIV" => $this -> lc_setting -> hash_iv -> get_value(),
						"MerchantID" => $this -> lc_setting -> merchantID -> get_value()
					), $orderform -> ship_id -> get_value(), $orderform -> trans_code -> get_value(), $this -> debug);
		}

		// 全家物流單
		if($ship_code == "FAMIC2C"){
			$this -> module_ec_logistics -> FAMI_PRINT(array(
						"HashKey" => $this -> lc_setting -> hash_key -> get_value(),
						"HashIV" => $this -> lc_setting -> hash_iv -> get_value(),
						"MerchantID" => $this -> lc_setting -> merchantID -> get_value()
					), $orderform -> ship_id -> get_value(), $orderform -> trans_code -> get_value(), $this -> debug);
		}

		// 萊爾富物流單
		if($ship_code == "HILIFEC2C"){
			$this -> module_ec_logistics -> HiLIFE_PRINT(array(
						"HashKey" => $this -> lc_setting -> hash_key -> get_value(),
						"HashIV" => $this -> lc_setting -> hash_iv -> get_value(),
						"MerchantID" => $this -> lc_setting -> merchantID -> get_value()
					), $orderform -> ship_id -> get_value(), $orderform -> trans_code -> get_value(), $this -> debug);
		}
		exit;
	}
}
?>