<?php
class Split_Image_Controller extends AdminController {	public function main() {				// 取得所有變數		$var = array();		$var["x1"] = $this -> module_io -> x1;		$var["y1"] = $this -> module_io -> y1;		$var["selection_width"] = $this -> module_io -> width;		$var["selection_height"] = $this -> module_io -> height;		$var["image_width"] = $this -> module_io -> image_width;		$var["image_height"] = $this -> module_io -> image_height;		$var["path"] = $this -> module_io -> path;				//print_r($var);		// 圖片路徑		$file_path = $this -> config_full_root_path . $var["path"];		// echo $file_path;				// 分析原始圖片大小		$source = imagecreatefromjpeg( $file_path );		$source_width = imagesx( $source );		$source_height = imagesy( $source );		// echo $source_width . "," . $source_height;				// 計算比例		$w_rate = $source_width / $var["image_width"];		$h_rate = $source_height / $var["image_height"];				$source_selection_width = $w_rate * $var["selection_width"];		$source_selection_height = $h_rate * $var["selection_height"];				$source_x = $w_rate * $var["x1"];		$source_y = $h_rate * $var["y1"];				 		// 新的畫布		$dst_image = imagecreatetruecolor($source_selection_width, $source_selection_height);		imagecopyresized($dst_image,$source,0,0,$source_x,$source_y,$source_selection_width,$source_selection_height,$source_selection_width,$source_selection_height);				// 路徑		// $paths = explode("/", $var["path"]);		$dir = str_replace("/files", "", dirname($var["path"]));		$file_name = trim(basename(" " . $var["path"]));		$new_path = $dir . "/" . "cut_" . uniqid(true). "_" . $file_name;		imagejpeg($dst_image, $this -> config_full_upload_path . $new_path);				echo "(" . json_encode(			array(				"fullPath" => $this -> config_machine_relative_full_upload_path . $new_path,				"name" => $file_name			)		) . ")";				 			}	}?>