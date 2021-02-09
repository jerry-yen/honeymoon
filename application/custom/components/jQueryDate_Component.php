<?php
class jQueryDate_Component extends Component
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

                // 如為空值則代入預設值
                if (trim($value) == "") {
                        $value = $this->get_default();
                }
                $value = ($value == '0000-00-00 00:00:00') ? '' : date('Y-m-d', strtotime($value));
                if($value == '1970-01-01'){
                        $value = '';
                }
                return "{$tip}<br/><input type=\"text\" name=\"{$this->variable}\" value=\"{$value}\" {$str_attribute}/>{$invalid_string}";
        }
        public function get_title()
        {
                $value = $this->get_value();
                if ($value == '0000-00-00 00:00:00') {
                        return '未設定';
                }
                return date('Y-m-d', strtotime($value));
        }

        public function script()
        {

                if (!self::$include_scripts) {
                        self::$include_scripts = true;
?>

                        <link href="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
                        <script src="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
                        <script type="text/javascript" src="<?php echo $this->config["machine_relative_jquery_lib_path"]; ?>/datepicker-zh-TW.js"></script>
                        <style>
                                select.ui-datepicker-year,select.ui-datepicker-month{
                                        color:black;
                                }
                        </style>
                <?php
                }
                ?>
                <script>
                        $(document).ready(function() {
                                $("input[name='<?php echo $this->variable; ?>']").datepicker({
                                        showButtonPanel: true,
                                        changeMonth: true,
                                        changeYear: true,
                                        showOtherMonths: true,
                                        selectOtherMonths: true,
                                        yearRange: "1930:2030"
                                });
                        });
                </script>
<?php
        }
}
?>