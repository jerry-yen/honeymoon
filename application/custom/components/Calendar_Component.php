<?php
class Calendar_Component extends Component {
	/**
	 * 無標題
	 */
	public function has_title() {
		return false;
	}

	public function render($attributes = array()) {
		$component = "<div id='" . $this -> variable . "'></div>";
		return $component;
	}
	
	private function get_event_string($title, $date){
		return "{title: '{$title}',start: '{$date}'},\r\n";
	}
	
	
	
	public function script(){
		$events = "";
		
?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.8.0/fullcalendar.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.8.0/fullcalendar.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript">
	// 定時通知伺服器，我還在執行，不要閒置過久而登出
	$(document).ready(function(){
		$('#<?php echo $this -> variable; ?>').fullCalendar({
			defaultDate: '<?php echo date("Y-m-d"); ?>',
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			buttonText: {
		        today: '今天'
		    },
		    monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
	    	dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
	 		dayNamesShort: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
		    
			events: [
				<?php echo $events; ?>
			]
		});
	});
</script>
<?php
	}

}
?>