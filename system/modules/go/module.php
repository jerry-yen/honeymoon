<?php
/**
 * 移動頁面模組
 */
class Go extends Base_Module {

        /**
         * @var 入口名稱
         */
        private $gate;

        public function init() {

                // 後台
                if (strpos($this -> system_configs["full_execute_php_path"], $this -> system_configs["full_admin_path"]) === 0) {
                        $this -> gate = "admin_history";
                }
                // 系統管理
                else if (strpos($this -> system_configs["full_execute_php_path"], $this -> system_configs["full_master_path"]) === 0) {
                        $this -> gate = "master_history";
                }
                // 前台
                else {
                        $this -> gate = "fronter_history";
                }

				if(!defined("__HM_SKIP__")){
					$this -> push_url();
				}
                
        }

        /**
         * 將目前頁面相對路徑記錄至歷史陣列
         */
        private function push_url() {
                @session_start();

                $url = $this -> system_configs["full_url"];
                if (isset($_SESSION[$this -> gate]) && count($_SESSION[$this -> gate]) > 0) {
                        $count = count($_SESSION[$this -> gate]) - 1;
                        if ($_SESSION[$this -> gate][$count] != $url) {
                                $_SESSION[$this -> gate][] = $url;
                        } else {
                                return;
                        }
                } else {
                        $_SESSION[$this -> gate][] = $url;
                }

                $count = count($_SESSION[$this -> gate]);
                $diff = $count - $this -> module_configs["history_length"];

                if ($diff > 0) {

                        $i = 0;
                        foreach ($_SESSION[$this -> gate] as $key => $value) {
                                if ($i < $diff) {
                                        unset($_SESSION[$this -> gate][$key]);
                                }
                                $i++;
                        }

                        $_SESSION[$this -> gate] = array_values($_SESSION[$this -> gate]);
                }
				
                @session_write_close();
        }

        /**
         * 回前n頁
         * @param integer $step 回幾頁
         */
        public function back($step = 1) {
                @session_start();
                $count = count($_SESSION[$this -> gate]);
                $index = ($count - 1) - $step;
                @session_write_close();

                $url = $this -> indexToUrl($index);
                $this -> page($url);
        }

        /**
         * 退回至符合指定關鍵字的網址
         * @param string $keyword 關鍵字
         */
        public function to($keyword) {
     
                $count = count($_SESSION[$this -> gate]);
                $index = -1;
                for ($i = $count - 1; $i >= 0; $i--) {
                        if (preg_match("/" . $keyword . "/i", $_SESSION[$this -> gate][$i])) {
                                $index = $i;
                                break;
                        }
                }

                $url = $this -> indexToUrl($index);
                $this -> page($url);
        }

        /**
         * 跳至指定標籤頁面(標籤頁面可在設定檔中進行設定)
         * @param string $symbol 標籤
         * @param array $param 參數
         */
        public function to_symbol($symbol, $params = array()) {
                switch($symbol) {
                        case "rows" :
                                $url = "list.php";
                                break;
                        case "add" :
                                $url = "add.php";
                                break;
                        case "fix" :
                                $url = "fix.php";
                                break;
                }

                if ($params != array()) {
                        $url .= "?" . http_build_query($params);
                }

                $this -> page($url);
        }

        /**
         * 根據索引取得陣列中的網址
         * @param integer $index 索引
         * @return string $url 網址
         */
        private function indexToUrl($index) {
                @session_start();
				$count = count($_SESSION[$this -> gate]);
                for ($i = $index + 1; $i < $count; $i++) {
                        unset($_SESSION[$this -> gate][$i]);
                }
				
				$url = ($index > -1) ? $_SESSION[$this -> gate][$index] : "";
                @session_write_close();
                return $url;
        }

        /**
         * 跳至指定網址
         * @param string $url 網址
         */
        public function page($url) {
        	if($url != ""){
                header("location: " . $url);
                exit ;
			}
			else{
				header("location: index.php");
			}
        }

}
?>