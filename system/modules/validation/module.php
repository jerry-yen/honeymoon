<?php
/**
 * 欄位驗證模組
 */
class Validation extends Base_Module {
	
	/**
	 * @var 欄位驗證物件
	 */
	protected static $instance;

	/**
	 * 是否有值
	 * @param string $value 值
	 * @return boolean
	 */
	public function hasValue($value) {
		return ($value != "") || !isset($value);
	}

	/**
	 * 字數是否大於最小長度
	 * @param string $value 值
	 * @param integer $minlength 最小長度
	 * @return boolean
	 */
	public function min_length($value, $minlength) {
		return ((mb_strlen($value, "utf-8") >= $minlength) || !isset($value));
	}

	/**
	 * 字數是否小於最大長度
	 * @param string $value 值
	 * @param integer $maxlength 最大長度
	 * @return boolean
	 */
	public function max_length($value, $maxlength) {
		return ((mb_strlen($value, "utf-8") <= $maxlength) || !isset($value));
	}

	/**
	 * 字數是否為指定長度
	 * @param string $value 值
	 * @param integer $length 指定長度
	 * @return boolean
	 */
	public function length($value, $length) {
		return ((mb_strlen($value, "utf-8") == $length) || !isset($value));
	}

	/**
	 * 字數是否在指定範圍
	 * @param string $value 值
	 * @param integer $minlength 最小長度
	 * @param integer $maxlength 最大長度
	 * @return boolean
	 */
	public function range_length($value, $minlength, $maxlength) {
		return ((mb_strlen($value, "utf-8") >= $minlength && mb_strlen($value, "utf-8") <= $maxlength) || !isset($value));
	}

	/**
	 * 是否為整數
	 * @param string $value 值
	 * @return boolean
	 */
	public function integer($value) {
		return (preg_match("/^-{0,1}\d+$/", $value) || !isset($value));
	}

	/**
	 * 數否為帶小數的數字(含整數)
	 * @param string $value 值
	 * @return boolean
	 */
	public function double($value) {
		return (preg_match("/^-{0,1}\d*\.{0,1}\d+$/", $value) || !isset($value));
	}

	/**
	 * 最小數值不可低於
	 * @param string $value 值
	 * @param integer $min 最小值
	 * @return boolean
	 */
	public function minimum($value, $min) {
		return (($this -> double($value) && ((double)$value) >= $min) || !isset($value));
	}

	/**
	 * 最大數值不可大於
	 * @param string $value 值
	 * @param integer $max 最大值
	 * @return boolean
	 */
	public function maximum($value, $max) {
		return (($this -> double($value) && ((double)$value) <= $max) || !isset($value));
	}

	/**
	 * 數值是否在指定區間
	 * @param string $value 值
	 * @param integer $min 最小值
	 * @param integer $max 最大值
	 * @return boolean
	 */
	public function numberRange($value, $min, $max) {
		return (($this -> double($value) && ((double)$value) <= $max && ((double)$value) >= $min) || !isset($value));
	}

	/**
	 * 是否為E-mail格式
	 * @param string $value 值
	 * @return boolean
	 */
	public function email($value) {
		return (preg_match("/^[\_]*([a-z0-9]+(\.|\_*)?)+@([a-z]?[a-z0-9\-]+(\.|\-*\.))+[a-z]{2,6}$/", $value) || !isset($value) || $value == "");
	}

	/**
	 * 是否為IP格式
	 * @param string $value 值
	 * @return boolean
	 */
	public function ip($value) {
		return (preg_match("/^(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]){3}$/", $value) || !isset($value));
	}

	/**
	 * 是否為網域格式
	 * @param string $value 值
	 * @return boolean
	 */
	public function domain($value) {
		return (preg_match("/^([a-z][a-z0-9\-]+(\.|\-*\.))+[a-z]{2,6}$/", $value) || !isset($value) || $value == "");
	}

	/**
	 * 是否為帳號格式
	 * @param string $value 值
	 * @return boolean
	 */
	public function account($value) {
		return (preg_match("/^[a-zA-Z][a-zA-Z0-9]{4,20}$/", $value) || !isset($value) || $value == "");
	}

	/**
	 * 是否為日期格式
	 * @param string $value 值
	 * @return boolean
	 */
	public function dateFormat($value) {
		return (preg_match("/^(19[0-9][0-9]|20[0-9][0-9])\D([1-9]|0[1-9]|1[012])\D([1-9]|0[1-9]|[12][0-9]|3[01])$/", $value) || !isset($value));
	}

	/**
	 * 可隨時取得 Validation 元件
	 */
	public static function & get_instance() {
		return self::$instance;
	}

}
?>