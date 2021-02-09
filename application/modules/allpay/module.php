<?php
/**
 * 歐付寶 模組
 */
require_once(dirname(__FILE__) . "/AllPay.Payment.Integration.php");

class Allpay extends Base_Module {
        public function pay_money($module_setting, $orderform){
        	$oPayment = new AllInOne();
			 /* 服務參數 */
			 $oPayment->ServiceURL ="https://payment.allpay.com.tw/Cashier/AioCheckOut";
			 // $oPayment->ServiceURL ="http://payment-stage.allpay.com.tw/Cashier/AioCheckOut";
			 $oPayment->HashKey = $module_setting -> HashKey -> get_value();
			 $oPayment->HashIV = $module_setting -> HashIV -> get_value();
			 $oPayment->MerchantID = $module_setting -> title -> get_value();
			 
			 /* 基本參數 */
			 
			 $oPayment->Send['ReturnURL'] = dirname($this -> curPageURL()) . "/allpay_return.php?sell_id=" . $orderform["sell_id"];
			 //$oPayment->Send['ClientBackURL'] = "http://" . $_SERVER["HTTP_HOST"] . "/";
			 //$oPayment->Send['OrderResultURL'] = "<<您要收到付款完成通知的瀏覽器端網址>>";
			 $oPayment->Send['MerchantTradeNo'] = $orderform["code"];
			 $oPayment->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
			 $oPayment->Send['TotalAmount'] = (int) $orderform["total"];
			 $oPayment->Send['TradeDesc'] = "<<您該筆訂單的描述>>";
			 $oPayment->Send['ChoosePayment'] = PaymentMethod::ALL;
			 $oPayment->Send['Remark'] = "<<您要填寫的其他備註>>";
			 $oPayment->Send['ChooseSubPayment'] = PaymentMethodItem::None;
			 $oPayment->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::Yes;
			 $oPayment->Send['DeviceSource'] = DeviceType::PC;
			 $oPayment->Send['IgnorePayment'] = "Alipay#Tenpay#TopUpUsed"; // 例(排除支付寶與財富通): Alipay#Tenpay
			 
			 $oPayment->SendExtend['ExpireDate'] = (int) "3";
 			 $oPayment->SendExtend['PaymentInfoURL'] = dirname($this -> curPageURL()) . "/allpay_pay_notify.php";
			 
			 
			 // 加入選購商品資料。
			
			foreach($orderform["goods"] as $good){
			 array_push($oPayment->Send['Items'], array('Name' => $good["title"], 'Price' => (int)$good["price"],
			'Currency' => "(元)新台幣", 'Quantity' => (int) $good["count"], 'URL' => "<<產品說明位址>>"));
			}
			
			
			 /* 產生訂單 */
			 $oPayment->CheckOut();
			 /* 產生產生訂單 Html Code 的方法 */
			 $szHtml = $oPayment->CheckOutString();
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