<?php

class Api_Controller extends AdminController {

	public function course_status(){
		$join_id = $this -> module_io -> join_id;
		$status = $this -> module_io -> status;

		$join = $this -> get_single_item('join_course', $join_id);
		if($join -> is_exists()){
			$join -> status = $status;
			$join -> update();
			echo json_encode(array('status' => 'success'));
			exit;
		}

		echo json_encode(array('status' => 'no data'));
		exit;

		
	}
}
?>