<?php
/**
 * 檔案上傳元件
 * @abstract
 * 元素：
 * count{n} : 可上傳 n個檔
 * allow{png,jpg}	: 可上傳檔案型態
 */
class FileMCE_Component extends Component{

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
                
                $files = $this -> item -> get_files($this -> variable);
				
				// -1 代表無限制數量
				$count = "-1";
				if(preg_match("/count{(.*?)};/", $this -> element, $element)){
						$count = (int)$element[1];
				}
                
                ob_start();
?>

<?php echo $tip; ?>
        <div class="platform_<?php echo $this -> variable; ?>">
        <?php foreach($files as $file): ?>
       	<div class="<?php echo $this -> variable; ?>-file-uploaded" file-name="<?php echo $file -> fileName; ?>" rel="<?php echo $file -> id;?>" style="background-color:#F0F0F0;padding:5px 10px;margin:3px 0px;">
       		<a href="javascript:void(0);" class="delete_button label label-danger glyphicon glyphicon-trash" title="刪除" rel="<?php echo $file -> id; ?>" style="margin-right:30px;"><span></span></a>
        	<a class="<?php echo $this -> variable; ?>_file" href="javascript:void(0);" style="width:50%;"><?php echo $file -> fileName; ?>.<?php echo $file -> ext; ?></a>
        	<input type="hidden" name="<?php echo $this -> variable; ?>_file_ids[]" value="I:<?php echo $file -> id;?>"/>
        </div>
        <?php endforeach; ?>
        </div>
        <?php if($count > 1 || $count == -1): ?>
        <div style="float:right;"><a href="javascript:void(0);" style="text-decoration: none;" id="<?php echo $this -> variable; ?>_plus" rel="<?php echo $this -> variable; ?>">[ + ] 新增檔案</a></div>
        <?php endif; ?>
        <div style="clear:both;"></div>
        
        
        <div class="file_<?php echo $this -> variable; ?>">
        	<?php if( count($files) < $count || $count == -1 ): ?>
        	<input id="<?php echo $this -> variable; ?>_<?php echo uniqid(); ?>" class="<?php echo $this -> variable; ?>_file" type="button" name="<?php echo $this -> variable; ?>[]" value="選擇檔案" style="display:block;margin-bottom:5px;"/>
        	<?php endif; ?>
        </div>
        
</div>

<?php
		$render = ob_get_contents();
		ob_end_clean();
		return $render;
	}

	public function set_value( $value ){
		try{
			$this -> upload();
			$this -> files_sort();
			$this -> delete();
		}
		catch(Exception $e){
			print_r($e);
		}
	}
	
	

	private function files_sort(){
		
		$controller = Base_Controller::get_instance();
		
		$ids_variable_name = $this -> variable . "_file_ids";
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
		$variable_name = $this -> variable . "_file_ids";
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
			$obj_file -> classType = "File";
			$obj_file -> createTime = date("Y-m-d H:i:s");
			$obj_file -> updateTime = date("Y-m-d H:i:s");
			$this -> item -> add_file($obj_file);
			unset($obj_file);
			
			$_POST[$variable_name][$key] = "I:" . $id;
			
			$controller -> module_io -> refresh();
			
		}

		
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

<link rel="stylesheet" type="text/css" href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/colorbox/colorbox.css">
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/colorbox/jquery.colorbox-min.js"></script>

<script>
	function show_mediabox(id){
		$.colorbox({	
			'width'		: 900,
			'height'	: 600,
			'iframe'	: true,
			'href'		: '<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/tinymce-4.2.5/plugins/fileman/index.html?integration=custom&field_id=' + id
		});
	}
	function file_selected(file,field_id){
		
		var new_object = '<div class="<?php echo $this -> variable; ?>-file-uploaded" file-name="' + file.name + '" rel="NEW" style="background-color:#F0F0F0;padding:5px 10px;margin:3px 0px;">';
       	new_object +=	'<a href="javascript:void(0);" class="delete_button label label-danger glyphicon glyphicon-trash" title="刪除" rel="NEW" style="margin-right:30px;"><span></span></a>';
        new_object +=	'<a class="<?php echo $this -> variable; ?>_file" href="javascript:void(0);" style="width:50%;">' + file.name + '</a>';
        new_object +=	'<input type="hidden" name="<?php echo $this -> variable; ?>_file_ids[]" value="P:' + file.fullPath + '"/>';
        new_object += '</div>';
 
		$(".platform_<?php echo $this -> variable; ?>").append(new_object);
		
	
		$(".delete_button").unbind("click");
		$(".delete_button").click(function(){
			delete_file(this);
		});
		
		$("#" + field_id).remove();
		
		$.colorbox.close();
	}
	function delete_file(obj){
		var me = $(obj).parents(".<?php echo $this -> variable; ?>-file-uploaded");
		
		var name = me.attr("file-name");
		if(confirm("是否刪除「" + name + "」\r\n為防止誤刪，系統將在按下修改鈕後會正式刪除檔案！")){
			var id = $(obj).attr("rel");
			$(".platform_<?php echo $this -> variable; ?>").append('<input type="hidden" name="delete_ids[]" value="' + id + '" />');
			me.remove();
			
			if( $(".<?php echo $this -> variable; ?>_file").length < <?php echo $count; ?> ){
						var tray = $(".file_<?php echo $this -> variable; ?>");
	                    tray.append("<input class=\"<?php echo $this -> variable; ?>_file\" id=\"<?php echo $this -> variable; ?>_" + guid() + "\" type=\"button\" name=\"<?php echo $this -> variable; ?>[]\" value=\"選擇檔案\" style=\"display:block;margin-bottom:5px;\"/>");
	                    $(".<?php echo $this -> variable; ?>_file").unbind("click");
	                    $(".<?php echo $this -> variable; ?>_file").click(function(){
							show_mediabox($(this).attr("id"));
						});
					}
					
					if($(".<?php echo $this -> variable; ?>_file").length < <?php echo $count?>){
						$("#<?php echo $this -> variable; ?>_plus").show();
					}
		}
	}
	function guid() {
	  function s4() {
	    return Math.floor((1 + Math.random()) * 0x10000)
	      .toString(16)
	      .substring(1);
	  }
	  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
	    s4() + '-' + s4() + s4() + s4();
	}
	$(document).ready(function(){

		// 新增上傳檔案
		$("#<?php echo $this -> variable; ?>_plus").click(function(){
			if($(".<?php echo $this -> variable; ?>_file").length >=<?php echo $count; ?> && <?php echo $count; ?>	!= -1){
				alert("<?php echo $this -> name; ?>只能上傳<?php echo $count; ?>個檔案");
				return false;
			}
			else{
				var tray = $(".file_<?php echo $this -> variable; ?>");
				tray.append("<input class=\"<?php echo $this -> variable; ?>_file\" id=\"<?php echo $this -> variable; ?>_" + guid() + "\" type=\"button\" name=\"<?php echo $this -> variable; ?>[]\" value=\"選擇檔案\" style=\"display:block;margin-bottom:5px;\"/>");
				
				$(".<?php echo $this -> variable; ?>_file").unbind("click");
				$(".<?php echo $this -> variable; ?>_file").click(function(){
					show_mediabox($(this).attr("id"));
				});
			}
		});
	
		$(".delete_button").click(function(){
			delete_file(this);
		});
		
		$(".<?php echo $this -> variable; ?>_file").click(function(){
			show_mediabox($(this).attr("id"));
		});
	
		// 檔案排序
		$(".platform_<?php echo $this -> variable; ?>").sortable();
	});
</script>
<?php
	}
}
?>