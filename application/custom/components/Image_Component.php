<?php
/**
 * 圖片上傳元件
 * @abstract
 * 元素：
 * count{n} : 可上傳 n張圖
 * resize{500x500,600x600}	: 壓縮 500x500 及 600x600 尺寸
 * allow{png,jpg}	: 可上傳檔案型態
 */
class Image_Component extends Component{

       public function render($attributes = array()) {
                
                // 驗證失敗呈現
				$invalid_string = "";
				if ($this -> valid_error_message != "") {
					$attributes["class"] = (isset($attributes["class"])) ? ($attributes["class"] . " invalid") : "invalid";
		
					$invalid_string = "<span class='invalid_message'>{$this -> valid_error_message}</span>";
				}
				
				// 屬性
				$str_attribute = "";
				foreach ($attributes as $name => $attr_value) {
					$str_attribute .= $name . '="' . $attr_value . '" ';
				}
                
                // 提示
				$tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span>") : "";
                
                $images = $this -> get_images();
                //print_r($images);
				
				// -1 代表無限制數量
				$count = "-1";
				if(preg_match("/count{(.*?)};/", $this -> element, $element)){
						$count = (int)$element[1];
				}
                
                ob_start();
?>

<?php echo $tip; ?>
<div>
	<div style="" class="platform_<?php echo $this -> variable; ?>">
		<?php foreach($images as $image): ?>
		<div class="<?php echo $this -> variable; ?>-image-uploaded" file-name="<?php echo $image -> fileName; ?>" data-group="<?php echo $this -> variable; ?>_group" data-src="<?php echo $image -> get_file_path(); ?>" rel="<?php echo $image -> id; ?>" style="float:left;">
			<img class="<?php echo $this -> variable; ?>_image" src="<?php echo $image -> get_file_path(150, 150); ?>" width="150" height="150"/>
			<input type="hidden" name="<?php echo $this -> variable; ?>_image_ids[]" value="<?php echo $image -> id; ?>"/>
		</div>
		<?php endforeach; ?>
	</div>
	<div style="clear:both;"></div>
	<?php if($count > 1 || $count == -1): ?>
	<div style="float:right;">
		<a href="javascript:void(0);" style="text-decoration: none;" id="<?php echo $this -> variable; ?>_plus" rel="<?php echo $this -> variable; ?>">[ + ] 新增圖片</a>
	</div>
	<?php endif; ?>
	
	<div class="image_<?php echo $this -> variable; ?>">
		<?php if( count($images) < $count || $count == -1): ?>
		<input  class="<?php echo $this -> variable; ?>_image" type="file" name="<?php echo $this -> variable; ?>[]" />
		<br/>
		<?php endif; ?>
	</div>
	
</div>

<?php
		$render = ob_get_contents();
		ob_end_clean();
		return $render;
	}

	public function set_value( $value ){
		$this -> images_sort();
		$this -> upload();
		$this -> delete();
	}
	
	public function get_title(){
		$file = $this -> get_image(); // $this -> item -> get_single_image($this -> variable);
		if($file -> path != ""){
			return "<img src=\"" . $file -> get_file_path(150,150) . "\" height=\"150\">";
		}
		return "";
	}

	private function images_sort(){
		
		$controller = Base_Controller::get_instance();
		
		$ids_variable_name = $this -> variable . "_image_ids";
		if(!is_array($controller -> module_io -> {$ids_variable_name})){
			return;
		}

		foreach($controller -> module_io -> {$ids_variable_name} as $key => $id){
			$file = $controller -> module_dao -> get_object("file", $id);
			$file -> sortTime = date("Y-m-d H:i:s");
			$file -> sequence = $key;
			$file -> update();
			unset($file);
		}
	}

	private function delete(){
		
		$controller = Base_Controller::get_instance();
		
		if(!is_array($controller -> module_io -> delete_ids)){
			return;
		}
		
		// 取得檢查元素的壓縮長寬
		$resize = "150x150,300x300";
		if(preg_match("/resize{(.*?)};/", $this -> element, $element)){
			$resize .= "," . $element[1];
		}
		$sizes = explode(",", $resize);
		
		foreach($controller -> module_io -> delete_ids as $id){
			$file = $controller -> module_dao -> get_object("file", $id);

			if($file -> is_exists()){
		
				foreach ($sizes as $size) {
					if (trim($size) == "") continue;
					$dim = explode("x", $size);
					$width = $dim[0];
					$height = $dim[1];
					$controller -> module_image -> delete($file -> path, $width, $height);
				}
	
				$controller -> module_image -> delete($file -> path);
	
				$file -> delete();
			}
		}

	}

