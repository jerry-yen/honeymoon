<?php
/**
 * 目前IP模組
 */
class Ip extends Base_Module {
	public function get_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$myip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$myip = $_SERVER['REMOTE_ADDR'];
		}
		return $myip;
	}

}
?>