<?php
/**
 * 工具 模組
 */
class Utility extends Base_Module {
	/**
	 * 從打卡開始算起，目前已經上班多久了，一天一定要上滿指定時數，指定下班時間下班
	 * @param $items 本日打卡記錄
	 * @param $work_setting 本日上班設定 
	 * @return $sec (總秒數)
	 */
	public function get_working_timer($items){
		
	}
	
	/**
	 * 距離下班剩餘秒數
	 * @param $items 本日打卡記錄
	 * @param $work_setting 本日上班設定 
	 * @return $sec (總秒數)
	 */
	public function get_count_down_timer($items, $work_setting){
		
		$count = count($items);
		
		// 本日放假
		if($work_setting["is_working"] != "Y"){
			return -1;
		}
		
		// 還沒打卡上班
		if($count == 0){
			return -1;
		}
		
		// 最後一筆為「下班」，還沒打卡上班
		if($items[$count -1 ] -> status -> get_value() != "1"){
			return -1;
		}
		
		
		
		// 本日可下班時間
		$leave_timer = strtotime($this -> get_leave_time($items, $work_setting));
		// 目前時間
		$now_timer = strtotime(date("Y-m-d H:i:s"));
		
		return ($leave_timer - $now_timer);
	}
	
	/**
	 * 取得今日可下班的時間
	 * @param $items 本日打卡記錄
	 * @param $work_setting 本日上班設定 
	 * @return $sec (總秒數)
	 */
	public function get_leave_time($items, $work_setting){
		// 打卡時間
		$checkin_date_timer = strtotime($items[0] -> check_date_time -> get_value());
		
		// 標準上班時間
		$work_timer = strtotime(date("Y-m-d " . $work_setting["work_time"] . ":00"));
		// 延後彈性時間
		$work_timer_in_delay = strtotime(date("Y-m-d " . $work_setting["work_time"] . ":00") . " +" . $work_setting["delay_time"] . " min");
		// 標準下班時間
		$leave_timer = strtotime(date("Y-m-d " . $work_setting["leave_time"] . ":00"));
		// 彈性下班時間
		$leave_timer_in_delay = strtotime(date("Y-m-d " . $work_setting["leave_time"] . ":00") . " +" . $work_setting["delay_time"] . " min");
		
		$timer = $leave_timer;
		
		// 只要超過標準上班時間打卡，只有遲到及彈性時間內遲到 兩種情形，下班時間就是「標準下班時間 + 彈性時間」
		if($work_timer < $checkin_date_timer){
			$timer = $leave_timer_in_delay;
		}
		
		return date("Y-m-d H:i:s", $timer);
		
	}
	
	/**
	 * 是否需要申請加班
	 * @param $checkin_timer 打卡時間(秒數)
	 * @param $items 本日打卡記錄
	 * @param $work_setting 本日上班設定
	 * @param $overtime array() 需申請加班的時間區間
	 */
	public function get_overtime($checkin_timer, $items, $work_setting){
		if(!is_array($work_setting) || $work_setting == array()){
			return array();
		}
		
		$work_timer_in_delay = strtotime(date("Y-m-d " . $work_setting["work_time"] . ":00") . " -" . $work_setting["delay_time"] . " min");
		$leave_timer_in_delay = strtotime(date("Y-m-d " . $work_setting["leave_time"] . ":00") . " +" . $work_setting["delay_time"] . " min");
		
		$overtimes = array();
		
		// 太早上班
		if($checkin_timer < $work_timer_in_delay){
			$overtimes[] = array(
				"type" => "AM",
				"msg" => date("Y-m-d H:i:s", $checkin_timer) . " ~ " . date("Y-m-d H:i:s", $work_timer_in_delay)
			);
		}
		
		// 太晚下班
		if($checkin_timer > $leave_timer_in_delay){
			$overtimes[] = array(
				"type" => "PM",
				"msg" => date("Y-m-d H:i:s", $leave_timer_in_delay) . " ~ " . date("Y-m-d H:i:s", $checkin_timer)
			);
		}
		
		return $overtimes;
		
	}
	
	/**
	 * 取得指定用戶的上班設定
	 */
	public function get_member_work_setting($member_id){
		
		// 取得用戶資訊
		$member = $this -> controller -> get_single_item("member", $member_id);
		if(!$member -> is_exists()){
			return array();
		}
		
		// 取得該用戶所屬的部門 ( 上班設定歸屬於部門設定 )
		$departmant_id = $member -> departmant -> get_value();
		$departmant = $this -> controller -> get_single_item("departmant",$departmant_id);
		
		// $v_work_timer = "work_timer_" . $day;
		// 取得本日星期幾
		$day = date("N");
		// 本日是否放假(變數)
		$is_working = "is_working_" . $day;
		// 上班時間(變數)
		$work_time = "work_time_" . $day;
		// 中間休息開始時間(變數)
		$break_time_start = "break_time_start_" . $day;
		// 中間休息結束時間(變數)
		$break_time_end = "break_time_end_" . $day;
		// 下班時間(變數)
		$leave_time = "leave_time_" . $day;
		// 彈性時間(變數)
		$delay_time = "delay_time_" . $day;
		
		// 用戶是否有進行工作設定 ( 依用戶設定為優先，預設為部門設定 )
		$work_setting = json_decode($member -> worktime -> get_value());
		
		// 個人沒填寫
		if($work_setting == "" || $work_setting -> {$work_time} == 0){
			$work_setting = json_decode($departmant -> worktime -> get_value());
		}
		
		// 部門也沒填寫，就採用預設值
		if($work_setting == "" || $work_setting -> {$work_time} == 0){
			return array(
				"default_value" => "Y",
				"is_working" => "Y",
				"work_time" => "8:00",
				"leave_time" => "17:00",
				"break_time_start" => "12:00",
				"break_time_end" => "13:00",
				"delay_time" => "30"
			);
		}
		
		return array(
			"default_value" => "N",
			"is_working" => $work_setting -> {$is_working},
			"work_time" => $work_setting -> {$work_time},
			"leave_time" => $work_setting -> {$leave_time},
			"break_time_start" => $work_setting -> {$break_time_start},
			"break_time_end" => $work_setting -> {$break_time_end},
			"delay_time" => $work_setting -> {$delay_time}
		);
	}
	/*
	 * 設定 8:00 上班 彈性時間30分鐘
	 * 7:30 ~ 8:30 打卡屬正常上班
	 * 7:00 打卡屬加班
	 * 
	 * 下班時間 為 5:00 彈性時間 30分鐘
	 * 5:30 內打卡為正常下班
	 * 6:00 打卡屬加班
	 * 
	 * 4:30 ~ 5:00  需請假
	 * 
	 * --加班-- 7:30 - 8:00 上午上班時間 - 中午休息時間 - 下午上班時間 - 5:00 - 5:30 -- 加班 --
	 * 
	 * 上班早於彈性時間 算加班
	 * 下班晚於彈性時間 算加班
	 * 上班晚於彈性時間 需請假
	 * 下班早於彈性時間 需請假
	 */
	
}
?>