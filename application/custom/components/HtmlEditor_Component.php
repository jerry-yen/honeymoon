<?php
class HtmlEditor_Component extends Component {
	
	private static $include_scripts = false;
	
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
        $value = $this -> get_value();
		
		
        return "{$tip}<br/><textarea name=\"{$this -> variable}\" {$str_attribute}/>{$value}</textarea>{$invalid_string}";
		
	}
	
	public function get_title(){
		return strip_tags($this -> get_value());
	}
	
	public function script(){
		
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<script type="text/javascript" src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/tinymce-4.2.5/tinymce.min.js"></script>

<?php
		}
		
		$css_content = "";
		if(preg_match("/css{(.*?)};/is", $this -> element, $res)){
			$css_content = $res[1];
		}
?>
<script type="text/javascript">
tinymce.init({
    selector: "textarea[name='<?php echo $this -> variable; ?>']",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern jbimages"
    ],
    toolbar1: "insertfile undo redo | code | bold italic | alignleft aligncenter alignright alignjustify | link image jbimages | bullist numlist outdent indent | print media | forecolor backcolor emoticons fontsizeselect",
    file_browser_callback : RoxyFileBrowser,
    image_advtab: true,
    menubar : false,
    relative_urls : false,
    language : 'zh_TW',
    height : 300,
	forced_root_block : false,
	<?php if($css_content != ""): ?>
	content_css : "<?php echo $this -> config["machine_relative_root_path"] . $css_content; ?>",
	<?php endif; ?>
	fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt"
});

function RoxyFileBrowser(field_name, url, type, win) {
  var roxyFileman = '<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/tinymce-4.2.5/plugins/fileman/index.html';
  if (roxyFileman.indexOf("?") < 0) {     
    roxyFileman += "?type=" + type;   
  }
  else {
    roxyFileman += "&type=" + type;
  }
  roxyFileman += '&input=' + field_name + '&value=' + win.document.getElementById(field_name).value;
  if(tinyMCE.activeEditor.settings.language){
    roxyFileman += '&langCode=' + tinyMCE.activeEditor.settings.language;
  }

  tinyMCE.activeEditor.windowManager.open({
     file: roxyFileman,
     title: '多媒體中心',
     width: 600, 
     height: 500,
     resizable: "yes",
     plugins: "media",
     inline: "yes",
     close_previous: "no"  
  }, {     window: win,     input: field_name    });
  return false; 
}
</script>
<?php
}
}
?>