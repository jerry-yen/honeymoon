<?php
/**
 * 記錄模組
 */
class Logger extends Base_Module {

        /**
         * 每次載入此模組時檢查是否有過期的 log 檔，超過期限則刪除
         */
        public function init() {
                
                $files = scandir($this -> system_configs["full_log_path"] . "/");
                
                foreach ($files as $file) {
                        if (in_array($file, array(".", ".."))) {
                                continue;
                        }
                        $date = substr($file, 0, 10);
                        $base_time = strtotime("-" . $this -> module_configs["expired"] . " days");
                        $file_time = strtotime($date);

                        if ($base_time > $file_time) {
                                $filename = $this -> system_configs["full_log_path"] . "/" . $file;
                                if (file_exists($filename)) {
                                        unlink($filename);
                                }
                        }
                }
        }

        /**
         * 記錄log
         * @param string $message 記錄訊息
         * @param string $name 記錄名稱
         */
        public function log($message, $name = "log") {
                
                // 檔案保存期間為0天則不產生 log 記錄
                if($this -> module_configs["expired"] <= 0){
                        return;
                }
                
                $date = date("Y-m-d");
                $time = date("H:i:s");
				
				$ip = "";

				if ($this -> controller -> module_loader -> is_exists("ip")) {
					$ip = " - " . $this -> controller -> module_ip -> get_ip();
				}
                file_put_contents($this -> system_configs["full_log_path"] . "/" . $date . "-" . $name . ".log", "[" . $date . " " . $time . $ip . "] " . $message . "\r\n", FILE_APPEND | LOCK_EX);
                unset($date);
                unset($time);
        }

}
?>