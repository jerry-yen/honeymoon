<?php
class Count_Down_Component extends Component {
	/**
	 * 無標題
	 */
	public function has_title() {
		return false;
	}

	/**
	 * 執行此元件的當下，已上班的總秒數
	 */	
	protected $count_down_timer = 0;

	public function render($attributes = array()) {
		// 取得今日指定用戶的所有打卡記錄
		@session_start();

		$this -> controller -> module_loader -> load("utility");
		$work_setting = $this -> controller ->  module_utility -> get_member_work_setting($_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"]);
		
		
		$items = $this -> controller -> get_items("checkin",array("DATE_FORMAT(check_date_time,'%Y-%m-%d')=?","title=?"),array(date("Y-m-d"),$_SESSION[$_SERVER["HTTP_HOST"] . "-login-session"]),array("check_date_time ASC"));
		
		$this -> count_down_timer = $this -> controller -> module_utility -> get_count_down_timer($items, $work_setting);
		
		$work_status = ($this -> count_down_timer > 0)?"":"<span style='color:red;'>您尚未打卡上班</span>";
		if($work_setting["is_working"] != "Y"){
			$work_status = "<span style='color:red;'>本日為您的放假日</span>";
		}
		
		$component = "
		<style>
			.timer {text-align:center;width:100%;margin:30px 0px;}
			.timer .title {font-size:30px}
			.timer .clock {font-size:100px}
		</style>
		<div class='timer'>
			<div class='title'>{$work_status}</div>
			<div class='clock'>00:00</div>
		</div>";
		
		return $component;
	}

	
	
	
	public function script(){		
?>
<script type="text/javascript">
	var max_timer = <?php echo $this -> count_down_timer; ?>;
	// var max_timer = 3;
	var rest_timer = 300;
	var timer = max_timer;
	var min = 0;
	var sec = 0;
	var interval = null;
	
	$("input[name='go']").click(function(){
		if($(this).hasClass("run")){
			clock_stop();
		}
		else{
			$("#stop_audio")[0].play();
            $("#stop_audio")[0].pause();
			clock_go();
		}
		
	});
	
	$("input[name='clear']").click(function(){
		timer = max_timer + 1;
		clock_run();
		clock_stop();
		
	});

	function clock_run(){
		timer--;
		
		if(timer < 0){
			timeisup();
			return;
		}
		
		hur = parseInt(timer / 60 / 60);
		min = parseInt(timer / 60) % 60;
		sec = timer % 60;
		
		hur = (hur < 10) ? ("0" + hur) : hur;
		min = (min < 10) ? ("0" + min) : min;
		sec = (sec < 10) ? ("0" + sec) : sec;
		$(".clock").html(hur + ":" + min + ":" + sec);
	}
	function timeisup(){
		clock_stop();
		timer = max_timer;
		$("#stop_audio")[0].pause();
		$("#stop_audio")[0].currentTime = 0;
		$("#stop_audio")[0].play();
	}
	
	function clock_go(){
		$("input[name='go']").addClass("run").addClass("btn-danger").removeClass("btn-primary").attr("value","暫停");
		interval = setInterval(clock_run,1000);
	}
	function clock_stop(){
		$("input[name='go']").removeClass("run").removeClass("btn-danger").addClass("btn-primary").attr("value","開始計時");
		clearInterval(interval);
	}
	
	// 定時通知伺服器，我還在執行，不要閒置過久而登出
	$(document).ready(function(){
		setInterval(function(){
			$.ajax({url: ""});
		},30000);
		
		<?php if($this -> count_down_timer > 0): ?>
		interval = setInterval(clock_run,1000);
		<?php endif; ?>
	});
</script>
<?php
	}

}
?>