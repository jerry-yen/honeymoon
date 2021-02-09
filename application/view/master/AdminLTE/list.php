<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this -> module_metadata["title"]; ?> | 開發者管理平台</title>
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
                        <?php echo $this -> module_metadata["title"]; ?>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="list.php?mod=<?php echo $this -> module_io -> mod; ?>"><i class="fa fa-dashboard"></i> <?php echo $this -> module_metadata["title"]; ?></a></li>
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
                                        <div class="input-group">
                                        	<button class="btn btn-success glyphicon glyphicon-plus" title="新增分類" type="submit" name="add_module" ></button>&nbsp;
                                        	<button class="btn btn-danger glyphicon glyphicon-trash" title="批次刪除" type="submit" name="del_module" onclick="return confirm('是否刪除<?php echo $this -> module_metadata["title"]; ?>');"></button>&nbsp;
                                        </div>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover">
                                    	
                                        <tr>
                                            <th><a href="javascript:void(0);" class="select_button label label-primary glyphicon glyphicon-ok" title="全選"><span></span></a></th>
                                            <th>指令</th>
                                            <?php foreach($this -> list_fields as $field): ?>
                                            <th><?php echo $field["name"]; ?></th>
                                            <?php endforeach; ?>
                                            <th>建立時間</th>
                                        </tr>
                                        <?php foreach($this->module_objects as $module): ?>
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" id="<?php echo $module->id; ?>" value="<?php echo $module->id; ?>"/></td>
                                            <td>
                                            	<a href="javascript:void(0);" class="edit_button label label-primary glyphicon glyphicon-pencil" title="編輯" rel="<?php echo $module -> id; ?>"><span></span></a>
                                            	<a href="javascript:void(0);" class="delete_button label label-danger glyphicon glyphicon-trash" title="刪除" rel="<?php echo $module -> id; ?>"><span></span></a>
                                            </td>
                                            <?php foreach($this -> list_fields as $field): ?>
                                            <td><?php echo $module -> {$field["variable"]} -> get_title(); ?></td>
                                            <?php endforeach; ?>
                                            <td><?php echo $module -> createTime;?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            
                            <?php //include (__DIR__ . "/include/page.php");  ?>
                            
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
        	$(".edit_button").click(function(){
        		var id = $(this).attr("rel");
        		location.href='fix.php?mod=<?php echo $this -> module_io -> mod; ?>&id=' + id; 
        	});
        	
			$(".delete_button").click(function(){
				var onclick = $("button[name='del_module']").attr("onclick");
				$("button[name='del_module']").attr("onclick","");
				if(confirm('是否刪除<?php echo $this -> module_metadata["title"]; ?>')){
					var id = $(this).attr("rel");
					$("#" + id).attr("checked","checked");
					$("button[name='del_module']").click();
				}
				$("button[name='del_module']").attr("onclick",onclick); 
        	});
        	
        	$(".select_button").click(function(){
				if($(this).hasClass("checked")){
					$("input[name='ids[]']").attr("checked","");
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
        	
        </script>
    </body>
</html>
