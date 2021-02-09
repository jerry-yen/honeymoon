<?php
/**
 * 訊息 模組
 */
class Showbox extends Base_Module {
        public function set_message($message){
                @session_start();
                $_SESSION["showbox_message"] = $message;
                @session_write_close();
        }
        
        public function after_load_view($modules){
                @session_start();
                if(isset($_SESSION["showbox_message"]) && trim($_SESSION["showbox_message"]) != ""){
                        $message = $_SESSION["showbox_message"];
                        echo "<script>$(document).ready(function(){ $('#alert').html('" . $message . "'); $('.alert').show(); });</script>";
                        unset($_SESSION["showbox_message"]);
                }
                @session_write_close();
        }       
}
?>