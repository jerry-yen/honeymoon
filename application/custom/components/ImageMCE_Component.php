<?php
/**
 * 圖片上傳元件
 * @abstract
 * 元素：
 * count{n} : 可上傳 n張圖
 * resize{500x500,600x600}	: 壓縮 500x500 及 600x600 尺寸
 * allow{png,jpg}	: 可上傳檔案型態
 */
class ImageMCE_Component extends Component{

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
				// print_r($images);

				
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
			<input type="hidden" name="<?php echo $this -> variable; ?>_image_ids[]" value="I:<?php echo $image -> id; ?>"/>
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
		<input id="<?php echo $this -> variable; ?>_<?php echo uniqid(); ?>" class="<?php echo $this -> variable; ?>_image" type="button" name="<?php echo $this -> variable; ?>[]" value="選擇圖片"  style="display:block;margin-bottom:5px;"/>
		<?php endif; ?>
	</div>
	
</div>

<?php
		$render = ob_get_contents();
		ob_end_clean();
		return $render;
	}

	public function set_value( $value ){
		$this -> upload();
		$this -> images_sort();
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
			$id_params = explode(":", $id);
			if($id_params[0] != "I"){
				continue;
			}
			
			$file = $controller -> module_dao -> get_object("file", $id_params[1]);
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
		
		
		
		foreach($controller -> module_io -> delete_ids as $id){
			$file = $controller -> module_dao -> get_object("file", $id);

			if($file -> is_exists()){
		
				$file -> delete();
			}
		}

	}

	private function upload(){

		$controller = Base_Controller::get_instance();
		
		// 上傳檔案
		$variable_name = $this -> variable . "_image_ids";
		$files = $controller -> module_io -> {$variable_name};
		
		// 將檔案資訊儲存至資料庫
		foreach($files as $key => $file){
			$file_params = explode(":", $file);
			if($file_params[0] == "I"){
				continue;
			}

			$file_path = $file_params[1];
			$ext = strtolower(substr(strrchr($file_path, "."), 1));
			$file_dirs = explode("/", $file_path);
			$file_name = end($file_dirs);
			
			$obj_file = $controller -> module_dao -> get_object("file");
			$obj_file -> variable = $this -> variable;
			$obj_file -> path = str_replace("/files","",$file_path);
			$obj_file -> fileName = trim(basename(" ". $file_name , "." . $ext));
			$obj_file -> ext = $ext;
			$obj_file -> classType = "Image";
			$obj_file -> createTime = date("Y-m-d H:i:s");
			$obj_file -> updateTime = date("Y-m-d H:i:s");
			$id = $this -> item -> add_file($obj_file);
			unset($obj_file);
			
			$_POST[$variable_name][$key] = "I:" . $id;
			
			$controller -> module_io -> refresh();
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
				
				tray.append("<input class=\"<?php echo $this -> variable; ?>_image\" id=\"<?php echo $this -> variable; ?>_" + guid() + "\" type=\"button\" name=\"<?php echo $this -> variable; ?>[]\" value=\"選擇圖片\" style=\"display:block;margin-bottom:5px;\"/>");
				$(".<?php echo $this -> variable; ?>_image").unbind("click");
				$(".<?php echo $this -> variable; ?>_image").click(function(){
					show_mediabox($(this).attr("id"));
				});
			}
		});
	
		// 圖片排序
		$(".platform_<?php echo $this -> variable; ?>").sortable();
	
		// 刪除圖片圖示
		$(".<?php echo $this -> variable; ?>-image-uploaded").closebutton({
			show : "<img src='../images/icon_delete.png' width='25'/>",
			close : delete_image
		});
	
		$('.<?php echo $this -> variable; ?>-image-uploaded').nailthumb({width:150,height:150,method:'resize',fitDirection:'center center'});
		
		$(".<?php echo $this -> variable; ?>_group").colorbox({rel:'<?php echo $this -> variable; ?>_group'});
		
		$(".<?php echo $this -> variable; ?>_image").click(function(){
			show_mediabox($(this).attr("id"));
		});
	});
	
	function guid() {
	  function s4() {
	    return Math.floor((1 + Math.random()) * 0x10000)
	      .toString(16)
	      .substring(1);
	  }
	  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
	    s4() + '-' + s4() + s4() + s4();
	}
	
	function delete_image(me){
		var name = me.attr("file-name");
				if(confirm("是否刪除「" + name + "」\r\n為防止誤刪，系統將在按下修改鈕後會正式刪除圖片！")){
					
					var id = me.attr("rel");
					$(".platform_<?php echo $this -> variable; ?>").append('<input type="hidden" name="delete_ids[]" value="' + id + '" />');
					me.remove();
					
					if( $(".<?php echo $this -> variable; ?>_image").length < <?php echo $count; ?> ){
						var tray = $(".image_<?php echo $this -> variable; ?>");
	                    tray.append("<input class=\"<?php echo $this -> variable; ?>_image\" id=\"<?php echo $this -> variable; ?>_" + guid() + "\" type=\"button\" name=\"<?php echo $this -> variable; ?>[]\" value=\"選擇圖片\" style=\"display:block;margin-bottom:5px;\"/>");
	                    $(".<?php echo $this -> variable; ?>_image").unbind("click");
	                    $(".<?php echo $this -> variable; ?>_image").click(function(){
							show_mediabox($(this).attr("id"));
						});
					}
					
					if($(".<?php echo $this -> variable; ?>_image").length < <?php echo $count?>){
						$("#<?php echo $this -> variable; ?>_plus").show();
					}
				}
	}
	
	function show_mediabox(id){
		$.colorbox({	
			'width'		: 900,
			'height'	: 600,
			'iframe'	: true,
			'href'		: '<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/tinymce-4.2.5/plugins/fileman/index.html?integration=custom&field_id=' + id
		});
	}
	function file_selected(file,field_id){
		
		var new_object = '<div class="<?php echo $this -> variable; ?>-image-uploaded" file-name="' + file.name + '" data-group="<?php echo $this -> variable; ?>_group" data-src="' + file.fullPath + '" rel="NEW" style="float:left;">';
		new_object += '<img class="<?php echo $this -> variable; ?>_image" src=' + '"' + file.fullPath + '" width="150" height="150"/>';
		new_object += '<input type="hidden" name="<?php echo $this -> variable; ?>_image_ids[]" value="P:' + file.fullPath + '"/>';
		new_object += '</div>';
 
		$(".platform_<?php echo $this -> variable; ?>").append(new_object);
		
		$('.<?php echo $this -> variable; ?>-image-uploaded > * > img').unwrap(".nailthumb-loading, .nailthumb-container");
		$('.<?php echo $this -> variable; ?>-image-uploaded img').nailthumb({width:150,height:150,method:'resize',fitDirection:'center center'});
		
		$(".<?php echo $this -> variable; ?>-image-uploaded").closebutton({
			show : "<img src='../images/icon_delete.png' width='25'/>",
			close : delete_image
		});
		$(".<?php echo $this -> variable; ?>_group").colorbox({rel:'nofollow'});
		$(".<?php echo $this -> variable; ?>_group").colorbox({rel:'<?php echo $this -> variable; ?>_group'});
		
		$("#" + field_id).remove();
		
		$.colorbox.close();
	}
</script>
<?php
}
}
?>