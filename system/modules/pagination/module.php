<?php
/**
 * 分頁模組
 */
class Pagination extends Base_Module {
        /**
         * @var 每頁幾筆
         */
        protected $count_per_page = -1;

        /**
         * @var 目前第幾頁
         */
        protected $current_page = 0;

        /**
         * @var 總筆數
         */
        protected $total_count = 0;
        
        /**
         * @var 鎖
         */
        protected $lock = true;
        
        /**
         * 取得鎖狀態
         * @return string LOCK | UNLOCK
         */
        public function get_lock_status(){
                return $this -> lock?"LOCK":"UNLOCK";
        }

        /**
         * 鎖定
         * 主要統計頁數的SQL查詢需要解鎖，否則其他查詢結果可能會蓋過需要頁數統計的數字
         */
        public function lock(){
                $this -> lock = true;
        }
        
        /**
         * 解鎖
         * 主要統計頁數的SQL查詢需要解鎖，否則其他查詢結果可能會蓋過需要頁數統計的數字
         */
        public function unlock(){
                $this -> lock = false;
        }

        /**
         * 設定每頁幾筆
         * @param integer $count 筆數
         */
        public function set_count_per_page($count) {
                $this -> count_per_page = $count;
        }

        /**
         * 取得每頁幾筆
         * @return integer $count_per_page
         */
        public function get_count_per_page() {
                return $this -> count_per_page;
        }

        /**
         * 設定總筆數
         * @param integer $count 總筆數
         */
        public function set_total_count($count) {
                $this -> total_count = $count;
        }

        /**
         * 總頁數
         * @return integer $page_count
         */
        public function get_total_page() {
                //echo "total=" . $this -> total_count . "<br/>";
                 //echo "perpage=" . $this -> count_per_page . "<br/>";
                return ceil($this -> total_count / $this -> count_per_page);
        }

        /**
         * 取得頁碼
         * @return integer $page;
         */
        public function get_page() {
                $this -> current_page = isset($this -> controller -> module_io -> page) ? $this -> controller -> module_io -> page : 1;

                return $this -> current_page;
        }

        /**
         * 計算此頁是從第幾筆開始(資料庫使用)
         * @return integer $index
         */
        public function get_start_index() {
			
                $start_index = (($this -> get_page() - 1) * $this -> count_per_page);
                // 頁面起始筆數 如 超過總資料筆數
                if($start_index >= $this -> total_count && $this -> total_count > 0){
                        $this -> controller -> module_io -> page --;
                        $start_index = $this -> get_start_index();
                }
               
                return $start_index;
        }

        /**
         * 清除分頁資訊
         */
        public function clear() {
                $this -> total_count = 0;
                $this -> count_per_page = -1;
        }
        
        /**
         * 取得分頁條列
         */
        public function get_page_list(){

                $params = $this -> controller ->  module_io -> get_all_data();
                
                unset($params["page"]);
                
                $param = ( $params == array() ) ? "" : ("&" . http_build_query($params));
                
                $total_page = $this -> get_total_page();
                
                $page_info = array();
                
                for($i=1;$i<=$total_page;$i++){
                        $page_info[$i] = (object)array(
                                "current" => ($this -> current_page == $i),
                                "url" => "?page=" . $i . $param
                        );
                }
                
                return $page_info;
        }
        
        /**
         * 下一頁
         */
        public function get_next_page(){
                
                $params = $this -> controller ->  module_io -> get_all_data();
                
                if(isset($params["page"])){
                        $page = (int)$params["page"];
                }
                else{
                        $page = 1;
                }
                
                unset($params["page"]);
                $total_page = $this -> get_total_page(); 
                if( $total_page == $page || $total_page == 0){
                        return (object) array("has_next" => false);
                }
                
                $param = ( $params == array() ) ? "" : ("&" . http_build_query($params));
                
                return (object)array("has_next" => true, "url" => "?page=" . ($page + 1) . $param);
        }
        
        
        /**
         * 上一頁
         */
        public function get_previous_page(){
                
                $params = $this -> controller ->  module_io -> get_all_data();
                
                if(isset($params["page"])){
                        $page = (int)$params["page"];
                }
                else{
                        $page = 1;
                }
                
                unset($params["page"]);
                $total_page = $this -> get_total_page(); 
                if(1 == $page || $total_page == 0){
                        return (object) array("has_previous" => false);
                }
                
                $param = ( $params == array() ) ? "" : ("&" . http_build_query($params));
                
                return (object)array("has_previous" => true, "url" => "?page=" . ($page - 1) . $param);
        }

}
?>