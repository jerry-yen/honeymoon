<?php
class Batch_Leave_Component extends Component {
	/**
	 * 無標題
	 */
	public function has_title() {
		return false;
	}

	public function render($attributes = array()) {
		
		// 所有部門
		$departmants = $this -> controller -> get_items("departmant");
		
		// 所有帳號
		$members = $this -> controller -> get_items("member");
		
		$value = json_decode($this -> get_value());
		if(!is_array($value)) $value = array();
		
		$component = "<div id='" . $this -> variable . "' style=''>
			<hr />
			<input type=\"checkbox\" name=\"" . $this -> variable . "[]\" value=\"A_ALL\" " . (in_array("A_ALL",$value)?"checked":"") . "> 全體
			";
			
		$component .= "<hr />";
		foreach($departmants as $departmant){
			$component .= "<input type=\"checkbox\" class=\"A_ALL\" name=\"" . $this -> variable . "[]\" value=\"D_{$departmant -> id}\" " . (in_array("D_{$departmant -> id}",$value)?"checked":"") . "> " . $departmant -> title -> get_value() . "　";
		}
		$component .= "<hr />";
		foreach($members as $member){
			$departmant_id = $member -> departmant -> get_value();
			$departmant_class = ($departmant_id != "")? (" D_" . $departmant_id) : "";
			$component .= "<input type=\"checkbox\" class=\"A_ALL {$departmant_class}\" name=\"" . $this -> variable . "[]\" value=\"M_{$member -> id}\" " . (in_array("M_{$member -> id}",$value)?"checked":"") . "> " . $member -> title -> get_value() . "　";
		}
		
		$component .= "</div>";
		
		
		return $component;
	}
	
	public function set_value($value){
		$this -> value = json_encode($value);
	}
	
	
	public function script(){
		
		
?>

<script type="text/javascript">
	
	$(document).ready(function(){
		
		$(".iCheck-helper").click(function(){
			var check_input = $(this).parents(".icheckbox_minimal");
			var check_value = $("input[name='<?php echo $this -> variable; ?>[]']",check_input).attr("value");
			var disabled = check_input.attr("aria-disabled");
			// console.log(check_input.hasClass("checked"));
			// console.log(check_value);
			if(disabled == "true"){
				return;
			}
			if(check_input.hasClass("checked")){
				var cont = $("." + check_value).parents(".icheckbox_minimal");
				cont.addClass("disabled");
				cont.addClass("checked");
				cont.attr("aria-disabled",true);
				$("." + check_value).attr("disabled","disabled");
			}
			else{
				var cont = $("." + check_value).parents(".icheckbox_minimal");
				cont.removeClass("disabled");
				cont.removeClass("checked");
				cont.attr("aria-disabled",false);
				$("." + check_value).removeAttr("disabled");
			}
		}).each(function(){
			var check_input = $(this).parents(".icheckbox_minimal");
			var check_value = $("input[name='<?php echo $this -> variable; ?>[]']",check_input).attr("value");
			if(check_input.hasClass("checked")){
				var cont = $("." + check_value).parents(".icheckbox_minimal");
				cont.addClass("disabled");
				cont.addClass("checked");
				cont.attr("aria-disabled",true);
				$("." + check_value).attr("disabled","disabled");
			}
			
		});
	});
</script>
<?php
	}

}
?>