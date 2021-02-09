<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>春先生管理系統</title>
<link href="css/backyard.css" rel="stylesheet" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">

<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

//-->
</script>
</head>

<body onload="MM_preloadImages('<?php echo $this -> config_machine_relative_view_path; ?>/admin/AdminLTE/images/btn_login_f2.jpg')" style="vertical-align:middle;" class="login">
<div id="login">
<form name="Form1" id="Form1" method="post">
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><img src="images/LOGIN_01.jpg" /></td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top" background="images/LOGIN_lbg.jpg">
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="images/LOGIN_02.jpg" width="300" height="25" /></td>
          </tr>
          <tr>
            <td align="left" background="images/LOGIN_lbg.jpg"><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><img src="images/LOGIN_06.jpg" width="78" height="22" /></td>
                <td><input name="account" id="Member_Id" type="text" value="" class="field" /></td>
              </tr>
            </table>
              </td>
          </tr>
          <tr>
            <td><img src="images/LOGIN_03.jpg" width="300" height="26" /></td>
          </tr>
          <tr>
            <td align="left" background="images/LOGIN_lbg.jpg"><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><img src="images/LOGIN_06.jpg" width="78" height="22" /></td>
                <td><input name="password" id="Pass_Wd" type="password" value="" class="field" /></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td height="91" align="center" valign="middle" background="images/LOGIN_lbg.jpg">
            <button type="submit" id="submit" name="login" class="btn_img">
            <img name="btn_login" id="btn_login" src="images/btn_login.jpg" width="173" height="81" border="0" alt=""  onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('btn_login','','<?php echo $this -> config_machine_relative_view_path; ?>/admin/AdminLTE/images/btn_login_f2.jpg',1);" />
            </button>
            </td>
          </tr>
        </table>
      </td>
        <td><img src="images/LOGIN_05.jpg" width="333" height="200" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><img src="images/LOGIN_04.jpg" width="633" height="62" /></td>
  </tr>
</table>
</form>
</div>
</body>
</html>
