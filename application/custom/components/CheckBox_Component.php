<?php
class CheckBox_Component extends Component
{

	private $datas = array();

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

		// 資料來源
		$this->datas = ($this->datas == array()) ? $this->get_data_source() : $this->datas;

		// 被選擇的項目
		$checked_items = (is_array($value)) ? $value : explode(":", $value);

		$render = "(<a href='javascript:void(0);' class='" . $this->variable . "_all'>全選</a>/<a href='javascript:void(0);' class='" . $this->variable . "_cancel'>取消全選</a>){$tip}<br/>";

		$source_data = array();

		foreach ($this->datas as $data) {
			if(!isset($data[1])){
				continue;
			}
			$source_data[] = $data[0];
			$checked = (in_array($data[0], $checked_items)) ? "checked" : "";
			$render .= "<div style='width:120px;float:left;'><input type=\"checkbox\" name=\"{$this->variable}[]\" value=\"{$data[0]}\" {$checked}/> {$data[1]}</div>";
		}

		// 其他選填項
		if (preg_match("/other{(.*?)};/", $this->element, $element)) {
			$text = $element[1];
			$text_value = "";
			$checked = "";

			foreach ($checked_items as $item_value) {

				if (trim($item_value) == "") continue;

				if (!in_array($item_value, $source_data)) {
					$text_value = $item_value;
					$v = explode("#", $item_value);
					$checked = (count($v) == 2 && $v[0] == "other") ? "checked" : "";
					$text_value = (count($v) == 2 && $v[0] == "other") ? $v[1] : "";
					$checked = "checked";
					break;
				}
			}

			$render .= "<div style='width:120px;float:left;'><input type=\"checkbox\" name=\"{$this->variable}[]\" value=\"other\" {$checked}/>{$text} <input type=\"text\" name=\"{$this->variable}_other\" value=\"{$text_value}\" {$str_attribute} style=\"display:inline; width:80px; border:0px; border-bottom:1px dotted gray;\" /></div>";
		}
		$render .= "<div style='clear:both;'></div>";
		return $render;
	}

	public function script()
	{
?>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.<?php echo $this->variable;?>_all').click(function() {
					var checkbox = $('input[name="permission[]"]');
					checkbox.prop('checked', true);
					checkbox.closest('div.icheckbox_minimal').addClass('checked').attr('aria-checked','true');
				});

				$('.<?php echo $this->variable;?>_cancel').click(function() {
					var checkbox = $('input[name="permission[]"]');
					checkbox.prop('checked', false);
					checkbox.closest('div.icheckbox_minimal').removeClass('checked').attr('aria-checked','false');
				});
			});
		</script>

<?php
	}

	public function get_title()
	{

		// 資料來源
		$this->datas = ($this->datas == array()) ? $this->get_data_source() : $this->datas;

		$value = $this->get_value();

		foreach ($this->datas as $data) {
			if ($data[0] == $value) {
				return $data[1];
			}
		}
	}

	public function set_value($values)
	{
		if (!is_array($values) || is_null($values) || $values == "") {
			$values = array();
		}
		foreach ($values as $key => $value) {
			if ($value == "other") {
				$variable = $this->variable . "_other";
				$value = "other#" . $this->controller->module_io->{$variable};
			}
			$values[$key] = $value;
		}
		$values = implode(":", $values);
		parent::set_value($values);
	}
}
?>