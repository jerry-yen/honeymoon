<?php
class File_Component extends Component{

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

				$variable_name = $this -> variable;

                $files = $this -> item -> {$variable_name} -> get_files();
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
				<a class="file_<?php echo $this -> variable; ?>" href="javascript:void(0);" style="width:50%;"><?php echo $file -> fileName; ?>.<?php echo $file -> ext; ?></a>
				<input type="hidden" name="<?php echo $this -> variable; ?>_file_ids[]" value="<?php echo $file -> id;?>"/>
			</div>
			<?php endforeach; ?>
			
			<?php if($count > 1 || $count == -1): ?>
			<div style="float:right;"><a href="javascript:void(0);" style="text-decoration: none;" id="<?php echo $this -> variable; ?>_plus" rel="<?php echo $this -> variable; ?>">[ + ] 新增檔案</a></div>
			<?php endif; ?>
		
			<?php if( count($files) < $count || $count == -1 ): ?>
			<div class="file_<?php echo $this -> variable; ?>">
				<input class="<?php echo $this -> variable; ?>_file" type="file" name="<?php echo $this -> variable; ?>[]" multiple/><br/>
			</div>
			<?php endif; ?>
			<div style="clear:both;"></div>
	</div>

<?php
		$render = ob_get_contents();
		ob_end_clean();
		return $render;
	}

	public function set_value( $value ){
		try{
			$this -> files_sort();
			$this -> upload();
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
					$wh = explode("x", $size);
					$width = $wh[0];
					$height = $wh[1];
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
			$obj_file -> fileName = trim(basename(" " . $file -> name , "." . $file -> ext));
			$obj_file -> ext = $file -> ext;
			$obj_file -> classType = "File";
			$obj_file -> createTime = date("Y-m-d H:i:s");
			$obj_file -> updateTime = date("Y-m-d H:i:s");
			$this -> item -> add_file($obj_file);
			unset($obj_file);
			
		}

		
	}

	public function get_files(){
		
		$controller = Base_Controller::get_instance();
		$obj_file = $controller -> module_dao -> get_object("file");
		
		$where[] = "itemId=?";
		$values[] = $this -> item -> id;
		
		$where[] = "classType=?";
		$values[] = "File";
		
		$where[] = "variable=?";
		$values[] = $this ->variable;
		
		return $obj_file -> get_objects("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"),$this -> module);
	}
	
	public function get_file(){
		
		$controller = Base_Controller::get_instance();
		$obj_file = $controller -> module_dao -> get_object("file");
		
		$where[] = "itemId=?";
		$values[] = $this -> item -> id;
		
		$where[] = "classType=?";
		$values[] = "File";
		
		$where[] = "variable=?";
		$values[] = $this ->variable;
		
		$files = $obj_file -> get_objects("file", $where, $values, array("sortTime ASC", "sequence ASC", "createTime ASC"),$this -> module);
		
		$noimage = "";
		if(preg_match("/noimage{(.*?)};/", $this -> element, $element)){
			$noimage = $element[1];
		}
		
		if(count($files) == 0){
			$obj_file -> path = $noimage;
		}
		
		return (count($files) > 0) ? $files[0] : $obj_file; 
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

<script>
	$(document).ready(function(){

		// 新增上傳檔案
		$("#<?php echo $this -> variable; ?>_plus").click(function(){
			if($(".<?php echo $this -> variable; ?>_file").length >=<?php echo $count; ?> && <?php echo $count; ?>	!= -1){
				alert("<?php echo $this -> name; ?>只能上傳<?php echo $count; ?>個檔案");
				return false;
			}
			else{
				var tray = $(".file_<?php echo $this -> variable; ?>");
				tray.append("<input class=\"<?php echo $this -> variable; ?>_file\" type=\"file\" name=\"<?php echo $this -> variable; ?>[]\" multiple /><br/>");
			}
		});
	
		$(".delete_button").click(function(){
			var me = $(this).parents(".<?php echo $this -> variable; ?>-file-uploaded");
			var name = me.attr("file-name");
			if(confirm("是否刪除「" + name + "」\r\n為防止誤刪，系統將在按下修改鈕後會正式刪除檔案！")){
				var id = $(this).attr("rel");
				$(".platform_<?php echo $this -> variable; ?>").append('<input type="hidden" name="delete_ids[]" value="' + id + '" />');
				me.remove();
			}
		});
	
		// 檔案排序
		$(".platform_<?php echo $this -> variable; ?>").sortable();
	});
</script>
<?php
	}
}