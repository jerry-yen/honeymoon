<?php

/**
 * 輸入/輸出模組
 */
class Io extends Base_Module {

		/**
		 * GET / POST 資料
		 */
        private $data = array();

        public function __get($name) {
                $value = (isset($this -> data[$name])) ? $this -> data[$name] : null;
                return $value;
        }

        public function __set($name, $value) {
                $this -> data[$name] = $value;
        }

        public function __isset($name) {
                $flag = isset($this -> data[$name]);
                return $flag;
        }

        public function __unset($name) {
                unset($this -> data[$name]);
        }

        /**
         * 取得 $_POST 值
         * @param string $name
         */
        public function post($name) {
                return $_POST[$name];
        }

        /**
         * 取得 $_GET 值
         * @param string $name
         */
        public function get($name) {
                return $_GET[$name];
        }

        /**
         * Controller 載入時所呼叫的函式
         */
        public function init() {
                $this -> refresh();
        }

        public function refresh() {
                $this -> data = array_merge($_POST, $_GET);
        }
        
        public function clear(){
                unset($this -> data);
        }
        
        public function get_all_data(){
                return $this -> data;
        }

}
?>