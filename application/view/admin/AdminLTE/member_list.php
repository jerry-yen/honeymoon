<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this -> module -> title; ?> | <?php echo $this -> mod_login -> name; ?></title>
        <meta name="google" value="notranslate">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <link href="css/define.css" rel="stylesheet" type="text/css" />
        

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        
        <script src="<?php echo $this -> config_machine_relative_jquery_lib_path; ?>/jquery.2.1.1.min.js"></script>
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <?php include(__DIR__ . "/include/header.php"); ?>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <?php include(__DIR__ . "/include/menu.php"); ?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php echo $this -> module -> title; ?>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="list.php?mod=<?php echo $this -> module_io -> mod; ?>"><i class="fa fa-dashboard"></i> <?php echo $this -> module -> title; ?></a></li>
                        <?php foreach($this -> nav_class as $key => $class): ?>
                        <?php if($key == count($this -> nav_class) - 1 ): ?>
                        <li class="active"> <?php echo $class -> title -> get_title(); ?></li>
                        <?php else: ?>
                        <li><a href="list.php?mod=<?php echo $this -> module_io -> mod; ?>&id=<?php echo $class -> id; ?>"> <?php echo $class -> title -> get_title(); ?></a></li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                   <div class="alert alert-success alert-dismissable" style="margin-left:0px; display:none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <span id="alert"></span>
					</div>
                    <form method="post">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <div class="box-tools">
                                        <div class="input-group" style="width:100%;">
                                        	
                                        	
                                        	<?php if(!$this -> skip_command_field): ?>
                                        	
                                        	<?php if(in_array("Add",explode(":",$this -> module -> item_setting))) : ?>
                                        	<button class="btn btn-success glyphicon glyphicon glyphicon-file" title="新增項目" type="submit" name="add_item" ></button>&nbsp;
                                        	<?php endif; ?>
                                        	
                                        	<?php if(in_array("Delete",explode(":",$this -> module -> item_setting))) : ?>
                                        	<button class="btn btn-danger glyphicon glyphicon-trash" title="批次刪除" type="submit" name="delete" onclick="return confirm('是否刪除項目');"></button>&nbsp;
                                        	<?php endif; ?>
                                        	
                                        	<?php if(in_array("Publish",explode(":",$this -> module -> item_setting))) : ?>
                                        	<button class="btn bg-olive glyphicon glyphicon-cloud-upload" title="批次上架" type="submit" name="publish" ></button>&nbsp;
                                        	<button class="btn bg-purple glyphicon glyphicon-cloud-download" title="批次下架" type="submit" name="unpublish" ></button>&nbsp;
                                        	<?php endif; ?>
                                        	
                                        	
                                        	
                                        	<?php if(in_array("Sort",explode(":",$this -> module -> item_setting))) : ?>
                                        	<button class="btn btn-info glyphicon glyphicon-sort" title="排序" type="submit" name="sort_item" ></button>&nbsp;
                                        	<?php endif; ?>
                                        	
                                        	<?php if($this -> module_io -> id != ""): ?>
                                        	<button class="btn bg-orange glyphicon glyphicon-share-alt" title="回上一層" type="submit" name="return_level" ></button>&nbsp;
                                        	<?php endif; ?>
                                        	
                                        	<?php if($this -> module_io -> mod == "checkin"): ?>
                                        	<button class="btn bg-purple glyphicon glyphicon-time" title="我要打卡" type="submit" name="checkin"  style="float:right;margin:0px 5px;"> 我要打卡</button>&nbsp;
                                        	
                                        	<?php endif; ?>
                                        	
                                        	<?php endif; ?>
                                        	
                                        	
                                        	
                                        </div>
                                      
                                      	<?php 
                                      		// 是否需要批次鉤選
                                      		
                                      		$checked_show = true;
                                      		$item_setting = explode(":",$this -> module -> item_setting);
											if(!in_array("Publish",$item_setting) && !in_array("Delete",$item_setting)){
												$checked_show = false;
											}
                                      		
                                      	?>
                                      
                                      	<?php if($this -> module_io -> mod == "checkin"): ?>
                                      	<div class="callout callout-info" style="margin-top:10px;">
											<div class="form-group">
												<label for="month">月份查詢</label>
												<select name="month" id="month" class="form-control">
													<?php 
													
														$start_month = date("Y-m-d"); 
														for($i=0;$i<12;$i++): 
															$month = date("Y-m", strtotime($start_month . " -{$i} month"));
													?>
													<option value="<?php echo $month; ?>" <?php if($this -> module_io -> month == $month): ?>selected<?php endif; ?>><?php echo $month; ?></option>
													<?php 
														endfor; 
													?>
												</select>
											</div>
											<div class="input-group-btn">
												<button class="btn btn-sm btn-default" type="submit" name="search"><i class="fa fa-search"> 搜尋</i></button>
											</div>
										</div>
                                      	<?php endif; ?>
                                      
                                        <?php if(count($this -> search_fields) > 0): ?>
	                                    <div class="callout callout-info" style="margin-top:10px;">
											<h4 class="search-title"><a href="javascript:void(0);">搜尋條件</a></h4>
				                                        
											<?php foreach($this -> search_fields as $key => $component): ?>
											<div class="form-group" style="display:none;">
												<label for="<?php echo $component -> get_variable(); ?>"><?php echo $component -> get_name(); ?></label>
												<?php echo $component -> render(array("class" => "form-control")); ?>
											</div>
											<?php endforeach; ?>
				                                       
											<div class="input-group-btn" style="display:none;">
												<button class="btn btn-sm btn-default" type="submit" name="search"><i class="fa fa-search"> 搜尋</i></button>
											</div>
										</div>
                                        <?php endif; ?>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover">
                                    	
                                        <tr>
                                        	<?php if($checked_show): ?>
                                            <th><a href="javascript:void(0);" class="select_button label label-primary glyphicon glyphicon-ok" title="全選"><span></span></a></th>
                                            <?php endif; ?>
                                            
                                            <?php if(!$this -> skip_command_field): ?>
                                            <th>指令</th>
                                            <?php endif; ?>
                                            
                                            <?php foreach($this -> list_fields as $field): ?>
                                            <th><?php echo $field -> fieldMetadata_field_name; ?></th>
                                            <?php endforeach; ?>
                                            
                                            <?php if(!$this -> skip_createTime_field): ?>
                                            <th>發佈時間</th>
                                            <?php endif; ?>
                                        </tr>
                                        <?php foreach($this-> items as $item): ?>
                                        <tr>
                                        	<?php if($checked_show): ?>
                                            <td><input type="checkbox" name="ids[]" id="<?php echo $item->id; ?>" value="<?php echo $item->id; ?>"/></td>
                                            <?php endif; ?>
                                            
                                            <?php if(!$this -> skip_command_field): ?>
                                            <td>
                                            	
                                            	<?php if(in_array("View",explode(":",$this -> module -> item_setting))) : ?>
                                            	<a href="javascript:void(0);" class="view_button label label-success glyphicon glyphicon-eye-open" title="檢視" rel="<?php echo $item -> id; ?>"><span></span></a>
                                            	<?php endif; ?>
                                            	
                                            	<?php if(in_array("Fix",explode(":",$this -> module -> item_setting))) : ?>
                                            	<a href="javascript:void(0);" class="edit_button label label-primary glyphicon glyphicon-pencil" title="編輯" rel="<?php echo $item -> id; ?>"><span></span></a>
                                            	<?php endif; ?>
                                            	
                                            	<?php if(in_array("Clone",explode(":",$this -> module -> item_setting))) : ?>
                                            	<a href="javascript:void(0);" class="clone_button label bg-purple glyphicon glyphicon-new-window" title="複製" rel="<?php echo $item -> id; ?>"><span></span></a>
                                            	<?php endif; ?>
                                            	
                                            	<?php if(in_array("Delete",explode(":",$this -> module -> item_setting))) : ?>
                                            	<a href="javascript:void(0);" class="delete_button label label-danger glyphicon glyphicon-trash" title="刪除" rel="<?php echo $item -> id; ?>"><span></span></a>
                                            	<?php endif; ?>
                                            	
                                            </td>
                                            
                                            <?php endif; ?>
                                            
                                            <?php foreach($this -> list_fields as $field): ?>
                                            <td><?php echo $item -> {$field -> fieldMetadata_field_variable} -> get_title(); ?></td>
                                            <?php endforeach; ?>
                                            
                                            <?php if(!$this -> skip_createTime_field): ?>
                                            
                                            <?php if(is_object($item -> createTime)): ?>
                                            <td><?php echo $item -> createTime -> get_value(); ?></td>
                                            <?php else: ?>
                                            <td><?php echo $item -> createTime; ?></td>
                                            <?php endif; ?>
                                            
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            
                            <?php include (__DIR__ . "/include/page.php");  ?>
                            
                        </div>
                    </div>
                    </form>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="js/AdminLTE/app.js" type="text/javascript"></script>

        <script type="text/javascript">
        
        	$(".view_button").click(function(){
        		var id = $(this).attr("rel");
        		location.href='view_item.php?mod=<?php echo $this -> module_io -> mod; ?>&id=' + id; 
        	});
        
        	$(".edit_button").click(function(){
        		var id = $(this).attr("rel");
        		location.href='fix_item.php?mod=<?php echo $this -> module_io -> mod; ?>&id=' + id; 
        	});
        	
        	$(".clone_button").click(function(){
        		var id = $(this).attr("rel");
        		location.href='clone_item.php?mod=<?php echo $this -> module_io -> mod; ?>&id=' + id; 
        	});
        	
			$(".delete_button").click(function(){
				var onclick = $("button[name='delete']").attr("onclick");
				$("button[name='delete']").attr("onclick","");
				if(confirm('是否刪除項目')){
					var id = $(this).attr("rel");
					$("#" + id).attr("checked","checked");
					$("button[name='delete']").click();
				}
				$("button[name='delete']").attr("onclick",onclick); 
        	});
        	
        	$(".select_button").click(function(){
				if($(this).hasClass("checked")){
					$("input[name='ids[]']").removeAttr("checked");
					$(".icheckbox_minimal").removeClass("checked");
					$(this).removeClass("checked");
					$(this).removeClass("glyphicon-remove").addClass("glyphicon-ok");
					$(this).removeClass("label-danger").addClass("label-primary");
					$(this).attr("title","全選");
				}
				else{
					$("input[name='ids[]']").attr("checked","checked");
					$(".icheckbox_minimal").addClass("checked");
					$(this).addClass("checked");
					$(this).removeClass("glyphicon-ok").addClass("glyphicon-remove");
					$(this).removeClass("label-primary").addClass("label-danger");
					$(this).attr("title","取消");
				}
			});
			
			$(".search-title").click(function(){
				var callout = $(this).parents(".callout");
				if(callout.hasClass("show")){
					callout.removeClass("show");
					callout.children("div").hide();
				}
				else{
					callout.addClass("show");
					callout.children("div").show();
				}
			});
        	
        </script>
        
        <?php echo $this -> module -> ext_code; ?>
        <?php if($this -> module_io -> mod == "checkin"): ?>
        <?php if($this -> is_overtime): ?>
		<link rel='stylesheet' href='<?php echo $this -> config_machine_relative_jquery_lib_path; ?>/sweetalert/sweetalert.css' type='text/css' />
		<script src='<?php echo $this -> config_machine_relative_jquery_lib_path; ?>/sweetalert/sweetalert-dev.js'></script>
		<script>
			$(document).ready(function(){
				swal({
					showCancelButton: true,
					title: "您打卡的時間並非在上班時間\r\n是否申請加班？",
					text: "如放棄，系統將自動將您打卡時間調整為正常下班時間\r\n如申請加班，請填寫加班申請表",
					confirmButtonText: "我要申請加班",
					cancelButtonText: "我要放棄",
					closeOnConfirm: false,
  					closeOnCancel: false
				},
				function(isConfirm){
					if (isConfirm) {
						location.href='add_item.php?mod=overtime';
					} else {
						
						$.post( "api.php", { "abort_work_plus": "1", "type":"<?php echo $this -> overtimes[0]["type"]; ?>","item_id": "<?php echo $this -> last_item_id; ?>" })
						  .done(function( data ) {
						  	if(data == "success"){
						    	swal({title:"成功放棄", text:"系統已自動將您打卡時間調整為正常上下班時間", type:"error"},function(){
						    		location.href=location.href;
						    	});
						   	}
						});
						
						// swal("成功放棄", "系統已自動將您打卡時間調整為正常上下班時間", "error");
					}
				});
			});
		</script>
        <?php endif; ?>
        <?php endif; ?>
    </body>
</html>
