<?php
require_once ( dirname(__FILE__) . "/PHPExcel.php");
class Phpexcel_Export extends Base_Module {
	public function get_instance(){
		return new PHPExcel();
	}

}
?>