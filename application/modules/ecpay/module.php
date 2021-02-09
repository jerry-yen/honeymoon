<?php
/**
 * 緣界金流 模組
 */
require_once(dirname(__FILE__) . "/ECPay.Payment.Integration.php");

class Ecpay extends Base_Module {
        public function pay($settings, $orderform, $goods, $test = false){

        	$settings = (object)$settings;

        	$oPayment = new ECPay_AllInOne();

			/* 服務參數 */
			if($test){
				$oPayment->ServiceURL ="https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";
			}
			else{
				$oPayment->ServiceURL ="https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5";
			}
			
			
			$oPayment->HashKey = $settings -> HashKey;
			$oPayment->HashIV = $settings -> HashIV;
			$oPayment->MerchantID = $settings -> MerchantID;
			$oPayment->EncryptType = '1';
			 
			/* 基本參數 */
			$oPayment->Send['ReturnURL'] = dirname($this -> curPageURL()) . "/pay_feedback.php?type=ecpay";
			//$oPayment->Send['ClientBackURL'] = "http://" . $_SERVER["HTTP_HOST"] . "/";
			$oPayment->Send['OrderResultURL'] = dirname($this -> curPageURL()) . "/cart_final.php";
			$oPayment->Send['MerchantTradeNo'] = $orderform -> code -> get_value();
			$oPayment->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
			$oPayment->Send['TotalAmount'] = (int) $orderform -> total -> get_value();
			$oPayment->Send['TradeDesc'] = "<<您該筆訂單的描述>>";
			$oPayment->Send['ChoosePayment'] = ECPay_PaymentMethod::ALL;
			$oPayment->Send['Remark'] = $orderform -> content -> get_value();
			$oPayment->Send['ChooseSubPayment'] = ECPay_PaymentMethodItem::None;
			$oPayment->Send['NeedExtraPaidInfo'] = ECPay_ExtraPaymentInfo::Yes;
			$oPayment->Send['DeviceSource'] = ECPay_DeviceType::PC;
			$oPayment->Send['IgnorePayment'] = "Alipay#Tenpay#TopUpUsed#BARCODE"; // 例(排除支付寶與財富通): Alipay#Tenpay
			
			$oPayment->SendExtend['ExpireDate'] = (int) "3";
 			$oPayment->SendExtend['PaymentInfoURL'] = dirname($this -> curPageURL()) . "/pay_notify.php";
			
			
			// 加入選購商品資料。
			
			foreach($goods as $good){
				array_push($oPayment->Send['Items'], array('Name' => $good -> title, 'Price' => (int)$good -> price,
					'Currency' => "(元)新台幣", 'Quantity' => (int) $good -> count, 'URL' => "<<產品說明位址>>"));
			}
			
			
			/* 產生訂單 */
			$oPayment->CheckOut();
			/* 產生產生訂單 Html Code 的方法 */
			// $szHtml = $oPayment->CheckOutString();
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