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
                   
                    <form method="post">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <div class="box-tools">
                                        <div class="input-group">
                                        	
                                        	<button class="btn btn-primary glyphicon glyphicon-ok" title="確定" type="submit" name="sort_item" ></button>&nbsp;
                                        	<button class="btn bg-orange glyphicon glyphicon-share-alt" title="回上一層" type="submit" name="return_level" ></button>&nbsp;
                                        	
                                        </div>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover">
                                    	
                                        <tr>
                                            <th>&nbsp;</th>
                                            <?php foreach($this -> list_fields as $field): ?>
                                            <th><?php echo $field -> fieldMetadata_field_name; ?></th>
                                            <?php endforeach; ?>
                                            <th>發佈時間</th>
                                        </tr>
                                        <tbody class="items">
                                        <?php foreach($this-> items as $item): ?>
                                        <tr>
                                            <td>
                                            	<div class="dropable glyphicon glyphicon-align-justify ui-sortable-handle"></div>
                                            	<input type="hidden" name="ids[]" value="<?php echo $item -> id; ?>"/>
                                            </td>
                                            
                                            <?php foreach($this -> list_fields as $field): ?>
                                            <td><?php echo $item -> {$field -> fieldMetadata_field_variable} -> get_title(); ?></td>
                                            <?php endforeach; ?>
                                            
                                            <td><?php echo $item -> createTime; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
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
        
        <link href="<?php echo $this -> config_machine_relative_jquery_lib_path; ?>/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
		<script src="<?php echo $this -> config_machine_relative_jquery_lib_path; ?>/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>

        <script type="text/javascript">
        
        	$(document).ready(function(){
        		$(".items").sortable({
					handle : ".dropable"
				});
        	});
        	
        </script>
        
        <?php echo $this -> module -> ext_code; ?>
    </body>
</html>
