<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
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
        
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                

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
                                        	
                                        </div>
                                        
                                        <form method="post">
	                                    <div class="callout callout-info" style="margin-top:10px;">
											<h4 class="search-title"><a href="javascript:void(0);">搜尋條件</a></h4>
										
											<div class="form-group" style="display:none;">
												<label for="keyword">關鍵字</label>
												<input class="form-control" name="keyword" value="<?php echo $this -> module_io -> keyword; ?>" />
											</div>
										
				                                       
											<div class="input-group-btn" style="display:none;">
												<button class="btn btn-sm btn-default" type="submit" name="search"><i class="fa fa-search"> 搜尋</i></button>
											</div>
										</div>
                                        </form>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover">
                                    	
                                        <tr>
                                            <th style="width:50px;"><a href="javascript:void(0);" class="select_button label label-primary glyphicon glyphicon-ok" title="全選"><span></span></a></th>
                                            <th style="width:200px;">產品圖片</th>
                                            <th>產品名稱</th>
                                            <th>Title</th>
                                        </tr>
                                        <?php foreach($this -> products as $product): ?>
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" id="<?php echo $product->id; ?>" value="<?php echo $product->id; ?>"/></td>
                                            <td class="img"><img src="<?php echo $product -> cover -> get_image() -> get_file_path(); ?>" height="150"></td>
                                            <td class="title"><?php echo $product -> title -> get_value(); ?></td>
                                            <td class="en_title"><?php echo $product -> en_title -> get_value(); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                        <button type="button" name="select" class="btn btn-primary">選擇</button>　
                                        <button type="button" name="cancel" class="btn btn-default">取消</button>
                                </div>
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
        
        	$("button[name='cancel']").click(function(){
        		parent.$().colorbox.close();
        	});
        	
        	$("button[name='select']").click(function(){
        		
        		$("input[name='ids[]']:checked").each(function(){
        			var id = $(this).attr("value");
        			var title = $(this).parents("tr").children(".title").html();
        			var en_title = $(this).parents("tr").children(".en_title").html();
        			var img = $(this).parents("tr").children(".img").html();
        			var new_item = "";
	        		new_item += '<tr class="<?php echo $this -> module_io -> var; ?>_item">';
	                new_item += '  	<td style="width:30px;" valign="middle"><div style="padding-top:8px;" class="dropable glyphicon glyphicon-align-justify"></div></td>';
	        		new_item += '  	<td>' + title + '</td>';
	        		new_item += '  	<td>' + en_title + '</td>';
					new_item += '  	<td>' + img + '</td>';
	                new_item += '  	<td>';
	                new_item += '  		<button class="delete btn btn-danger glyphicon glyphicon-trash" title="刪除欄位" type="button"></button>';
	                new_item += '  		<input type="hidden" name="<?php echo $this -> module_io -> var; ?>_id[]" value="' + id + '" />';
	                new_item += '  	</td>';
					new_item += '</tr>';
        		
        			parent.$(".<?php echo $this -> module_io -> var; ?>_items").append(new_item);
        			parent.$(".delete").unbind("click");
					parent.$(".delete").click(function(){
						$(this).parents(".<?php echo $this -> module_io -> var; ?>_item").remove();  
					});	
        		});
        		
        		parent.$().colorbox.close();
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
    </body>
</html>