	private function upload(){

		$controller = Base_Controller::get_instance();

		
		
		// 取得檢查元素的壓縮長寬
		$resize = "150x150,300x300";
		if(preg_match("/resize{(.*?)};/", $this -> element, $element)){
			$resize .= "," . $element[1];
		}
		
		// 篩選檔案類型
		$allow_types = array();
		if(preg_match("/allow{(.*?)};/", $this -> element, $element)){
			$allow_types = explode(",",$element[1]);
		}
		
		// 上傳檔案
		$files = $controller -> module_file -> upload($this -> variable, $allow_types);

		// 將檔案資訊儲存至資料庫
		foreach($files as $file){
			if($file -> error == 4){
				continue;
			}

			$obj_file = $controller -> module_dao -> get_object("file");
			$obj_file -> variable = $this -> variable;
			$obj_file -> path = $file -> path;
			$obj_file -> fileName = basename($file -> name , "." . $file -> ext);
			$obj_file -> ext = $file -> ext;
			$obj_file -> classType = "Image";
			$obj_file -> resize = $resize;
			$obj_file -> createTime = date("Y-m-d H:i:s");
			$obj_file -> updateTime = date("Y-m-d H:i:s");
			$this -> item -> add_file($obj_file);
			unset($obj_file);
			
			
			// 壓縮檔案
            $sizes = explode(",",$resize);
            foreach($sizes as $size){
        		if(trim($size) == "")continue;
                $wh = explode("x",$size);
                $width = $wh[0];
                $height = $wh[1];
				$controller -> module_image -> resize($file -> path, $width, $height);        
			}
		}
		
		
	}

	public function get_images(){
		
		$controller = Base_Controller::get_instance();
		$obj_file = $controller -> module_dao -> get_object("file");
		
		$where[] = "itemId=?";
		$values[] = $this -> item -> id;
		
		$where[] = "classType=?";
		$values[] = "Image";
		
		$where[] = "variable=?";
		$values[] = $this ->variable;
		
		return $obj_file -> get_objects("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"),$this -> module);
	}
	
	public function get_image(){
		
		$controller = Base_Controller::get_instance();
		$obj_file = $controller -> module_dao -> get_object("file");
		
		$where[] = "itemId=?";
		$values[] = $this -> item -> id;
		
		$where[] = "classType=?";
		$values[] = "Image";
		
		$where[] = "variable=?";
		$values[] = $this ->variable;
		
		$images = $obj_file -> get_objects("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"),$this -> module);
		
		$noimage = "";
		if(preg_match("/noimage{(.*?)};/", $this -> element, $element)){
			$noimage = $element[1];
		}
		
		if(count($images) == 0){
			$obj_file -> path = $noimage;
		}
		
		return (count($images) > 0) ? $images[0] : $obj_file; 
	}

	public function script(){

		// -1 代表無限制數量
		$count = "-1";
		if(preg_match("/count{(.*?)};/", $this -> element, $element)){
			$count = (int)$element[1];
		}
?>

<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery.close.button.wei.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/colorbox/colorbox.css">
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/colorbox/jquery.colorbox-min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/nailthumb/jquery.nailthumb.1.1.min.css">
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/nailthumb/jquery.nailthumb.1.1.min.js"></script>

<script>
	$(document).ready(function(){

		// 新增上傳圖片
		$("#<?php echo $this -> variable; ?>_plus").click(function(){
			if($(".<?php echo $this -> variable; ?>_image").length >=<?php echo $count?> && <?php echo $count?>	!= -1){
				alert("<?php echo $this -> name; ?>只能上傳<?php echo $count; ?>張圖");
				return false;
			}
			else{
				var tray = $(".image_<?php echo $this -> variable; ?>");
				tray.append("<input class=\"<?php echo $this -> variable; ?>_image\" type=\"file\" name=\"<?php echo $this -> variable; ?>[]\" /><br />");
			}
		});
	
		// 圖片排序
		$(".platform_<?php echo $this -> variable; ?>").sortable();
	
		// 刪除圖片圖示
		$(".<?php echo $this -> variable; ?>-image-uploaded").closebutton({
			show : "<img src='../images/icon_delete.png' width='25'/>",
			close : function(me){
				var name = me.attr("file-name");
				if(confirm("是否刪除「" + name + "」\r\n為防止誤刪，系統將在按下修改鈕後會正式刪除圖片！")){
					
					var id = me.attr("rel");
					$(".platform_<?php echo $this -> variable; ?>").append('<input type="hidden" name="delete_ids[]" value="' + id + '" />');
					me.remove();
					
					if( $(".<?php echo $this -> variable; ?>_image").length < <?php echo $count; ?> ){
						var tray = $(".image_<?php echo $this -> variable; ?>");
	                    tray.append("<input class=\"<?php echo $this -> variable; ?>_image\" type=\"file\" name=\"<?php echo $this -> variable; ?>[]\" /><br />");
					}
					
					if($(".<?php echo $this -> variable; ?>_image").length < <?php echo $count?>){
						$("#<?php echo $this -> variable; ?>_plus").show();
					}
				}
			}
		});
	
		$('.<?php echo $this -> variable; ?>-image-uploaded').nailthumb({width:150,height:150,method:'resize',fitDirection:'center center'});
		
		$(".<?php echo $this -> variable; ?>_group").colorbox({rel:'<?php echo $this -> variable; ?>_group'});
	});
</script>
<?php
}
}
?>