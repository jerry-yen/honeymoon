<?php
require_once ("ImageResize.php");

/**
 * 圖片模組
 */
class Image extends Base_Module {
	
        /**
         * 壓縮圖片
         * @param string $imagePath 圖片路徑
         * @param integer $width 寬度
         * @param integer $height 高度
         */
        public function resize($imagePath, $width, $height, $mode = "resize") {

                $imagePath = $this -> get_full_path($imagePath);

                $resize = new ImageResize($imagePath);
				
                switch(true) {
                        case ($height == 0) :
                                $resize -> resizeImage($width, $height, "landscape");
                                break;
                        case ($width == 0) :
                                $resize -> resizeImage($width, $height, "portrait");
                                break;
						case ($mode == "crop") :
								$resize -> resizeImage($width, $height, "crop");
								break;
                        default :
                                $resize -> resizeImage($width, $height);
                                break;
                }

                $resize -> saveImage($this -> get_full_path($imagePath, $width, $height));

        }

        /**
         * 取得壓縮過後的絕對路徑
         * @param string $path 原路徑
         * @param integer $width 寬度
         * @param integer $height 高度
         * @return string 壓縮過後的絕對路徑
         */
        public function get_full_path($path, $width = 0, $height = 0) {
             
                // 將路徑轉換成完整絕對路徑
                if (isset($this -> system_configs["full_upload_path"])) {
                        $path = str_replace($this -> system_configs["full_upload_path"], "", $path);
                        $path = $this -> system_configs["full_upload_path"] . "/" . $path;
                }

                // 如無指定被壓縮的尺寸，則回傳原圖路徑
                if ($width == 0 && $height == 0) {
                        return $path;
                }

                $dir = dirname($path);
                $filename = basename($path);

                return $dir . "/" . $width . "x" . $height . "/" . $filename;
        }

        /**
         * 取得壓縮過後的相對路徑
         * @param string $path 原路徑
         * @param integer $width 寬度
         * @param integer $height 高度
         * @return string 壓縮過後的相對路徑
         */
        public function get_relative_path($path, $width = 0, $height = 0) {
                return str_replace($this -> system_configs["full_machine_path"], "", $this -> get_full_path($path, $width, $height));
        }
        
		/**
		 * 刪除圖片 & 壓縮過後的圖片
		 */
        public function delete($path, $width = 0, $height = 0){
                
                $path = $this -> get_full_path($path, $width, $height);
                
                if( file_exists( $path ) ){
                        unlink( $path );
                        
                        // 刪除完檔案後，直接刪除空目錄
                        while( $path != "." && $path != "\\"){
                                $path = dirname($path);
                                if($path == "." || $path == "\\") break;
                                $files = scandir($path);
                                if(count($files) > 2 || $path == $this -> system_configs["full_upload_path"]){ 
                                        break;
                                }
                                else{
                                        rmdir($path);
                                }
                                
                        }
                }
        }

}
?>