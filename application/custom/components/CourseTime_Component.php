<?php
class CourseTime_Component extends Component
{

	private static $include_scripts = false;

	public function render($attributes = array())
	{

		// 驗證失敗呈現
		$invalid_string = "";
		if ($this->valid_error_message != "") {
			$attributes["class"] = (isset($attributes["class"])) ? ($attributes["class"] . " invalid") : "invalid";
			$invalid_string = "<span class='invalid_message'>{$this->valid_error_message}</span>";
		}

		// 屬性
		$str_attribute = "";
		foreach ($attributes as $name => $attr_value) {
			$str_attribute .= $name . '="' . $attr_value . '" ';
		}

		// 提示
		$tip = ($this->tip != "") ? ("<span class=\"tip\">" . $this->tip . "</span>") : "";

		$value = $this->get_value();

		if ($value == "") {
			$value = array();
		}


		$fieldMetadatas = (is_string($value)) ? json_decode($value) : $value;

		include($this->config["full_application_path"] . "/custom/config/components.php");

		$render = "{$tip} <div id='{$this->variable}_field_add' style='float:right;margin-bottom:5px;'><button class=\"btn btn-success glyphicon glyphicon-plus\" type='button' title=\"新增欄位\"></button></div><br />";
		$render .= '<div class="box-body table-responsive no-padding">
						<table class="table table-hover">
							
							<tr>
 								<th>&nbsp;</th>
								<th>課程日期</th>
								<th>開始時間</th>
								<th>時數</th>
								<th>指令</th>
							</tr>
							';
		$render .= '<tbody class="' . $this->variable . '_items">';

		foreach ($fieldMetadatas as $fieldMetadata) {
			$render .= $this->get_row_object($fieldMetadata);
		}
		$render .= $this->get_empty_row_object();


		$render .= '</tbody>';
		$render .= '</table>
					</div>';

		return $render;
	}

	public function get_timer(){
		$value = $this->get_value();
		if($value == ''){
			return 0;
		}
		$timer = 0;
		$value = json_decode($value, true);
		foreach($value as $time){
			$timer += $time['datetimes_field_hour'];
		}
		return $timer;
	}

	private function get_row_object($fieldMetadata)
	{
		$render = '			<tr class="' . $this->variable . '_item">
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>
                            	<td><input type="text" class="form-control" name="' . $this->variable . '_field_date[]" value="' . $fieldMetadata->{$this->variable . "_field_date"} . '"></td>
								<td><input type="text" class="form-control" name="' . $this->variable . '_field_time[]" value="' . $fieldMetadata->{$this->variable . "_field_time"} . '"></td>
								<td><input type="text" class="form-control" name="' . $this->variable . '_field_hour[]" value="' . $fieldMetadata->{$this->variable . "_field_hour"} . '"></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}

	private function get_empty_row_object()
	{
		$render = '			<tr class="' . $this->variable . '_empty" style="display:none;">
                            	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>
                            	<td><input type="text" class="form-control" name="' . $this->variable . '_field_date[]" value=""></td>
								<td><input type="text" class="form-control" name="' . $this->variable . '_field_time[]" value=""></td>
								<td><input type="text" class="form-control" name="' . $this->variable . '_field_hour[]" value=""></td>
                                <td><button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button></td>
                            </tr>';
		return $render;
	}

	public function get_title()
	{
		$value = $this->get_value();

		if ($value == "") {
			$value = array();
		}

		$week = array('日','一','二','三','四','五','六');
		$value = (is_string($value)) ? json_decode($value, true) : $value;
		foreach($value as $v){
			$times[] = $v['datetimes_field_date'] . '(' . $week[date('w', strtotime($v['datetimes_field_date']))] . ') ' . $v['datetimes_field_time'];
		}
		return implode('<br />', $times);
	}

	public function set_value($value)
	{
		$field_date = $this->variable . "_field_date";
		$field_time = $this->variable . "_field_time";
		$field_hour = $this->variable . "_field_hour";

		$count = count($this->controller->module_io->{$field_date});

		$fields = array();
		for ($i = 0; $i < $count - 1; $i++) {

			if ($this->controller->module_io->{$field_date}[$i] == "") {
				continue;
			}

			$fields[] = array(
				$field_date => $this->controller->module_io->{$field_date}[$i],
				$field_time => $this->controller->module_io->{$field_time}[$i],
				$field_hour => $this->controller->module_io->{$field_hour}[$i]
			);
		}

		$this->value = json_encode($fields);
	}

	public function script()
	{
		if (!self::$include_scripts) {
			self::$include_scripts = true;
?>
			<link href="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
			<script src="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
			<script type="text/javascript" src="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/datepicker-zh-TW.js"></script>
			<link href="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/jquery.datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
			<script src="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/jquery.datetimepicker/jquery.datetimepicker.full.js" type="text/javascript"></script>
			<style>
				.ui-datepicker-month, .ui-datepicker-year {
					color:black;
				}
			</style>
		<?php
		}
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				$(".<?php echo $this->variable ?>_items").sortable({
					handle: ".dropable"
				});



				$('body').on('focus', 'input[name="<?php echo $this->variable; ?>_field_date[]"]', function() {
					$(this).datepicker({
						showButtonPanel: true,
						changeMonth: true,
						changeYear: true,
						showOtherMonths: true,
						selectOtherMonths: true,
						yearRange: "1930:2030"
					});
				});

				$('body').on('focus', 'input[name="<?php echo $this->variable; ?>_field_time[]"]', function() {
					$(this).datetimepicker({
						datepicker: false,
						format: 'H:i',
						step: 30
					});
				});


				$("#<?php echo $this->variable; ?>_field_add").click(function() {
					var new_object = $(".<?php echo $this->variable ?>_empty").clone();
					$(".<?php echo $this->variable ?>_empty").before(new_object);
					new_object.addClass("<?php echo $this->variable ?>_item").removeClass("<?php echo $this->variable ?>_empty");
					new_object.show();

					$(".delete").unbind("click");
					$(".delete").click(function() {
						$(this).parents(".<?php echo $this->variable ?>_item").remove();
					});

				});

				$(".delete").click(function() {
					$(this).parents(".<?php echo $this->variable ?>_item").remove();
				});
			});
		</script>
<?php
	}
}
?>