<?php
/**
 * 緣界金流 模組
 */
require_once(dirname(__FILE__) . "/ECPay.Logistics.Integration.php");

class Ec_Logistics extends Base_Module {
        public function shop($settings, $orderform, $test = true){

        	$settings = (object)$settings;

        	try {
		        $AL = new ECPayLogistics();


		        /* 服務參數 */
				if($test){
					$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/Create";
				}
				else{
					$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/Create";
				}

				$type = "";
				switch($orderform -> ship_code -> get_value()){
					case "UNIMART" : $type = LogisticsSubType::UNIMART_C2C; break;
					case "FAMI" : $type = LogisticsSubType::FAMILY_C2C; break;
					case "HILIFE" : $type = LogisticsSubType::HILIFE_C2C; break;
					case "UNIMARTC2C" : $type = LogisticsSubType::UNIMART_C2C; break;
					case "FAMIC2C" : $type = LogisticsSubType::FAMILY_C2C; break;
					case "HILIFEC2C" : $type = LogisticsSubType::HILIFE_C2C; break;
				}

		        $AL->HashKey = $settings -> HashKey;
		        $AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'MerchantTradeNo' => $orderform -> code -> get_value(),
		            'MerchantTradeDate' => date('Y/m/d H:i:s'),
		            'LogisticsType' => LogisticsType::CVS,
		            'LogisticsSubType' => $type, // LogisticsSubType::UNIMART, LogisticsSubType::FAMI, LogisticsSubType::HILIFE
		            'GoodsAmount' => (int) $orderform -> total -> get_value(),
		            'CollectionAmount' => (int) $orderform -> total -> get_value(),
		            'IsCollection' => IsCollection::YES,
		            'GoodsName' => '魚宮寵物商品',
		            'SenderName' => $orderform -> title -> get_value(),
		            'SenderPhone' => $orderform -> phone -> get_value(),
		            'SenderCellPhone' => $orderform -> phone -> get_value(),
		            'ReceiverName' => $orderform -> r_title -> get_value(),
		            'ReceiverPhone' => $orderform -> r_phone -> get_value(),
		            'ReceiverCellPhone' => $orderform -> r_phone -> get_value(),
		            'ReceiverEmail' => $orderform -> r_email -> get_value(),
		            'TradeDesc' => '',
		            'ServerReplyURL' => dirname($this -> curPageURL()) . "/logisticsid.php",
		            'LogisticsC2CReplyURL' => dirname($this -> curPageURL()) . "/logisticsid.php?store_q=1",
		            'Remark' => '',
		            'PlatformID' => '',
		        );
		        
		       	$AL->SendExtend = array(
		            'ReceiverStoreID' => $orderform -> store_id -> get_value(),
		       		// 'ReceiverStoreID' => '991182',
		            // 'ReturnStoreID' => '991182'
		        );
		        // BGCreateShippingOrder()
		        $Result = $AL->BGCreateShippingOrder();
		     // echo '<pre>' . print_r($Result, true) . '</pre>';
		     // exit;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		        exit;

		    }
		     
        }

        public function shop_no_collection($settings, $orderform, $test = true){

        	$settings = (object)$settings;

        	try {
		        $AL = new ECPayLogistics();


		        /* 服務參數 */
				if($test){
					$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/Create";
				}
				else{
					$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/Create";
				}

				$type = "";
				switch($orderform -> ship_code -> get_value()){
					case "UNIMART" : $type = LogisticsSubType::UNIMART_C2C; break;
					case "FAMI" : $type = LogisticsSubType::FAMILY_C2C; break;
					case "HILIFE" : $type = LogisticsSubType::HILIFE_C2C; break;
					case "UNIMARTC2C" : $type = LogisticsSubType::UNIMART_C2C; break;
					case "FAMIC2C" : $type = LogisticsSubType::FAMILY_C2C; break;
					case "HILIFEC2C" : $type = LogisticsSubType::HILIFE_C2C; break;
				}

		        $AL->HashKey = $settings -> HashKey;
		        $AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'MerchantTradeNo' => $orderform -> code -> get_value(),
		            'MerchantTradeDate' => date('Y/m/d H:i:s'),
		            'LogisticsType' => LogisticsType::CVS,
		            'LogisticsSubType' => $type, // LogisticsSubType::UNIMART, LogisticsSubType::FAMI, LogisticsSubType::HILIFE
		            'GoodsAmount' => (int) $orderform -> total -> get_value(),
		            'CollectionAmount' => (int) $orderform -> total -> get_value(),
		            'IsCollection' => IsCollection::NO,
		            'GoodsName' => '魚宮寵物商品',
		            'SenderName' => $orderform -> title -> get_value(),
		            'SenderPhone' => $orderform -> phone -> get_value(),
		            'SenderCellPhone' => $orderform -> phone -> get_value(),
		            'ReceiverName' => $orderform -> r_title -> get_value(),
		            'ReceiverPhone' => $orderform -> r_phone -> get_value(),
		            'ReceiverCellPhone' => $orderform -> r_phone -> get_value(),
		            'ReceiverEmail' => $orderform -> r_email -> get_value(),
		            'TradeDesc' => '',
		            'ServerReplyURL' => dirname($this -> curPageURL()) . "/logisticsid.php",
		            'LogisticsC2CReplyURL' => dirname($this -> curPageURL()) . "/logisticsid.php?store_q=1",
		            'Remark' => '',
		            'PlatformID' => '',
		        );
		        
		       	$AL->SendExtend = array(
		            'ReceiverStoreID' => $orderform -> store_id -> get_value(),
		       		// 'ReceiverStoreID' => '991182',
		            // 'ReturnStoreID' => '991182'
		        );
		        // BGCreateShippingOrder()
		        $Result = $AL->BGCreateShippingOrder();
		     // echo '<pre>' . print_r($Result, true) . '</pre>';
		     // exit;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		        exit;

		    }
		     
        }

        public function home_cat($settings, $orderform, $test = true){

        	$settings = (object)$settings;

        	try {
		        $AL = new ECPayLogistics();


		        /* 服務參數 */
				if($test){
					$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/Create";
				}
				else{
					$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/Create";
				}

				$settings -> senderPhone = preg_replace("/[\(\)\- ]/","",$settings -> senderPhone);

		        $AL->HashKey = $settings -> HashKey;
		        $AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'MerchantTradeNo' => $orderform -> code -> get_value(),
		            'MerchantTradeDate' => date('Y/m/d H:i:s'),
		            'LogisticsType' => LogisticsType::HOME,
		            'LogisticsSubType' => LogisticsSubType::TCAT,
		            'GoodsAmount' => (int) $orderform -> total -> get_value(),
		            'CollectionAmount' => (int) $orderform -> total -> get_value(),
		            'IsCollection' => IsCollection::NO,
		            'GoodsName' => '魚宮寵物商品',
		            
		            'SenderName' =>  $settings -> senderName,
		            'SenderPhone' => $settings -> senderPhone,
		            // 'SenderCellPhone' => $settings -> senderPhone,
		            'ReceiverName' => $orderform -> r_title -> get_value(),
		            'ReceiverPhone' => $orderform -> r_phone -> get_value(),
		            'ReceiverCellPhone' => $orderform -> r_phone -> get_value(),
		            'ReceiverEmail' => $orderform -> r_email -> get_value(),
		            'TradeDesc' => '',
		            'ServerReplyURL' => dirname($this -> curPageURL()) . "/logisticsid.php",
		            // 'LogisticsC2CReplyURL' => dirname($this -> curPageURL()) . "/logisticsid.php?store_q=1",
		            'Remark' => '',
		            'PlatformID' => '',
		        );

		        /*
		        $adds = explode("  ",$orderform -> address -> get_value());
		        $zip = $adds[0];
		        unset($adds[0]);
		        $address = implode("",$adds);
				*/
		        $r_adds = explode("  ",$orderform -> r_address -> get_value());
		        $r_zip = $r_adds[0];
		        $r_township = $r_adds[1];
		        unset($r_adds[0]);
		        $r_address = implode("",$r_adds);


		        // 地區
				$distance = Distance::SAME;
				if($r_township != "台中市"){
					$distance = Distance::OTHER;
					if(in_array($r_township,array("金門縣","澎湖縣","連江縣","南海諸島","釣魚台列嶼"))){
						$distance = Distance::ISLAND;
					}
				}

		        
		        
		       	$AL->SendExtend = array(
		           	'SenderZipCode' => "434",
		            'SenderAddress' =>  $settings -> senderAddress,
		            'ReceiverZipCode' => $r_zip,
		            'ReceiverAddress' => $r_address,
		            'Temperature' => Temperature::ROOM,
		            'Distance' => $distance,
		            'Specification ' => Specification::CM_60,
		            'ScheduledPickupTime' => ScheduledPickupTime::UNLIMITED,
		            'ScheduledDeliveryTime' => ScheduledDeliveryTime::UNLIMITED,
		        );
		        
		        
		        // BGCreateShippingOrder()
		        $Result = $AL->BGCreateShippingOrder();
		    //  echo '<pre>' . print_r($Result, true) . '</pre>';
		     // exit;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		        exit;

		    }
		     
        }

        public function maps($settings, $test = true){
        	
        	$settings = (object)$settings;

        	try {
		        $AL = new ECPayLogistics();

		         /* 服務參數 */
				if($test){
					$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/map";
				}
				else{
					$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/map";
				}

				$AL->HashKey = $settings -> HashKey;
		        $AL->HashIV = $settings -> HashIV;
				switch($settings -> Type){
					case "unimart" : $type = LogisticsSubType::UNIMART_C2C; break;
					case "fami" : $type = LogisticsSubType::FAMILY_C2C; break;
					case "hilife" : $type = LogisticsSubType::HILIFE_C2C; break;
				}

		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'MerchantTradeNo' => uniqid(true),
		            'LogisticsType' => LogisticsType::CVS,
		            'LogisticsSubType' => $type, // LogisticsSubType::UNIMART, LogisticsSubType::FAMI, LogisticsSubType::HILIFE
		            'IsCollection' => IsCollection::YES,
		            'ServerReplyURL' => dirname($this -> curPageURL()) . "/shop_select.php?save=1",
		            // 'ExtraData' => '測試額外資訊',
		            // 'Device' => Device::PC
		        );

		       
		        // CvsMap(Button名稱, Form target)
		        $html = $AL->CvsMap('電子地圖');
		        echo $html;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		        exit("error");
		    }
		    exit;
        }


        public function SHIP_PRINT($settings, $id, $test = true){

        	$settings = (object)$settings;

        	 /* 服務參數 */
			if($test){
				$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/helper/printTradeDocument";
			}
			else{
				$AL->ServiceURL ="https://logistics.ecpay.com.tw/helper/printTradeDocument";
			}

			
        	try {
		        $AL = new ECPayLogistics();
		       	$AL->HashKey = $settings -> HashKey;
		    	$AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'AllPayLogisticsID' => $id,
		            'PlatformID' => ''
		        );
		        // PrintTradeDoc(Button名稱, Form target)
		        $html = $AL->PrintTradeDoc('產生托運單/一段標');
		        echo $html;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		    }
        }

        public function UNIMART_PRINT($settings, $id, $trans_code, $test = true){

        	$settings = (object)$settings;

        	 /* 服務參數 */
			if($test){
				$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/PrintUniMartC2COrderInfo";
			}
			else{
				$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/PrintUniMartC2COrderInfo";
			}


			$code = explode(" ", $trans_code);
			
        	try {
		        $AL = new ECPayLogistics();
		       	$AL->HashKey = $settings -> HashKey;
		    	$AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'AllPayLogisticsID' => $id,
		            'CVSPaymentNo' => $code[0],
            		'CVSValidationNo' => $code[1],
		            'PlatformID' => ''
		        );
		        // PrintTradeDoc(Button名稱, Form target)
		        $html = $AL->PrintUnimartC2CBill('列印繳款單(統一超商C2C)');
		        echo $html;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		    }
        }

        public function FAMI_PRINT($settings, $id, $trans_code, $test = true){

        	$settings = (object)$settings;

        	 /* 服務參數 */
			if($test){
				$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/PrintFAMIC2COrderInfo";
			}
			else{
				$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/PrintFAMIC2COrderInfo";
			}


			$code = explode(" ", $trans_code);
			
        	try {
		        $AL = new ECPayLogistics();
		       	$AL->HashKey = $settings -> HashKey;
		    	$AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'AllPayLogisticsID' => $id,
		            'CVSPaymentNo' => $code[0],
		            'PlatformID' => ''
		        );
		        // PrintTradeDoc(Button名稱, Form target)
		        $html = $AL->PrintFamilyC2CBill('全家列印小白單(全家超商C2C)');
		        echo $html;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		    }
        }

        public function HiLIFE_PRINT($settings, $id, $trans_code, $test = true){

        	$settings = (object)$settings;

        	 /* 服務參數 */
			if($test){
				$AL->ServiceURL ="https://logistics-stage.ecpay.com.tw/Express/PrintHILIFEC2COrderInfo";
			}
			else{
				$AL->ServiceURL ="https://logistics.ecpay.com.tw/Express/PrintHILIFEC2COrderInfo";
			}


			$code = explode(" ", $trans_code);
			
        	try {
		        $AL = new ECPayLogistics();
		       	$AL->HashKey = $settings -> HashKey;
		    	$AL->HashIV = $settings -> HashIV;
		        $AL->Send = array(
		            'MerchantID' => $settings -> MerchantID,
		            'AllPayLogisticsID' => $id,
		            'CVSPaymentNo' => $code[0],
		            'PlatformID' => ''
		        );
		        // PrintTradeDoc(Button名稱, Form target)
		        $html = $AL->PrintHiLifeC2CBill('萊爾富列印小白單(萊爾富超商C2C)');
		        echo $html;
		    } catch(Exception $e) {
		        echo $e->getMessage();
		    }
        }
		
		
		/**
		 * 目前完整網址(含參數)
		 */
		private function curPageURL() {
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";
			}
			$pageURL .= "://";
			if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
			} else {
				$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
			}
			return $pageURL;
		}
       
}
?>