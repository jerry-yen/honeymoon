<?php
/**
 * 檔案上傳模組
 */
class File extends Base_Module {

        
        /**
         * 將檔案從暫存區移到目的資料夾
         * @param string $tmp_name 暫存區路徑
         * @param string $target_path 目的資料夾路徑
         * @return boolean 成功|失敗
         */
        private function file_move_from_temp_to_target($tmp_name, $target_path) {
                $upload_dir = $this -> system_configs["full_upload_path"];

                // 產生上傳的實體路徑(建立資料夾)
                $oldmask = umask(0);
                $dirs = preg_split("/[\\\\\/]/", dirname($target_path));
                $dir_path = "";
                foreach ($dirs as $dir) {
					if ($dir != "") {
                    	$dir_path .= $dir . "/";
                        if (!file_exists($upload_dir . "/" . $dir_path)) {
                        	mkdir($upload_dir . "/" . $dir_path);
                        }
					}
                }
                umask($oldmask);
               
                // 將圖片從暫存區複製至指定資料夾
                return (move_uploaded_file($tmp_name, $upload_dir . "/" . $target_path));
        }
		
		private function file_update($file){
			// 沒有檔案
			if( $file -> error == 4 ){
				return "";
			}
			
			// 隨時產生檔案名稱
			$filepath = uniqid(rand(), true) . "." . $file -> ext;
			
			
			$dir = "/";
			
			// 依網域來區分檔案上傳區
			@session_start();
			if(isset($_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"])){
				$dir .= $_SESSION[$_SERVER["HTTP_HOST"] . "_DOMAIN_ID"] . "/";
			}
			@session_write_close();

			// 將檔案從上傳暫存區 複製到 指定目錄
			if($this -> file_move_from_temp_to_target($file -> tmp_name, $dir . $filepath)){
				return $dir . $filepath;
			}
			else{
				return "";
			}
			
		}

        /**
         * 上傳檔案
         * @param string $key 參數名稱
		 * @param Array $allow_type 允許的類型
         */
        public function upload($key, $allow_type = array()) {
        	
			// 是否存有檔案上傳
			if(isset($_FILES[$key])){
				
				$file = $_FILES[$key];
				$files = array();
				if(is_array($file["name"])){
					$count = count($file["name"]);
					
					for ($i = 0; $i < $count; $i++) {
                        $ext = strtolower(substr(strrchr($file["name"][$i], "."), 1));
                    	$files[$i] = (object) array("name" => $file["name"][$i], "type" => $file["type"][$i], "tmp_name" => $file["tmp_name"][$i], "error" => $file["error"][$i], "size" => $file["size"][$i], "ext" => $ext);
						
						// 篩選檔案類型
						if(count($allow_type) > 0){
							$this -> controller -> module_loader -> load("mime");
							$exts = $this -> controller -> module_mime -> get_extand($files[$i] -> type);
							$intersect = array_intersect($allow_type, $exts);
							if(count($intersect) == 0){
								continue;
							}
						}
						
						// 上傳檔案
						$files[$i] -> path = $this -> file_update($files[$i]);
                    }
				}
				else{
					$ext = strtolower(substr(strrchr($file["name"], "."), 1));
                    $files[0] = (object) array("name" => $file["name"], "type" => $file["type"], "tmp_name" => $file["tmp_name"], "error" => $file["error"], "size" => $file["size"], "ext" => $ext);
					 
					// 篩選檔案類型
					if(count($allow_type) > 0){
						$this -> controller -> module_loader -> load("mime");
						$exts = $this -> controller -> module_mime -> get_extand($files[$i] -> type);
						$intersect = array_intersect($allow_type, $exts);
						if(count($intersect) == 0){
							//continue;
						}
					}
					 
					 // 上傳檔案
					 $files[0] -> path = $this -> file_update($files[0]);
				}
				
				return $files;
			}
			
			return array();

        }

        /**
         * 檔案下載
         * @param string $path 真實檔案路徑
         * @param string $download_file_name 希望下載的檔案名稱
         */
        public function download($path, $download_file_name) {
                $path = $this -> system_configs["full_upload_path"] . "/" . $path;
                header("Content-type:application");
                header("Content-Disposition: attachment; filename=" . $download_file_name);
                readfile($url . str_replace("@", "", $path));
                exit ;
        }

}
?>