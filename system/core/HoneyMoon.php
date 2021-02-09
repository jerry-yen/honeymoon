<?php
require_once ("HM_Controller.php");
require_once ("HM_Module.php");

class HoneyMoon {

        /**
         * @var 模組
         * @access private
         */
        private $modules = array();

        /**
         * @var 系統設定值
         * @access private
         */
        private $configs = array();

        /**
         * @var 控制器
         * @access private
         */

        private $controller = null;



        public function __construct() {
        		

                // 載入系統設定值
                require_once (dirname(dirname(__FILE__)) . "/config.php");
                $this -> configs = & $configs;
                
                // 指定使用者自訂路徑
                set_include_path(get_include_path() . PATH_SEPARATOR . $this -> configs["full_application_path"]);


				/*************************************************************
				 *                       Controller 載入
				 *************************************************************/

                 // 控制器路徑
                $controller_path = $this -> configs["full_controller_path"] . $this -> configs["system_relative_execute_php_path"];
				
                // 控制器檔案 不存在
                if (!file_exists($controller_path)) {
					$this -> controller = new UserController($this -> modules , $this -> configs);
                }
				// 存在
				else{
					require_once($controller_path);
					$class_name = ucfirst(basename($_SERVER['SCRIPT_FILENAME'],".php")) . "_Controller";
					$this -> controller = new $class_name($this -> modules , $this -> configs); 
                }

				/*************************************************************
				 *                       模組 載入
				 *************************************************************/
				 
				 
                // 載入模組
                foreach ($this -> configs["modules"] as $module) {
                	// 模組路徑
                	$module_path = $this -> configs["full_system_path"] . "/modules/" . $module . "/module.php"; 
					if(!file_exists($module_path)){
						continue;
					}
                	
					require_once ($this -> configs["full_system_path"] . "/modules/" . $module . "/module.php");

                    $class_name = ucfirst($module);

                    $this -> modules[$module] = new $class_name();

                    $this -> modules[$module] -> set_config($this -> configs);

                }
        }



        public function running() {



                // 逐一執行控制器流程

                

                // 載入模組
                $this -> controller -> init();

                

                // 身份驗證

                $this -> controller -> verification();

                

                // 自訂函式

                $this -> controller -> global_code();
				
				$this -> controller -> before_main();

                

                // 主程式

                $this -> controller -> main();

                

                // 載入樣版前

                $this -> controller -> before_load_view();

                

                // 載入樣版

                $this -> controller -> load_view();

                

                // 樣版載完

                $this -> controller -> after_load_view();

                

                unset($this -> controller);

        }



        public function __destruct() {

                foreach ($this -> modules as $key => $module) {

                        unset($this -> modules[$key]);

                }

        }



}
$honey = new HoneyMoon();
$honey -> running();
unset($honey);
exit;
?>