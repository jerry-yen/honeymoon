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
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-xs-12">
                            <!-- general form elements -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title"></h3>
                                </div><!-- /.box-header -->
                                <!-- form start -->
                                <form method="post" enctype="multipart/form-data">
                                    <div class="box-body">
                                        
                                        <?php $in_group = false; ?>
                                        
                                        <?php foreach($this -> module -> class_fieldMetadata as $fieldMetadata): ?>
                                        
                                        
                                        <?php
                                        	if($this -> is_special_level){
                                        		$field = $this -> item_class -> {$fieldMetadata -> class_special_fieldMetadata_field_variable};
                                        	} 
											else{
												$field = $this -> item_class -> {$fieldMetadata -> class_fieldMetadata_field_variable};
											}
                                        ?>
                                        
                                        <?php
                                        if($field -> is_group_start()){
                                        	
                                        	if(!$in_group){
	                                        	ob_start();
												$in_group = true;
												$field -> init();
												$field -> create_tab();
											}
											else{
												$components = ob_get_contents();
												ob_end_clean();
												$field -> add_component($components);
												
												$field -> create_tab();
												ob_start();
											}
                                        }
										if($field -> is_group_end()){
											
											$components = ob_get_contents();
											ob_end_clean();
											$field -> add_component($components);
											$in_group = false;
											
										}
                                        ?>
                                        
                                        <div class="form-group">
                                        	
                                        	<?php if($field -> has_title()): ?>
                                        	
                                            <label for="<?php echo $field -> get_variable(); ?>">
                                            	<?php echo $field -> get_name(); ?>
                                            	
                                            	<?php if($field -> is_required()): ?>
                                            	<required>(必填)</required>
                                            	<?php endif; ?>
                                            	：
                                            </label>
                                            <?php endif; ?>
                                            
                                            
                                            <?php echo $field -> render(array("class" => "form-control")); ?>
                                        </div>
                                        
                                        <?php endforeach; ?>
                                    	
                                    </div><!-- /.box-body -->
									
                                    <div class="box-footer">
                                        <button type="submit" name="save" class="btn btn-primary">儲存</button>　
                                        <button type="button" name="cancel" class="btn btn-default" onclick="history.back(-1);">取消</button>
                                    </div>
                                </form>
                            </div><!-- /.box -->

                            

                       
                        </div><!--/.col (left) -->
                        <!-- right column -->
                        
                    </div>   <!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="js/AdminLTE/app.js" type="text/javascript"></script>
        
        <script src="js/jquery.blockUI.js" type="text/javascript"></script>
      	<script type="text/javascript">
      		$(document).ready(function(){
      			$("form").submit(function(){
      				$.blockUI({ 
					 	css: { 
				            border: 'none', 
				            padding: '15px', 
				            backgroundColor: '#000', 
				            '-webkit-border-radius': '10px', 
				            '-moz-border-radius': '10px', 
				            opacity: .5, 
				            color: '#fff'
			        	},
			        	message : "儲存中，請耐心等候....."
			        });
      			});
      		});
      	</script>
      	
      	<?php 
      		foreach($this -> module -> class_fieldMetadata as $fieldMetadata){
				if($this -> is_special_level){
      				$field = $this -> item_class -> {$fieldMetadata -> class_special_fieldMetadata_field_variable};
				}
				else{
					$field = $this -> item_class -> {$fieldMetadata -> class_fieldMetadata_field_variable};
				}
      			$field -> script();
      		} 
      	?>
      	
      	<?php echo $this -> module -> ext_code; ?>
    </body>
</html>
