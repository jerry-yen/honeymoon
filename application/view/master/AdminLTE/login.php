<!DOCTYPE html>
<html class="bg-black">
    <head>
        <meta charset="UTF-8">
        <title>開發者登入 | 開發者管理平台</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="google" value="notranslate">
        
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />

		<script src="<?php echo $this -> config_machine_relative_jquery_lib_path; ?>/jquery.2.1.1.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">

        <div class="form-box" id="login-box">
        	<div class="alert alert-success alert-dismissable" style="margin-left:0px;display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            	<span id="alert"></span>
			</div>
					
            <div class="header">開發者管理平台<br />開發者登入</div>
            <form method="post">
                <div class="body bg-gray">
                    <div class="form-group">
                        <input type="text" name="account" class="form-control" placeholder="帳號"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="密碼"/>
                    </div>
                   
                    <!--       
                    <div class="form-group">
                        <input type="checkbox" name="remember_me"/> Remember me
                    </div>
                    -->
                </div>
                <div class="footer">                                                               
                    <button type="submit" name="login" class="btn bg-olive btn-block">登入</button>  
                    
                    <!-- <p><a href="#">I forgot my password</a></p> -->
                    
                    <!-- <a href="register.html" class="text-center">Register a new membership</a> -->
                </div>
            </form>
        </div>

        
    </body>
</html>