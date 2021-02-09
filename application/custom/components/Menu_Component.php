<?php
class Menu_Component extends Component{
	
	private static $include_scripts = false;

    public function render($attributes = array()){
                
				// 驗證失敗呈現
				$invalid_string = "";
				if($this -> valid_error_message != ""){
					$attributes["class"] = (isset($attributes["class"])) ? ($attributes["class"] . " invalid") : "invalid";
					
					$invalid_string = "<span class='invalid_message'>{$this -> valid_error_message}</span>";
				}
				
				include($this -> config["full_application_path"] . "/custom/config/master.php");
				
				$module_action = $this -> controller -> module_dao -> get_object("module");
				
				
				
				$module_items = array();
				foreach($master_config["modules"] as $key => $module){
					$moduleType = $master_config["modules"][$key]["type"];
					$moduleTitle = $master_config["modules"][$key]["title"];
					if($moduleType != "Config"){
						$modules = $module_action -> get_modules($moduleType);
						
						foreach($modules as $module){
							$module_items[$module -> code] = $module -> title;
						}
					}
				}
				
				
				$select = "<select class='form-control input select hide'>";
				foreach($module_items as $key => $item){
					$select .= "<option value='{$key}'>{$item}</option>";
				}
				$select .="</select>";
				
                // 屬性
                $str_attribute = "";
                foreach($attributes as $name => $attr_value){
                        $str_attribute .= $name . '="' . $attr_value . '" ';
                }
                
                // 提示
                $tip = ($this -> tip != "") ? ("<span class=\"tip\">" . $this -> tip . "</span>") : "";
                $value = $this -> get_value();
				
							
				$menus = $value;
				
				if(is_null($menus) || $menus == ""){
					$menus = array();
				}
				
				if(!is_array($menus)){
					$menus = json_decode($menus);
				}
				
				$render = "";
				foreach($menus as $menu){
					$render .= $this -> load_menu($module_items, $menu);
				}
				
				$menu = '<div id="container">
							<div style="float:right;"><button class="btn btn-success glyphicon glyphicon-plus" title="新增選單" type="button" name="add_menu"></button></div>
							<br style="clear:both;" />
							' . $render . '
                         </div>
                         <input type="hidden" value="" name="' . $this -> variable . '" />
                         <div class="menu_source callout callout-info hide">
							<h4 style="float:right;">
								<button class="btn btn-success glyphicon glyphicon-plus" title="新增項目" type="button" name="add_item"></button>
								<button class="btn btn-danger glyphicon glyphicon-trash" title="刪除選單" type="button" name="del_menu" ></button>
							</h4>
	                        <h4 style="float:left;width:90%;"><input type="text" value="" class="form-control input text hide"/></h4>
	                        <br style="clear:both;" />
	                    	<div class="sortable"></div>
						</div>
						<div class="item_source callout callout-warning hide">
							<h4 style="float:right;">
								<button class="btn btn-danger glyphicon glyphicon-trash" title="刪除項目" type="button" name="del_item" ></button>
							</h4>
		                    <h4 style="float:left;width:90%;">' . $select . '</h4>
		                    <br style="clear:both;" />
						</div>
                         ';
				
                return "{$tip}<br/>{$menu}";
        }

		public function load_menu($module_items,$menu){
			
			$items = "";
			foreach($menu -> items as $item){
				$items .= $this -> load_items($module_items, $item);
			}
			
			$render ='<div class="callout callout-info">
							<h4 style="float:right;">
								<button class="btn btn-success glyphicon glyphicon-plus" title="新增項目" type="button" name="add_item"></button>
								<button class="btn btn-danger glyphicon glyphicon-trash" title="刪除選單" type="button" name="del_menu" ></button>
							</h4>
	                        <h4 style="float:left;width:90%;"><input type="text" value="' . $menu -> title . '" class="form-control input text"/></h4>
	                        <br style="clear:both;" />
	                    	<div class="sortable">' . $items . '</div>
						</div>';
			return $render;
		}

		public function load_items($module_items, $item_value){
			
			$select = "<select class='form-control input select'>";
			foreach($module_items as $key => $item){
				$selected = ($item_value == $key) ? "selected":"";
				$select .= "<option value='{$key}' {$selected}>{$item}</option>";
			}
			$select .="</select>";
			
			
			$render = '<div class="callout callout-warning">
							<h4 style="float:right;">
								<button class="btn btn-danger glyphicon glyphicon-trash" title="刪除項目" type="button" name="del_item" ></button>
							</h4>
		                    <h4 style="float:left;width:90%;">' . $select . '</h4>
		                    <br style="clear:both;" />
						</div>';
			return $render;
		}


		public function script(){
	
		
		if( !self::$include_scripts ){
               self::$include_scripts = true;
?>
<link href="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this -> config["machine_relative_jquery_lib_path"]; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
<style>
	.callout {
		padding: 5px 30px 5px 15px;
		margin: 0 0 10px 0;
		border-left: 15px solid #eee;
	}
</style>
<?php
		}

?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#container").sortable();
		$(".sortable").sortable();
	});
	
	$("button[name='add_menu']").click(function(){
		var menu = $(".menu_source").clone();
		menu.removeClass("menu_source");
		menu.removeClass("hide");
		$(".hide",menu).removeClass("hide");
		$("#container").append(menu);
		$("button[name='del_menu']").unbind("click");
		$("button[name='del_menu']").click(del_menu);
		
		$("button[name='add_item']").unbind("click");
		$("button[name='add_item']").click(add_item);
	});
		
	
	
	function add_item(){
		var item = $(".item_source").clone();
		item.removeClass("item_source");
		item.removeClass("hide");
		$(".hide",item).removeClass("hide");
		$(this).parents(".callout").children(".sortable").append(item);
		
		$("button[name='del_item']").unbind("click");
		$("button[name='del_item']").click(del_item);
		
		$(".sortable").sortable();
	}
	
	function del_menu(){
		$(this).parents(".callout").remove();
	}
	
	function del_item(){
		$(this).parents(".callout").eq(0).remove();
	}
	
	$("button[name='add_item']").click(add_item);
	
	$("button[name='del_item']").click(del_item);
	
	$("button[name='del_menu']").click(del_menu);
	
	$("button[name='save']").click(function(){
		var menus = new Array();
		var index = -1;
		$(".input").not(".hide").each(function(){
			if($(this).hasClass("text")){
				index ++;
				menus[index] = new Object();
				menus[index].title = $(this).val();
				menus[index].items = [];
			}
			if($(this).hasClass("select")){
				menus[index].items[menus[index].items.length] = $(this).val();
			}
		});
		
		$("input[name='<?php echo $this -> variable; ?>']").val(JSON.stringify(menus));
		
	});
</script>
<?php
	}
}
?>