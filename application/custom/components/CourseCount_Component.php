<?php
class CourseCount_Component extends Component
{

        /**
         * 是否有標題(群組標籤就沒有標題)
         */
        public function has_title()
        {
                return false;
        }

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


                $value = html_entity_decode($this->get_value());


                return ''; // "<br/><span {$str_attribute} style=\"height:auto;\">{$value}<span><input type=\"hidden\" name=\"{$this -> variable}\" value=\"{$value}\" />";
        }

        public function get_title()
        {
                $module_action = $this->controller->module_dao->get_object("module");
                $module_action->get_module('signup*v_signup');
                $members = $module_action->get_items(array('course=?', 'status=1'), array($this->item->id));

                $button = '<a class="join_button label label-primary glyphicon glyphicon-user" href="/admin/search_item.php?mod=signup*v_signup&course=' . $this->item->id . '"> 報名名單</a>';

                return $button . ' - ' . count($members) . '人';
        }

        public function script()
        {

?>
                <script type="text/javascript">
                        $(document).ready(function() {


                                $(".join_button").click(function() {
                                        console.log($(this).attr('id'));
                                });
                        });
                </script>
<?php
        }
}
?>