<?php
require_once ("Sanitizer.php");

/**
 * 輸入/輸出過濾模組
 */
class Filter extends Base_Module {

	/**
	 * 將原本已過濾過的值，重新取出備份的原始資料，跳過指定的過濾機制
	 * @param string $key $_GET 或 $_POST 標籤
	 * @param string $element 跳過的過濾元素
	 */
	public function filter_allow($value, $allows = array()) {

		$san = new HTML_Sanitizer();

		foreach ($allows as $allow) {
			switch($allow) {
				case "script" :
					$san -> allowAllJavascript();
					break;
				case "css" :
					$san -> allowStyle();
					break;
				case "escape" :
					$san -> allowHTMLNoneEscape();
					break;
				case "iframe" :
					$san -> allowIframes();
					break;
				case "all" :
					$san -> allowAllJavascript();
					$san -> allowStyle();
					$san -> allowHTMLNoneEscape();
					$san -> allowIframes();
					break;
			}
		}

		if (isset($value)) {

			if (is_array($value)) {

				foreach ($value as $key => $val) {
					$value[$key] = $san -> sanitize($val);
				}
			} else {
				$value = $san -> sanitize($value);
			}

		}

		unset($san);
		return $value;

	}

}
?>